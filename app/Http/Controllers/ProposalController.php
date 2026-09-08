<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\ProposalStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class ProposalController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'budget' => ['required', 'numeric', 'min:1', 'max:999999999.99'],
            'deadline' => ['required', 'date'],
            'requirements' => ['nullable', 'string'],
            'app_form' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
            'application_form' => ['required_without:combined_requirements_pdf', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
            'cv_file' => ['required_without:combined_requirements_pdf', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
            'budget_sheet' => ['required_without:combined_requirements_pdf', 'file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:5120'],
            'proposal_supporting_docs' => ['required_without:combined_requirements_pdf', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
            'combined_requirements_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'advisory_cert' => ['required_without:combined_requirements_pdf', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
            'id_photocopy' => ['required_without:combined_requirements_pdf', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'institution_id' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'lgu_cert' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $proposalId = DB::table('proposals')->insertGetId([
            'user_id' => auth()->user()->user_id ?? auth()->user()->email,
            'title' => $validated['title'],
            'budget' => $validated['budget'],
            'approved_grant' => 0,
            'requirements_file' => null,
            'requirements' => $validated['requirements'] ?? 'Checklist attachments submitted.',
            'deadline' => $validated['deadline'],
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $primaryStoredName = null;
        $uploadDir = public_path('uploads/proposals');
        if (! File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true);
        }

        foreach (['app_form', 'application_form', 'cv_file', 'budget_sheet', 'proposal_supporting_docs', 'combined_requirements_pdf', 'advisory_cert', 'id_photocopy', 'institution_id', 'lgu_cert'] as $field) {
            if (! $request->hasFile($field)) {
                continue;
            }

            $file = $request->file($field);
            $size = $file->getSize() ?: 0;
            $storedName = $proposalId.'_'.$field.'_'.time().'_'.$file->getClientOriginalName();
            $file->move($uploadDir, $storedName);
            $primaryStoredName = $field === 'app_form' ? $storedName : $primaryStoredName;

            DB::table('proposal_documents')->insert([
                'proposal_id' => $proposalId,
                'document_type' => $field,
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $storedName,
                'mime_type' => $file->getClientMimeType(),
                'size' => $size,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('proposals')->where('id', $proposalId)->update([
            'requirements_file' => $primaryStoredName,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Complete application transmitted successfully. Your proposal is now pending review.');
    }
 public function viewDocument($filename)
    {
        // adjust disk/path if you store elsewhere (here assumes storage/app/public/proposals/)
        $disk = Storage::disk('public');
        $relative = 'proposals/' . basename($filename);

        if (! $disk->exists($relative)) {
            abort(404);
        }
  $path = $disk->path($relative);
        $mime = mime_content_type($path) ?: 'application/octet-stream';

        // response()->file sends Content-Disposition: inline so browser will try to view
        return response()->file($path, ['Content-Type' => $mime]);
    }

    public function updateStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Approved', 'Rejected'])],
            'feedback' => ['nullable', 'string'],
            'approved_grant' => ['nullable', 'numeric', 'min:0'],
        ]);

        $proposal = DB::table('proposals')->where('id', $id)->first();
        abort_if(! $proposal, 404);

        DB::table('proposals')->where('id', $id)->update([
            'status' => $validated['status'],
            'feedback' => $validated['feedback'] ?? null,
            'approved_grant' => $validated['status'] === 'Approved'
                ? ($validated['approved_grant'] ?? $proposal->budget)
                : 0,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'updated_at' => now(),
        ]);

        // Send notification to researcher
        $researcher = User::where('user_id', $proposal->user_id)->orWhere('email', $proposal->user_id)->first();
        if ($researcher) {
            // Collect recent comments to include in the notification (if any).
            $comments = DB::table('proposal_comments')
                ->where('proposal_id', $id)
                ->orderBy('created_at')
                ->get()
                ->map(function ($row) {
                    return [
                        'commenter_role' => $row->commenter_role ?? 'Staff',
                        'page' => $row->page ?? null,
                        'location' => $row->location ?? null,
                        'comment_text' => $row->comment_text ?? '',
                        'created_at' => $row->created_at ?? null,
                    ];
                })->toArray();

            $researcher->notify(new ProposalStatusUpdated(
                proposalTitle: $proposal->title,
                status: $validated['status'],
                feedback: $validated['feedback'] ?? '',
                approvedGrant: $validated['status'] === 'Approved' ? ($validated['approved_grant'] ?? $proposal->budget) : null,
                comments: $comments,
            ));
        }

        return back()->with('success', 'Proposal status updated to '.$validated['status'].'.');
    }

    public function researcherIndex()
    {
        $user = auth()->user();
        $userId = $user->user_id ?? $user->email;

        $proposals = Schema::hasTable('proposals')
            ? DB::table('proposals')
                ->where('user_id', $userId)
                ->when(Schema::hasColumn('proposals', 'archived_at'), fn($q) => $q->whereNull('archived_at'))
                ->latest('created_at')
                ->get()
            : collect();

        $proposalCount = $proposals->count();

        $approvedFunds = $proposals->where('status', 'Approved')->sum('approved_grant');

        $announcements = Schema::hasTable('announcements')
            ? DB::table('announcements')
                ->whereIn('recipients', ['all', 'researcher'])
                ->where(function ($query) {
                    $query->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                })
                ->latest('created_at')
                ->limit(5)
                ->get()
            : collect();

        $notifications = $user->notifications()->latest()->limit(6)->get();
        $unreadNotificationCount = $user->unreadNotifications()->count();

        return view('researcher-dashboard', compact(
            'announcements',
            'proposals',
            'proposalCount',
            'approvedFunds',
            'notifications',
            'unreadNotificationCount'
        ));
    }

    public function downloadDocument(int $documentId)
    {
        $document = DB::table('proposal_documents')
            ->join('proposals', 'proposal_documents.proposal_id', '=', 'proposals.id')
            ->where('proposal_documents.id', $documentId)
            ->select('proposal_documents.*', 'proposals.user_id')
            ->first();
        abort_if(! $document, 404);

        $user = auth()->user();
        $isOwner = $user->role === 'researcher' && $document->user_id === ($user->user_id ?? $user->email);
        abort_unless($isOwner || in_array($user->role, ['admin', 'staff'], true), 403);

        $path = public_path('uploads/proposals/'.$document->stored_name);
        abort_unless(file_exists($path), 404);

        // If the file is a viewable type (PDF or common image types), send inline so the browser can render it.
        $mime = mime_content_type($path) ?: ($document->mime_type ?? 'application/octet-stream');
        $viewable = in_array($mime, ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'], true);

        if ($viewable) {
            return response()->file($path, ['Content-Type' => $mime]);
        }

        // Fallback: force download for other types
        return response()->download($path, $document->original_name);
    }

    /**
     * View a proposal document inline (Content-Disposition: inline).
     * Preserves the same permission checks as downloadDocument.
     */
    public function viewDocumentById(int $documentId)
    {
        $document = DB::table('proposal_documents')
            ->join('proposals', 'proposal_documents.proposal_id', '=', 'proposals.id')
            ->where('proposal_documents.id', $documentId)
            ->select('proposal_documents.*', 'proposals.user_id')
            ->first();
        abort_if(! $document, 404);

        $user = auth()->user();
        $isOwner = $user->role === 'researcher' && $document->user_id === ($user->user_id ?? $user->email);
        abort_unless($isOwner || in_array($user->role, ['admin', 'staff'], true), 403);

        $path = public_path('uploads/proposals/'.$document->stored_name);
        abort_unless(file_exists($path), 404);

        $mime = mime_content_type($path) ?: ($document->mime_type ?? 'application/octet-stream');

        return response()->file($path, ['Content-Type' => $mime]);
    }

    public function showPdf(int $id)
    {
        $proposal = DB::table('proposals')->where('id', $id)->first();
        abort_if(! $proposal, 404);

        $documents = DB::table('proposal_documents')->where('proposal_id', $id)->get();

        // Prefer the researcher's main proposal (app_form) first, then combined_requirements_pdf, otherwise first document
        $selected = $documents->firstWhere('document_type', 'app_form')
            ?? $documents->firstWhere('document_type', 'combined_requirements_pdf')
            ?? $documents->first();

        abort_if(! $selected, 404);

        $comments = DB::table('proposal_comments')->where('proposal_id', $id)->orderBy('created_at')->get();

        return view('admin.proposals.pdf-view', compact('proposal', 'selected', 'documents', 'comments'));
    }

    public function storeComment(Request $request, int $id)
    {
        $validated = $request->validate([
            'comment_text' => ['required', 'string', 'max:2000'],
            'page' => ['nullable', 'integer', 'min:1'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $proposal = DB::table('proposals')->where('id', $id)->first();
        abort_if(! $proposal, 404);

        $user = auth()->user();

        $commentId = DB::table('proposal_comments')->insertGetId([
            'proposal_id' => $id,
            'user_id' => $user->id ?? null,
            'commenter_role' => $user->role ?? null,
            'page' => $validated['page'] ?? null,
            'location' => $validated['location'] ?? null,
            'comment_text' => $validated['comment_text'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $comment = DB::table('proposal_comments')->where('id', $commentId)->first();

        return response()->json(['success' => true, 'comment' => $comment]);
    }

    /**
     * Return all comments for a proposal as JSON (used by the View modal).
     */
    public function getComments(int $id)
    {
        $proposal = DB::table('proposals')->where('id', $id)->first();
        if (! $proposal) {
            return response()->json(['comments' => []], 404);
        }

        $comments = [];
        if (Schema::hasTable('proposal_comments')) {
            $comments = DB::table('proposal_comments')
                ->where('proposal_id', $id)
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return response()->json(['comments' => $comments]);
    }

    public function storeFundTransaction(Request $request)
    {
        $validated = $request->validate([
            'proposal_id' => ['required', 'exists:proposals,id'],
            'type' => ['required', Rule::in(['Release', 'Expense', 'Liquidation'])],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'transaction_date' => ['required', 'date'],
        ]);

        $proposal = DB::table('proposals')->where('id', $validated['proposal_id'])->first();
        $user = auth()->user();
        if ($user->role === 'researcher' && $proposal->user_id !== ($user->user_id ?? $user->email)) {
            abort(403);
        }

        DB::table('research_fund_transactions')->insert([
            ...$validated,
            'status' => $user->role === 'researcher' ? 'Pending' : 'Approved',
            'submitted_by' => $user->id,
            'reviewed_by' => $user->role === 'researcher' ? null : $user->id,
            'reviewed_at' => $user->role === 'researcher' ? null : now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Research fund transaction saved.');
    }

    public function reviewFundTransaction(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Approved', 'Rejected'])],
        ]);

        DB::table('research_fund_transactions')->where('id', $id)->update([
            'status' => $validated['status'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Research fund transaction reviewed.');
    }

    public function archive(Request $request, int $id)
    {
        $user = $request->user();

        // Guard: ensure the archived_at column exists
        if (! Schema::hasColumn('proposals', 'archived_at')) {
            return back()->withErrors(['proposal_error' => 'The proposals table is missing the archived_at column. Please run: php artisan migrate']);
        }

        $query = DB::table('proposals')->where('id', $id);
        if ($user->role === 'researcher') {
            $query->where('user_id', $user->user_id ?? $user->email);
        }

        $proposal = $query->first();
        if (! $proposal) {
            return back()->withErrors(['proposal_error' => 'Proposal not found or you do not have permission to archive it.']);
        }

        // Idempotent: already archived
        if (! empty($proposal->archived_at)) {
            return back()->with('info', 'Proposal is already archived.');
        }

        DB::table('proposals')->where('id', $id)->update([
            'archived_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Proposal archived successfully.');
    }

    public function restore(Request $request, int $id)
    {
        $user = $request->user();

        // Guard: ensure the archived_at column exists
        if (! Schema::hasColumn('proposals', 'archived_at')) {
            return back()->withErrors(['proposal_error' => 'The proposals table is missing the archived_at column. Please run: php artisan migrate']);
        }

        $query = DB::table('proposals')->where('id', $id);
        if ($user->role === 'researcher') {
            $query->where('user_id', $user->user_id ?? $user->email);
        }

        $proposal = $query->first();
        if (! $proposal) {
            return back()->withErrors(['proposal_error' => 'Proposal not found or you do not have permission to restore it.']);
        }

        // Idempotent: already active
        if (empty($proposal->archived_at)) {
            return back()->with('info', 'Proposal is already active (not archived).');
        }

        DB::table('proposals')->where('id', $id)->update([
            'archived_at' => null,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Proposal restored successfully.');
    }
}
