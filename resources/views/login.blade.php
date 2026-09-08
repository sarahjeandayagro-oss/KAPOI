<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Panabo City Library— Login</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/style.css') }}">

<style>

    *, *::before, *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    :root {
        /* ── CLEAN MODERN PALETTE ── */
        --bg: #f6f7fb;
        --panel: #ffffff;
        --card: #eef2f7;
        --border: #dcdfe6;
        --text: #1f2937;
        --muted: #6b7280;

        /* ── ACADEMIC BRAND ACCENTS ── */
        --gold: #b38f2d;
        --gold-light: #c9a84c;
        --accent: #2563eb;
        --success: #10b981;
        --error: #ef4444;

        /* ── TAB AND MAIN BUTTON COLORS ── */
        --tab-active-blue: #c9a84c;
        --btn-signin-blue: #b38f2d;

        --shadow-lg: 0 24px 60px rgba(31, 41, 55, 0.12);
        --shadow-sm: 0 8px 20px rgba(31, 41, 55, 0.08);
    }


    /* =========================================================
       BODY
       ========================================================= */

    body {
        font-family: 'DM Sans', sans-serif;

        /* BACKGROUND IMAGE */
        background:
            linear-gradient(
                180deg,
                rgba(245, 247, 250, 0.68) 0%,
                rgba(245, 247, 250, 0.78) 100%
            ),
           url("{{ asset('images/library-group-study-stockcake.jpg') }}")
            center center / cover no-repeat fixed !important;

        color: var(--text);
        min-height: 100vh;

        display: flex;
        justify-content: center;
        align-items: center;

        padding: 28px 20px;

        overflow-y: auto;
        overflow-x: hidden;

        position: relative;
    }


    /* =========================================================
       BACKGROUND DECORATION
       ========================================================= */

    body::before {
        content: '';
        position: fixed;

        right: -10%;
        top: -20%;

        width: 60vw;
        height: 60vw;

        background:
            radial-gradient(
                circle,
                rgba(47, 158, 143, 0.12),
                transparent 60%
            );

        z-index: -1;
        pointer-events: none;
    }


    /* =========================================================
       TWO-COLUMN LAYOUT
       LOGIN FORM LEFT / BRANDING RIGHT
       ========================================================= */

    .main-wrapper {
        width: 100%;
        max-width: 1120px;

        display: flex;
        align-items: center;
        justify-content: center;

        gap: 36px;
        padding: 16px 0;
    }


    /* =========================================================
       BRANDING SECTION
       ========================================================= */

    .brand {
        flex: 1 1 420px;

        text-align: center;
        margin-bottom: 0;

        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }


    .brand h1 {
        font-family: 'Playfair Display', serif;

        font-size: 42px;
        font-weight: 700;

        letter-spacing: -1px;

        color: var(--gold);

        margin-bottom: 10px;
    }


    .brand p {
        color: #718096;

        font-size: 15px;
        font-weight: 400;

        letter-spacing: 0.5px;

        max-width: 420px;
        text-align: center;
    }


    /* =========================================================
       BRAND LOGO
       ========================================================= */

    .brand-logo {
        width: min(320px, 100%);
        height: auto;

        aspect-ratio: 1;

        object-fit: contain;

        border-radius: 24px;

        border: 3px solid rgba(47, 158, 143, 0.25);

        margin-bottom: 20px;

        display: block;

        background: #ffffff;

        padding: 12px;

        box-shadow:
            0 10px 30px rgba(15, 23, 42, 0.08);

        will-change: transform, box-shadow;

        animation: floatLogo 10s ease-in-out infinite;
    }


    /* =========================================================
       LOGIN BOX
       ========================================================= */

    .right-panel {
        flex: 1 1 480px;

        width: 100%;
        max-width: 520px;

        background: rgba(255, 255, 255, 0.96);

        border: 1px solid #E5E7EB;

        border-radius: 20px;

        display: flex;
        flex-direction: column;

        padding: 34px 44px;

        box-shadow:
            0 24px 60px rgba(15, 23, 42, 0.12);

        will-change: transform, box-shadow;

        animation: floatPanel 12s ease-in-out infinite;
    }


    .brand-logo:hover,
    .right-panel:hover {
        box-shadow:
            0 24px 60px rgba(15, 23, 42, 0.18);
    }


    /* =========================================================
       ANIMATIONS
       ========================================================= */

    @keyframes floatLogo {
        0%, 100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-10px);
        }
    }


    @keyframes floatPanel {
        0%, 100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-6px);
        }
    }


    /* =========================================================
       LOGIN TITLE
       ========================================================= */

    .login-title {
        font-family: 'Inter', sans-serif;

        font-size: 30px;
        font-weight: 700;

        margin-bottom: 8px;

        color: #1F2937;
    }


    .login-subtitle {
        color: #6B7280;

        font-size: 14px;

        margin-bottom: 26px;
    }


    /* =========================================================
       TABS
       ========================================================= */

    .tabs {
        display: flex;

        gap: 12px;

        background: #F8FAFC;

        border-radius: 12px;

        padding: 5px;

        margin-bottom: 24px;

        border: 1px solid #E5E7EB;
    }


    .tab {
        flex: 1;

        padding: 12px;

        border: none;

        font-family: 'DM Sans', sans-serif;

        font-size: 14px;
        font-weight: 600;

        border-radius: 8px;

        cursor: pointer;

        transition: all 0.2s;

        display: flex;

        align-items: center;
        justify-content: center;

        gap: 8px;
    }


    /* Credentials button */

    .tab.active-credentials {
        background: #2F9E8F;

        color: #ffffff;
    }


    /* Scan button */

    .tab.active-scan {
        background: #ffffff;

        color: #1F2937;
    }


    .tab.inactive {
        background: transparent;

        color: #6B7280;
    }


    /* =========================================================
       FORM
       ========================================================= */

    .form-group {
        margin-bottom: 24px;
    }


    label {
        display: block;

        font-size: 14px;
        font-weight: 600;

        color: #6B7280;

        margin-bottom: 8px;
    }


    input[type="text"],
    input[type="password"],
    input[type="email"],
    select {

        width: 100%;

        background: #ffffff;

        border: 1px solid #E5E7EB;

        border-radius: 10px;

        padding: 13px 16px;

        color: #1f2937;

        font-family: 'Inter', sans-serif;

        font-size: 14px;

        outline: none;

        transition:
            border-color 0.2s,
            box-shadow 0.2s;
    }


    input::placeholder {
        color: #4b5563;
    }


    input:focus,
    select:focus {

        border-color: #2F9E8F;

        box-shadow:
            0 0 0 3px rgba(47, 158, 143, 0.12);
    }


    select {
        appearance: none;

        cursor: pointer;

        line-height: 1.35;
    }


    select option {
        color: #111827;

        background: #ffffff;
    }


    /* =========================================================
       SIGN IN BUTTON
       ========================================================= */

    .btn-primary {

        width: 100%;

        padding: 15px;

        background:
            linear-gradient(
                135deg,
                #2F9E8F,
                #26867A
            );

        border: none;

        border-radius: 10px;

        color: #ffffff;

        font-family: 'Inter', sans-serif;

        font-size: 15px;
        font-weight: 600;

        cursor: pointer;

        transition:
            transform 0.2s,
            box-shadow 0.2s;

        margin-top: 8px;

        box-shadow:
            0 10px 30px rgba(15, 23, 42, 0.08);
    }


    .btn-primary:hover {

        transform: translateY(-1px);

        box-shadow:
            0 12px 30px rgba(15, 23, 42, 0.14);
    }


    /* =========================================================
       DIVIDER
       ========================================================= */

    .divider {

        display: flex;

        align-items: center;

        gap: 12px;

        margin: 24px 0;

        color: #6B7280;

        font-size: 13px;
    }


    .divider::before,
    .divider::after {

        content: '';

        flex: 1;

        height: 1px;

        background: #E5E7EB;
    }


    /* =========================================================
       SCAN WINDOW
       ========================================================= */

    .scan-panel {

        display: none;

        flex-direction: column;

        align-items: center;

        gap: 20px;
    }


    .scan-panel.active {
        display: flex;
    }


    .qr-frame {

        width: 220px;
        height: 220px;

        border: 2px solid #E5E7EB;

        border-radius: 16px;

        background: #F8FAFC;

        position: relative;

        display: flex;

        align-items: center;
        justify-content: center;

        overflow: hidden;
    }


    .scan-line {

        position: absolute;

        width: 80%;
        height: 2px;

        background:
            linear-gradient(
                90deg,
                transparent,
                #2F9E8F,
                transparent
            );

        animation:
            scanMove 2s ease-in-out infinite;
    }


    @keyframes scanMove {

        0% {
            top: 20%;
        }

        50% {
            top: 80%;
        }

        100% {
            top: 20%;
        }
    }


    .qr-icon {

        font-size: 60px;

        opacity: 0.15;
    }


    .scan-label {

        font-size: 13px;

        color: #6B7280;

        text-align: center;
    }


    /* =========================================================
       ERROR MESSAGE
       ========================================================= */

    .error-msg {

        color: #ef4444;

        font-size: 12px;

        margin-top: 6px;

        display: none;
    }


    /* =========================================================
       FORGOT PASSWORD
       ========================================================= */

    .forgot {

        text-align: right;

        margin-top: 8px;

        margin-bottom: 20px;
    }


    .forgot a {

        font-size: 12px;

        color: #4b5563;

        text-decoration: none;
    }


    .forgot a:hover {
        text-decoration: underline;
    }


    /* =========================================================
       DEMO LINKS
       ========================================================= */

    .demo-links {

        margin-top: 24px;

        padding-top: 18px;

        border-top: 1px solid #E5E7EB;

        display: flex;

        gap: 8px;

        flex-wrap: wrap;
    }


    .demo-chip {

        padding: 6px 14px;

        border: 1px solid #E5E7EB;

        border-radius: 20px;

        font-size: 12px;

        color: #6B7280;

        text-decoration: none;
    }


    /* =========================================================
       REGISTER
       ========================================================= */

    #registerBox {

        text-align: center;

        margin-top: 20px;
    }


    #registerBox a {

        color: #2F9E8F;

        text-decoration: none;

        font-weight: 600;
    }


    /* =========================================================
       LIBRARY PORTAL BUTTONS
       ========================================================= */

    .btn-scan {

        width: 100%;

        display: block;

        text-align: center;

        padding: 13px 15px;

        border-radius: 10px;

        border: 1px solid #E5E7EB;

        background: #F8FAFC;

        color: #1F2937;

        font-family: 'Inter', sans-serif;

        font-size: 14px;

        font-weight: 600;

        cursor: pointer;

        transition:
            all 0.2s ease;
    }


    .btn-scan:hover {

        background: #2F9E8F;

        border-color: #2F9E8F;

        color: #ffffff;

        transform: translateY(-1px);
    }


    /* =========================================================
       RESPONSIVE DESIGN
       ========================================================= */

    @media (max-width: 900px) {

        .main-wrapper {

            flex-direction: column;

            gap: 24px;
        }


        .brand {

            align-items: center;

            text-align: center;
        }


        .brand-logo {

            width: min(220px, 100%);

            margin-bottom: 16px;
        }
    }


    @media (max-width: 720px) {

        .right-panel {

            padding: 32px 28px;
        }


        .brand h1 {

            font-size: 32px;
        }
    }


    @media (max-width: 480px) {

        .right-panel {

            padding: 28px 20px;
        }


        .login-title {

            font-size: 26px;
        }


        .tabs {

            flex-direction: column;
        }
    }

</style>
</head>


<body>


<!-- =========================================================
     MAIN WRAPPER
     ========================================================= -->

<div class="main-wrapper">


    <!-- =====================================================
         BRANDING SECTION
         ===================================================== -->

    <div class="brand">
        <img
            src="{{ asset('images/library logos.jpg') }}"
            class="brand-logo"
            alt="Panabo City Library Logo"
        >
        <h2>Panabo City Library</h2>
        <p>
            A Web-Based Library Management System for Panabo City Library
        </p>
    </div>



    <!-- =====================================================
         MAIN LOGIN PANEL
         ===================================================== -->

    <div class="right-panel">


        <!-- LOGIN HEADER -->

        <div class="login-title">
            Welcome back
        </div>


        <div class="login-subtitle">
            Sign in to your Panabo City Library account
        </div>



        <!-- =================================================
             CREDENTIALS PANEL
             ================================================= -->

        <div id="credentialsPanel">


            <!-- LOGIN FORM -->

            <form
                action="{{ route('login.submit') }}"
                method="POST"
            >

                @csrf


                <!-- SESSION WARNING -->

                @if(session('warning'))

                    <div
                        style="
                            color: #b45309;
                            background: rgba(245, 158, 11, 0.12);
                            padding: 10px;
                            border-radius: 8px;
                            margin-bottom: 15px;
                            font-size: 13px;
                            border: 1px solid rgba(245, 158, 11, 0.25);
                        "
                    >

                        {{ session('warning') }}

                    </div>

                @endif



                <!-- LOGIN ERROR -->

                @if($errors->has('login_error'))

                    <div
                        style="
                            color: var(--error);
                            background: rgba(239, 68, 68, 0.1);
                            padding: 10px;
                            border-radius: 8px;
                            margin-bottom: 15px;
                            font-size: 13px;
                        "
                    >

                        {{ $errors->first('login_error') }}

                    </div>

                @endif



                <!-- ID / EMAIL -->

                <div class="form-group">

                    <label>
                        ID / Email
                    </label>


                    <input
                        type="text"
                        id="userId"
                        name="user_id"
                        placeholder="Enter your student ID or email"
                        required
                    />


                    <div
                        class="error-msg"
                        id="idError"
                    >
                        Please enter your ID or email.
                    </div>

                </div>



                <!-- PASSWORD -->

                <div class="form-group">

                    <label>
                        Password
                    </label>


                    <div style="position: relative;">

                        <input
                            type="password"
                            id="userPass"
                            name="password"
                            placeholder="Enter your password"
                            required
                        />


                        <span
                            id="togglePassword"
                            style="
                                position: absolute;
                                right: 16px;
                                top: 50%;
                                transform: translateY(-50%);
                                cursor: pointer;
                                font-size: 16px;
                                user-select: none;
                                color: #4b5563;
                            "
                        >
                            👁
                        </span>

                    </div>


                    <div
                        class="error-msg"
                        id="passError"
                    >
                        Please enter your password.
                    </div>

                </div>



                <!-- FORGOT PASSWORD -->

                <div class="forgot">

                    <a href="#">
                        Forgot password?
                    </a>

                </div>



                <!-- SIGN IN BUTTON -->

                <button
                    type="submit"
                    class="btn-primary"
                >
                    Sign In
                </button>

            </form>



            <!-- REGISTER -->

            <div
                id="registerBox"
                class="register-box"
            >

                <p
                    style="
                        color: var(--muted);
                        font-size: 13px;
                    "
                >

                    New user?

                    <a href="/register">
                        Create an Account
                    </a>

                </p>

            </div>



            <!-- LIBRARY ENTRANCE PORTAL -->

            <a
                class="btn-scan"
                href="{{ route('entrance.portal') }}"
                style="
                    margin-top: 12px;
                    text-decoration: none;
                "
            >
                Library Entrance Portal
            </a>



            <!-- OPAC PORTAL -->

            <a
                class="btn-scan"
                href="{{ route('opac.index') }}"
                style="
                    margin-top: 12px;
                    text-decoration: none;
                "
            >
                OPAC Search Portal
            </a>



            <!-- DEMO LINKS -->

            <div
                class="demo-links"
                style="margin-top: 30px;"
            >

                <div
                    style="
                        width: 100%;
                        font-size: 12px;
                        color: var(--muted);
                        margin-bottom: 8px;
                    "
                >
                </div>

            </div>


        </div>



        <!-- =================================================
             SCAN PANEL
             ================================================= -->

        <div
            id="scanPanel"
            class="scan-panel"
        >


            <p class="scan-label">
                Use the entrance portal for physical barcode scanner check-ins.
            </p>


            <!-- QR / SCANNER FRAME -->

            <div class="qr-frame">

                <div class="qr-icon">
                    ⬛
                </div>


                <div class="scan-line"></div>

            </div>


            <p class="scan-label">
                Scanner input automatically triggers lookup and entry logging.
            </p>


            <!-- OPEN ENTRANCE PORTAL -->

            <button
                class="btn-primary"
                onclick="window.location.href='{{ route('entrance.portal') }}'"
                style="
                    width: 220px;
                    margin-top: 0;
                "
            >
                Open Entrance Portal
            </button>


            <!-- BACK BUTTON -->

            <button
                class="btn-scan"
                onclick="switchTab('credentials')"
                style="
                    width: 220px;
                    margin-top: -8px;
                "
            >
                ← Back to Login
            </button>

        </div>


    </div>


</div>


<!-- =========================================================
     JAVASCRIPT FILE
     ========================================================= -->

<script src="{{ asset('login-script.js') }}"></script>

<script>


    /* =========================================================
       PASSWORD SHOW / HIDE
       ========================================================= */

    document
        .getElementById('togglePassword')
        .addEventListener('click', function () {

            const passwordInput =
                document.getElementById('userPass');


            if (passwordInput.type === 'password') {

                passwordInput.type = 'text';

                this.textContent = '🙈';

            }

            else {

                passwordInput.type = 'password';

                this.textContent = '👁';

            }

        });



    /* =========================================================
       SWITCH LOGIN TABS
       ========================================================= */

    function switchTab(tab) {

        const isCreds =
            tab === 'credentials';


        document.getElementById('credentialsPanel').style.display =
            isCreds ? 'block' : 'none';


        document.getElementById('scanPanel').style.display =
            isCreds ? 'none' : 'block';



        const tCreds =
            document.getElementById('tab-creds');


        const tScan =
            document.getElementById('tab-scan');



        if (isCreds) {

            if (tCreds) {
                tCreds.className =
                    "tab active-credentials";
            }


            if (tScan) {
                tScan.className =
                    "tab inactive";
            }

        }

        else {

            if (tCreds) {
                tCreds.className =
                    "tab inactive";
            }


            if (tScan) {
                tScan.className =
                    "tab active-scan";
            }

        }

    }



    /* =========================================================
       UPDATE UI
       ========================================================= */

    function updateUI() {

        const input =
            document.getElementById('userId');


        input.placeholder =
            'Enter your student ID or email';

    }



    /* =========================================================
       SIMULATE SCAN
       ========================================================= */

    function simulateScan() {

        const idInput =
            document.getElementById('userId')
                .value
                .trim();


        const role =
            document.getElementById('roleSelect')?.value
            || 'student';


        let targetUrl =
            '/student-dashboard';



        if (role === 'admin')
            targetUrl = '/admin-dashboard';

        else if (role === 'staff')
            targetUrl = '/staff-dashboard';

        else if (role === 'visitor')
            targetUrl = '/visitor-dashboard';

        else if (role === 'researcher')
            targetUrl = '/researcher-dashboard';



        if (idInput === "") {

            alert(
                "ACCESS DENIED: Something went wrong. We cannot find a library account linked to this scan. Please register first!"
            );

        }

        else {

            alert(
                'QR Code Scanned! Identity Verified.'
            );

            window.location.href =
                targetUrl;

        }

    }



    /* =========================================================
       PAGE LOAD
       ========================================================= */

    window.onload = updateUI;

</script>

</body>
</html>
