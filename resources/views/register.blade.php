<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Panabo City Library — Create Account</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
        /* ── MATCHING CLEAN LIGHT INTELLECTUAL PALETTE ── */
        --bg: #f6f7fb;
        --panel: #ffffff;
        --card: #eef2f7;
        --border: #dcdfe6;
        --text: #1f2937;
        --muted: #6b7280;

        --gold: #b38f2d;
        --gold-light: #c9a84c;
        --accent: #2563eb;
        --success: #10b981;
        --error: #ef4444;
        --shadow-lg: 0 22px 50px rgba(31, 41, 55, 0.12);
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(180deg, #f9fafb 0%, #eef2f7 100%);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            left: -10%;
            bottom: -20%;
            width: 55vw;
            height: 55vw;
            background: radial-gradient(circle, rgba(179, 143, 45, 0.18), transparent 60%);
            z-index: -1;
            pointer-events: none;
        }

        .register-container {
            width: 100%;
            max-width: 520px;
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 40px;
            box-shadow: var(--shadow-lg);
            position: relative;
        }

        .logo-area { text-align: center; margin-bottom: 30px; }

        .logo-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 24px;
            border: 3px solid var(--gold);
            margin-bottom: 15px;
        }

        h2 { font-family: 'Playfair Display', serif; font-size: 24px; margin-bottom: 8px; text-align: center; }
        p.subtitle { color: var(--muted); font-size: 13px; text-align: center; margin-bottom: 30px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; font-weight: 500; }

        input, select {
            width: 100%;
            background: #f7f8fc;
            border: 1px solid #d6dde8;
            border-radius: 10px;
            padding: 12px 15px;
            color: var(--text);
            outline: none;
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input:focus, select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(179, 143, 45, 0.12);
        }
        select { appearance: none; cursor: pointer; }

        input[type="file"] {
            padding: 8px 12px;
            cursor: pointer;
        }

        .role-wrapper { position: relative; }
        .role-wrapper::after {
            content: '▾';
            position: absolute;
            right: 15px; top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            pointer-events: none;
        }

        .form-row {
            display: flex;
            gap: 15px;
        }
        .form-row .form-group {
            flex: 1;
        }

        .password-hint {
            font-size: 11px;
            color: var(--muted);
            margin-top: 4px;
            display: block;
        }

        .btn-reg {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            border: none;
            border-radius: 10px;
            color: #ffffff;
            font-weight: 600;
            cursor: pointer;
            margin-top: 15px;
            text-align: center;
            display: block;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-reg:hover { transform: translateY(-1px); box-shadow: 0 10px 24px rgba(179,143,45,0.2); }

        .btn-reg:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            opacity: 1;
        }

        .footer-links { text-align: center; margin-top: 20px; font-size: 13px; color: var(--muted); }
        .footer-links a { color: var(--gold); text-decoration: none; font-weight: 600; }
        .footer-links a:hover { text-decoration: underline; }

        /* ── CUSTOM POP-UP MODAL STYLE ── */
        .success-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(31, 41, 55, 0.4);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        .success-popup {
            background: #ffffff;
            width: 90%;
            max-width: 400px;
            padding: 35px 30px;
            border-radius: 16px;
            text-align: center;
            border: 1px solid var(--border);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes popIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .success-icon {
            font-size: 44px;
            color: #ffffff;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            width: 70px; height: 70px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px auto;
            box-shadow: 0 8px 16px rgba(179,143,45,0.2);
        }
        /* Style variation for pending review clock icon */
        .success-icon.pending {
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            box-shadow: 0 8px 16px rgba(245,158,11,0.2);
        }
        .success-popup h3 {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            color: var(--text);
            margin-bottom: 10px;
        }
        .success-popup p {
            font-size: 14px;
            color: var(--muted);
            margin-bottom: 25px;
            line-height: 1.5;
        }
        .btn-modal-close {
            background: var(--text);
            color: #ffffff;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: background 0.2s;
        }
        .btn-modal-close:hover { background: #111827; }

        @media (max-width: 600px) {
            .register-container { padding: 28px 22px; }
            .form-row { flex-direction: column; }
        }

        :root {
          --bg:#F5F7FA !important;
          --panel:#FFFFFF !important;
          --card:#F8FAFC !important;
          --border:#E5E7EB !important;
          --text:#1F2937 !important;
          --muted:#6B7280 !important;
          --gold:#2F9E8F !important;
          --gold-light:#26867A !important;
          --success:#22C55E !important;
          --error:#EF4444 !important;
          --shadow-lg:0 22px 50px rgba(15,23,42,.12) !important;
        }
        body { background: radial-gradient(circle at top right, rgba(47,158,143,.10), transparent 35%), radial-gradient(circle at bottom left, rgba(47,158,143,.08), transparent 30%), #F5F7FA !important; font-family:'Inter',sans-serif !important; }
        body::before { background: radial-gradient(circle, rgba(47,158,143,.12), transparent 60%) !important; }
        .register-container { border-color:#E5E7EB !important; box-shadow:0 24px 60px rgba(15,23,42,.12) !important; }
        .logo-img { border-color: rgba(47,158,143,.25) !important; box-shadow:0 10px 30px rgba(15,23,42,.08) !important; }
        h2 { color:#1F2937 !important; font-family:'Inter',sans-serif !important; font-weight:800 !important; }
        p.subtitle, label, .password-hint, .footer-links { color:#6B7280 !important; }
        input, select { background:#fff !important; border-color:#E5E7EB !important; font-family:'Inter',sans-serif !important; }
        input:focus, select:focus { border-color:#2F9E8F !important; box-shadow:0 0 0 3px rgba(47,158,143,.12) !important; }
        .btn-reg { background: linear-gradient(135deg, #2F9E8F, #26867A) !important; box-shadow:0 10px 30px rgba(15,23,42,.08) !important; font-family:'Inter',sans-serif !important; }
        .footer-links a { color:#2F9E8F !important; }
        .success-overlay { background: rgba(31,41,55,.4) !important; }
        .success-popup { border-color:#E5E7EB !important; box-shadow:0 20px 40px rgba(15,23,42,.15) !important; }
        .success-icon { background: linear-gradient(135deg, #2F9E8F, #26867A) !important; }
        .success-icon.pending { background: linear-gradient(135deg, #F59E0B, #FBBF24) !important; }
        .btn-modal-close { background:#1F2937 !important; }
    </style>
</head>
<body>

<div class="register-container">
    <div class="logo-area">
        <img src="{{ asset('images/library logos.jpg') }}" class="logo-img" alt="Logo">
        <h2>Create Account</h2>
        <p class="subtitle">Enter your details to register for the library</p>
    </div>

    <!-- The form points directly to your register handler route -->
    <form id="registrationForm" action="{{ route('register.submit') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div id="formErrorSummary" class="error-summary" style="display:none; margin-bottom: 16px; padding: 12px 14px; border: 1px solid #ef4444; border-radius: 8px; background: rgba(239,68,68,.08); color: #991b1b;"></div>

        <div class="form-group">
            <label>Register As</label>
            <div class="role-wrapper">
                <select name="role" id="roleSelect" required>
                    <option value="student">Student</option>
                    <option value="researcher">Researcher</option>
                    <option value="visitor">Visitor</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Full Name</label>
            <input type="text" id="regName" name="name" placeholder="Enter your full name" required>
            <small class="password-hint">Letters, spaces, apostrophes, and periods only.</small>
            <div id="error_name" class="error-message" style="display:none; color:#b91c1c; font-size:13px; margin-top:6px;"></div>
        </div>

        <div class="form-group" id="schoolGroup">
            <label>School / Institution</label>
            <input list="schoolOptions" id="regSchool" name="selected_school" placeholder="Type or select your school" required>
            <input type="hidden" id="regSchoolHidden" name="school" value="">
            <datalist id="schoolOptions">
                @foreach($schoolOptions ?? [] as $schoolOption)
                    <option value="{{ $schoolOption }}">{{ $schoolOption }}</option>
                @endforeach
                <option value="other">Other / Visitor</option>
            </datalist>
            <div id="error_school" class="error-message" style="display:none; color:#b91c1c; font-size:13px; margin-top:6px;"></div>
        </div>

        <div class="form-group" id="regSchoolOtherGroup" style="display:none;">
            <label>If Other, type your school, Visitor or N/A</label>
            <input type="text" id="regSchoolOther" placeholder="Type your school name or N/A">
        </div>

        <div class="form-group" id="idGroup">
            <label id="idLabel">ID Number</label>
            <input type="text" id="regId" name="user_id" placeholder="e.g. STU-2026-001">
            <small class="password-hint" id="idHint">(ID number is the id of your id)</small>
            <div id="error_user_id" class="error-message" style="display:none; color:#b91c1c; font-size:13px; margin-top:6px;"></div>
        </div>

        <div class="form-group">
            <label>Profile Picture</label>
            <input type="file" id="regPicture" name="profile_picture" accept="image/*" required>
            <div id="error_profile_picture" class="error-message" style="display:none; color:#b91c1c; font-size:13px; margin-top:6px;"></div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Birthdate</label>
                <input type="date" id="regBirthdate" name="birthdate" required onchange="calculateAge()">
                <div id="error_birthdate" class="error-message" style="display:none; color:#b91c1c; font-size:13px; margin-top:6px;"></div>
            </div>

            <div class="form-group">
                <label>Age</label>
                <input type="number" id="regAge" name="age" placeholder="Age" min="1" max="120" required readonly>
                <div id="error_age" class="error-message" style="display:none; color:#b91c1c; font-size:13px; margin-top:6px;"></div>
            </div>
        </div>

        <div class="form-group" id="genderGroup">
            <label>Gender</label>
            <div class="role-wrapper">
                <select name="gender" id="regGender" required>
                    <option value="" disabled selected>Select your gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Non-binary">Non-binary</option>
                    <option value="Prefer not to say">Prefer not to say</option>
                </select>
            </div>
            <div id="error_gender" class="error-message" style="display:none; color:#b91c1c; font-size:13px; margin-top:6px;"></div>
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" id="regEmail" name="email" placeholder="name@email.com" required>
            <div id="error_email" class="error-message" style="display:none; color:#b91c1c; font-size:13px; margin-top:6px;"></div>
        </div>

        <div class="form-group">
            <label>Create Password</label>
            <div style="position: relative;">
                <input type="password" id="regPass" name="password" placeholder="••••••••"
                       pattern="^(?=.*[A-Z])(?=.*[!@#$%^&*(),.?&quot;{}|<>]).{8,}$"
                       title="Password must be at least 8 characters long, include at least one uppercase letter, and contain at least one special character." required>
                <span id="toggleRegPassword" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 16px; user-select: none; color: var(--muted);">Show</span>
            </div>
            <small class="password-hint">Must be at least 8 characters, include 1 uppercase letter, and 1 special character.</small>
            <div id="error_password" class="error-message" style="display:none; color:#b91c1c; font-size:13px; margin-top:6px;"></div>
        </div>

        <div class="form-group">
            <label>Confirm Password</label>
            <div style="position: relative;">
                <input type="password" id="regPassConfirm" name="password_confirmation" placeholder="••••••••" required>
                <span id="toggleRegPassConfirm" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 16px; user-select: none; color: var(--muted);">Show</span>
            </div>
            <div id="error_password_confirmation" class="error-message" style="display:none; color:#b91c1c; font-size:13px; margin-top:6px;"></div>
        </div>

        <button type="submit" id="submitBtn" class="btn-reg">Create Account</button>
    </form>

    <div class="footer-links">
        Already have an account? <a href="/login">Sign In here</a>
    </div>
</div>

<div id="successOverlay" class="success-overlay">
    <div class="success-popup">
        <div id="modalIcon" class="success-icon">Success</div>
        <h3 id="modalTitle">Registration Successful!</h3>
        <p id="modalMessage">Your library account is ready. You can now use your account parameters to sign into the hub terminal.</p>
        <button class="btn-modal-close" id="modalCloseBtn" onclick="closeSuccessModal()">Proceed to Login</button>
    </div>
</div>

<script>
// Toggle mask field visibility logic
document.getElementById('toggleRegPassword').addEventListener('click', function () {
    const passwordInput = document.getElementById('regPass');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        this.textContent = 'Hide';
    } else {
        passwordInput.type = 'password';
        this.textContent = 'Show';
    }
});

document.getElementById('toggleRegPassConfirm').addEventListener('click', function () {
    const confirmInput = document.getElementById('regPassConfirm');
    if (confirmInput.type === 'password') {
        confirmInput.type = 'text';
        this.textContent = 'Hide';
    } else {
        confirmInput.type = 'password';
        this.textContent = 'Show';
    }
});

const roleSelect = document.getElementById('roleSelect');
const regSchool = document.getElementById('regSchool');
const regSchoolHidden = document.getElementById('regSchoolHidden');
const schoolGroup = document.getElementById('schoolGroup');
const regSchoolOtherGroup = document.getElementById('regSchoolOtherGroup');
const regSchoolOther = document.getElementById('regSchoolOther');
const idGroup = document.getElementById('idGroup');
const regId = document.getElementById('regId');
const genderGroup = document.getElementById('genderGroup');
const regGender = document.getElementById('regGender');

function updateRegistrationFields() {
    const isVisitor = roleSelect.value === 'visitor';
    const isResearcher = roleSelect.value === 'researcher';

    schoolGroup.style.display = isVisitor ? 'none' : 'block';
    idGroup.style.display = (isVisitor || isResearcher) ? 'none' : 'block';
    regSchoolOtherGroup.style.display = isVisitor ? 'none' : regSchool.value === 'other' ? 'block' : 'none';

    const idLabel = document.getElementById('idLabel');
    const idHint = document.getElementById('idHint');

    if (isResearcher) {
        regId.removeAttribute('required');
        regId.value = '';
        idLabel.textContent = 'ID Number';
        idHint.textContent = '';
    } else if (isVisitor) {
        regId.removeAttribute('required');
        regId.value = '';
        idLabel.textContent = 'ID Number';
        idHint.textContent = '';
    } else {
        idLabel.textContent = 'ID Number';
        idHint.textContent = '(ID number is the id of your id)';
        regId.setAttribute('required', 'required');
    }

    regSchool.required = !isVisitor;
    regGender.required = true;
    regSchoolOther.required = !isVisitor && regSchool.value === 'other';

    if (isVisitor) {
        regSchool.value = '';
        regSchoolHidden.value = 'Visitor';
        regSchoolOther.value = '';
    } else if (regSchool.value === 'other') {
        regSchoolHidden.value = regSchoolOther.value.trim() || '';
    } else {
        regSchoolHidden.value = regSchool.value.trim();
    }
}

roleSelect.addEventListener('change', updateRegistrationFields);
regSchool.addEventListener('change', function () {
    if (this.value === 'other' && roleSelect.value !== 'visitor') {
        regSchoolOtherGroup.style.display = 'block';
        regSchoolOther.required = true;
        regSchoolHidden.value = regSchoolOther.value.trim();
    } else {
        regSchoolOtherGroup.style.display = 'none';
        regSchoolOther.required = false;
        regSchoolOther.value = '';
        regSchoolHidden.value = this.value.trim();
    }
});

document.addEventListener('DOMContentLoaded', updateRegistrationFields);

regSchoolOther.addEventListener('input', function () {
    if (regSchool.value === 'other') {
        const value = this.value.trim();
        regSchoolHidden.value = value !== '' ? value : '';
    }
});

// Auto calculate age based on selected birthdate
function calculateAge() {
    const birthdateInput = document.getElementById('regBirthdate').value;
    if (!birthdateInput) return;

    const birthDate = new Date(birthdateInput);
    const today = new Date();

    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDifference = today.getMonth() - birthDate.getMonth();

    if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }

    document.getElementById('regAge').value = age >= 0 ? age : 0;
}

function showModal(type, title, message) {
    const overlay = document.getElementById('successOverlay');
    const modalIcon = document.getElementById('modalIcon');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const modalCloseBtn = document.getElementById('modalCloseBtn');

    modalTitle.textContent = title;
    modalMessage.textContent = message;

    if (type === 'success') {
        modalIcon.textContent = '✓';
        modalIcon.className = 'success-icon';
        modalCloseBtn.textContent = 'Proceed to Verify Email';
    } else {
        modalIcon.textContent = '!';
        modalIcon.className = 'success-icon pending';
        modalCloseBtn.textContent = 'Close';
    }

    overlay.style.display = 'flex';
}

// Intercept submission to handle unique verification context based on roles
document.getElementById('registrationForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formElement = this;
    const submitBtn = document.getElementById('submitBtn');
    const selectedRole = document.getElementById('roleSelect').value;
    const fullName = document.getElementById('regName').value.trim();
    const password = document.getElementById('regPass').value;
    const confirmPassword = document.getElementById('regPassConfirm').value;
    const schoolOtherText = regSchoolOther.value.trim();
    const formData = new FormData(formElement);

    if (/\d/.test(fullName)) {
        showModal('warning', 'Invalid name', 'Full name cannot contain numbers. Please enter a valid name.');
        submitBtn.textContent = "Create Account";
        submitBtn.disabled = false;
        return;
    }

    const passwordRules = /^(?=.*[A-Z])(?=.*[!@#$%^&*(),.?"{}|<>]).{8,}$/;
    if (!passwordRules.test(password)) {
        showModal('warning', 'Password requirements not met', 'Password must be at least 8 characters long, include 1 uppercase letter, and at least 1 special character.');
        submitBtn.textContent = "Create Account";
        submitBtn.disabled = false;
        return;
    }

    if (password !== confirmPassword) {
        showModal('warning', 'Password mismatch', 'Passwords do not match. Please confirm your password.');
        submitBtn.textContent = "Create Account";
        submitBtn.disabled = false;
        return;
    }

    if (selectedRole === 'visitor') {
        formData.set('school', 'Visitor');
        formData.delete('user_id');
    } else {
        if (selectedRole === 'researcher' && regId.value.trim() === '') {
            formData.delete('user_id');
        }

        if (regSchool.value === 'other') {
            if (!schoolOtherText) {
                alert('Please type your school name or N/A for the Other / Visitor option.');
                submitBtn.textContent = "Create Account";
                submitBtn.disabled = false;
                return;
            }
            formData.set('school', schoolOtherText);
        } else {
            formData.set('school', regSchool.value.trim());
        }
    }

    submitBtn.textContent = "Verifying credentials...";
    submitBtn.disabled = true;

    const errorFields = [
        'name', 'school', 'user_id', 'profile_picture', 'birthdate', 'age', 'gender', 'email', 'password', 'password_confirmation'
    ];
    errorFields.forEach(field => {
        const errorEl = document.getElementById(`error_${field}`);
        if (errorEl) {
            errorEl.style.display = 'none';
            errorEl.textContent = '';
        }
    });
    const errorSummary = document.getElementById('formErrorSummary');
    errorSummary.style.display = 'none';
    errorSummary.textContent = '';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || formElement.querySelector('input[name="_token"]')?.value;

    // Gather form payload containing files/images securely
    fetch(formElement.action, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken || '',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(async response => {
        const contentType = response.headers.get('content-type') || '';
        let data = null;

        if (contentType.includes('application/json')) {
            data = await response.json();
        }

        if (!response.ok) {
            if (data && data.errors) {
                const messages = [];
                Object.keys(data.errors).forEach(key => {
                    const errorEl = document.getElementById(`error_${key}`);
                    const message = Array.isArray(data.errors[key]) ? data.errors[key][0] : data.errors[key];
                    if (errorEl) {
                        errorEl.textContent = message;
                        errorEl.style.display = 'block';
                    }
                    messages.push(message);
                });
                if (messages.length > 0) {
                    errorSummary.textContent = 'Please fix the highlighted fields below.';
                    errorSummary.style.display = 'block';
                }
            } else if (data && data.message) {
                showModal('warning', 'Registration failed', data.message);
            } else if (response.status === 419) {
                showModal('warning', 'Session expired', 'Your page session expired. Please refresh and try again.');
            } else {
                showModal('warning', 'Registration failed', 'Please check your details and try again.');
            }
            throw new Error('Registration failed');
        }

        showModal('success', 'Registration Complete', 'Your account is ready. We have sent a 6-digit OTP to your email. Please verify it to continue.');
        submitBtn.textContent = "Create Account";
        submitBtn.disabled = false;
    })
    .catch(error => {
        console.error('Registration Error:', error);
        submitBtn.textContent = "Create Account";
        submitBtn.disabled = false;
    });
});

function closeSuccessModal() {
    const overlay = document.getElementById('successOverlay');
    overlay.style.display = 'none';

    const modalCloseBtn = document.getElementById('modalCloseBtn');
    if (modalCloseBtn && modalCloseBtn.textContent.trim() === 'Proceed to Verify Email') {
        window.location.href = '/email/verify';
        return;
    }

    if (modalCloseBtn && modalCloseBtn.textContent.trim() === 'Proceed to Login') {
        window.location.href = '/login';
    }
}
</script>
</body>
</html>
