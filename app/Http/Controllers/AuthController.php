<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use App\Notifications\EmailVerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    private const SCHOOL_OPTIONS = [
        'Davao del Norte State College',
        'Colegio de Panabo',
        'University of Mindanao Tagum College',
        'A Mabini College',
        'Davao Oriental State University - Panabo Extension',
        'Sto. Nino College',
        'New Corella College',
        'Panabo City National High School',
        'Panabo City National High School - Senior High',
        'Panabo City Montessori School',
        'Other Panabo School',
    ];

    public function login(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('user_id', $request->user_id)->first();
        if (! $user) {
            $user = User::where('email', $request->user_id)
                ->orderByDesc('created_at')
                ->first();
        }

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['login_error' => 'Invalid Library ID or Password.']);
        }

        if ($user->role === 'researcher' && ($user->status ?? 'approved') !== 'approved') {
            return back()->withErrors(['login_error' => 'Your researcher account is still pending staff verification.']);
        }

        if (! in_array($user->role, ['admin', 'staff'], true)
            && in_array($user->role, ['student', 'researcher', 'visitor'], true)
            && empty($user->email_verified_at)) {
            $verificationCode = (string) random_int(100000, 999999);
            $user->email_verification_code = $verificationCode;
            $user->email_verification_expires_at = now()->addMinutes(15);
            $user->save();

            try {
                $user->notify(new EmailVerificationCode($verificationCode));
            } catch (\Exception $exception) {
                logger()->error('OTP email failed to send: ' . $exception->getMessage());
                return redirect()->route('email.verify.form')
                    ->with('warning', 'Verification email could not be sent. Please check your mail settings or see server logs.');
            }

            auth()->login($user);

            return redirect()->route('email.verify.form')->with('warning', 'Your email is not verified yet. We sent a new 6-digit code to your email address.');
        }

        auth()->login($user);
        $request->session()->regenerate();

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'staff' => redirect()->route('staff.dashboard'),
            'researcher' => redirect()->route('researcher.dashboard'),
            'visitor' => redirect()->route('visitor.dashboard'),
            default => redirect()->route('student.dashboard'),
        };
    }

    public function ajaxLogin(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('user_id', $request->user_id)->first();
        if (! $user) {
            $user = User::where('email', $request->user_id)
                ->orderByDesc('created_at')
                ->first();
        }

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid Library ID or Password.'], 422);
        }

        if ($user->role === 'researcher' && ($user->status ?? 'approved') !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Your researcher account is still pending staff verification.'], 403);
        }

        if (! in_array($user->role, ['admin', 'staff'], true)
            && in_array($user->role, ['student', 'researcher', 'visitor'], true)
            && empty($user->email_verified_at)) {
            $verificationCode = (string) random_int(100000, 999999);
            $user->email_verification_code = $verificationCode;
            $user->email_verification_expires_at = now()->addMinutes(15);
            $user->save();

            try {
                $user->notify(new EmailVerificationCode($verificationCode));
            } catch (\Exception $exception) {
                logger()->error('OTP email failed to send via AJAX login: ' . $exception->getMessage());
                return response()->json(['success' => false, 'message' => 'Verification email could not be sent. Please check your mail settings.'], 500);
            }

            auth()->login($user);

            return response()->json(['success' => true, 'message' => 'Please verify your email before continuing.'], 202);
        }

        auth()->login($user);

        return response()->json(['success' => true, 'message' => 'Logged in successfully.']);
    }

    public function register(Request $request)
    {
        $schoolOptions = $this->schoolOptions();
        $request->merge(['school_options' => $schoolOptions]);
        if (! $request->filled('school') && $request->filled('selected_school')) {
            $request->merge(['school' => $request->input('selected_school')]);
        }

        $role = strtolower($request->input('role', 'student'));

        $rules = [
            'name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s\.\'\-]+$/'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->where(fn ($query) => $query->where('role', $role))],
            'password' => ['required', 'string', 'regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*(),.?"{}|<>]).{8,}$/', 'confirmed'],
            'role' => ['required', Rule::in(['student', 'researcher', 'visitor'])],
            'birthdate' => ['required', 'date'],
            'age' => ['required', 'integer', 'min:1', 'max:120'],
            'profile_picture' => ['required', 'image', 'max:4096'],
            'school_id' => ['nullable', 'string', 'max:255'],
        ];

        if ($role === 'visitor') {
            $rules['user_id'] = ['nullable', 'string', 'max:100'];
            $rules['school'] = ['nullable', 'string', 'max:255'];
            $rules['gender'] = ['required', Rule::in(['Male', 'Female', 'Non-binary', 'Prefer not to say'])];
        } elseif ($role === 'researcher') {
            $rules['user_id'] = ['nullable', 'string', 'max:100'];
            $rules['school'] = ['required', 'string', 'max:255'];
            $rules['gender'] = ['required', Rule::in(['Male', 'Female', 'Non-binary', 'Prefer not to say'])];
        } else {
            $rules['user_id'] = ['required', 'string', 'max:100', 'unique:users,user_id'];
            $rules['school'] = ['required', 'string', 'max:255'];
            $rules['gender'] = ['required', Rule::in(['Male', 'Female', 'Non-binary', 'Prefer not to say'])];
        }

        $validated = $request->validate($rules);

        $profilePicturePath = $request->file('profile_picture')->store('profiles', 'public');

        $userId = $validated['user_id'] ?? null;
        if ($role === 'visitor' && empty($userId)) {
            $userId = 'VIS-' . strtoupper(uniqid());
        }

        if ($role === 'researcher' && empty($userId)) {
            $userId = 'RES-' . strtoupper(uniqid());
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'user_id' => $userId,
            'barcode_id' => Member::generateUniqueBarcodeId(),
            'password' => Hash::make($validated['password']),
            'role' => $role,
            'school' => $validated['school'] ?? ($role === 'visitor' ? 'Visitor' : null),
            'school_id' => $validated['school_id'] ?? null,
            'profile_picture' => $profilePicturePath,
            'birthdate' => $validated['birthdate'],
            'age' => $validated['age'],
            'gender' => $validated['gender'] ?? ($role === 'visitor' ? 'Prefer not to say' : null),
            'status' => 'pending',
            'email_verified_at' => null,
            'email_verification_code' => (string) random_int(100000, 999999),
            'email_verification_expires_at' => now()->addMinutes(15),
        ]);

        try {
            $user->notify(new EmailVerificationCode($user->email_verification_code));
        } catch (\Exception $exception) {
            logger()->error('OTP email failed to send during registration: ' . $exception->getMessage());
            return redirect()->route('login')
                ->with('warning', 'Your account was created, but the verification email could not be sent. Please check your mail settings.');
        }

        auth()->login($user);

        return redirect()->route('email.verify.form')->with('success', 'Registration complete. Please verify your email to continue.');
    }

    public function showEmailVerificationForm()
    {
        return view('email-verify');
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = auth()->user();
        if (! $user) {
            return redirect()->route('login')->withErrors(['verification_error' => 'Please sign in to verify your email.']);
        }

        if ($user->email_verified_at) {
            return redirect()->route(match ($user->role) {
                'admin' => 'admin.dashboard',
                'staff' => 'staff.dashboard',
                'researcher' => 'researcher.dashboard',
                'visitor' => 'visitor.dashboard',
                default => 'student.dashboard',
            })->with('success', 'Your email is already verified.');
        }

        if ($user->email_verification_code !== $request->input('code') || now()->greaterThan($user->email_verification_expires_at)) {
            return back()->withErrors(['verification_error' => 'That code is invalid or has expired.']);
        }

        $user->email_verified_at = now();
        $user->email_verification_code = null;
        $user->email_verification_expires_at = null;

        if (in_array($user->role, ['student', 'researcher', 'visitor'], true) && ($user->status ?? '') !== 'approved') {
            $user->status = 'approved';
            $user->rejection_reason = null;
        }

        $user->save();

        return redirect()->route(match ($user->role) {
            'admin' => 'admin.dashboard',
            'staff' => 'staff.dashboard',
            'researcher' => 'researcher.dashboard',
            'visitor' => 'visitor.dashboard',
            default => 'student.dashboard',
        })->with('success', 'Email verified successfully.');
    }

    public static function schoolOptions(): array
    {
        if (Schema::hasTable('school_options')) {
            $values = DB::table('school_options')->pluck('name')->filter()->map(fn ($value) => trim((string) $value))->unique()->values()->all();
            if (! empty($values)) {
                return $values;
            }
        }

        return self::SCHOOL_OPTIONS;
    }
}
