
<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if (!empty($_SESSION['member_id'])) {
    redirect('/fgck_joyland/member/dashboard.php');
}

$error = '';
$registration_success = false;
$membership_no = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $n   = trim($_POST['full_name'] ?? '');
    $p   = trim($_POST['phone'] ?? '');
    $em  = trim($_POST['email'] ?? '');
    $g   = trim($_POST['gender'] ?? '');
    $pw  = $_POST['password'] ?? '';
    $cpw = $_POST['confirm_password'] ?? '';

    /*
     * Validate registration details
     */
    if (
        strlen($n) < 3 ||
        strlen($p) < 7 ||
        !filter_var($em, FILTER_VALIDATE_EMAIL) ||
        strlen($pw) < 8 ||
        !preg_match('/[A-Za-z]/', $pw) ||
        !preg_match('/[0-9]/', $pw)
    ) {

        $error =
            'Please provide valid registration details. ' .
            'Your password must contain at least 8 characters, ' .
            'including at least one letter and one number.';

    } elseif ($pw !== $cpw) {

        $error = 'The passwords do not match. Please check and try again.';

    } else {

        try {

            /*
             * Generate membership number.
             */
            $mem =
                'FGCK-' .
                date('Y') .
                '-' .
                strtoupper(bin2hex(random_bytes(3)));

            /*
             * Create member account.
             */
            $q = $pdo->prepare(
                'INSERT INTO members
                (
                    membership_no,
                    full_name,
                    phone,
                    email,
                    gender,
                    password_hash
                )
                VALUES (?, ?, ?, ?, ?, ?)'
            );

            $q->execute([
                $mem,
                $n,
                $p,
                $em,
                $g,
                password_hash($pw, PASSWORD_DEFAULT)
            ]);

            /*
             * Secure session.
             */
            session_regenerate_id(true);

            $_SESSION['member_id'] =
                (int) $pdo->lastInsertId();

            /*
             * Store membership number temporarily
             * for the success message.
             */
            $membership_no = $mem;

            /*
             * Record registration activity.
             */
            log_activity(
                $pdo,
                $_SESSION['member_id'],
                null,
                'registration',
                'Member account created'
            );

            $registration_success = true;

        } catch (PDOException $e) {

            if ($e->getCode() === '23000') {

                $error =
                    'That email address or phone number is already registered. ' .
                    'Please use different details or sign in to your existing account.';

            } else {

                $error =
                    'Registration could not be completed at this time. ' .
                    'Please try again.';
            }
        }
    }
}

$page_title = 'Create Member Account';

require __DIR__ . '/../includes/header.php';
?>

<!-- SweetAlert2 -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>

/* =========================================================
   FGCK JOYLAND
   PROFESSIONAL MEMBER REGISTRATION
   ========================================================= */

:root {

    --joy-green: #087443;
    --joy-green-dark: #04552f;
    --joy-green-light: #0b8f55;

    --joy-gold: #d9a441;
    --joy-gold-light: #f4d98b;

    --joy-text: #17221c;
    --joy-muted: #718078;

    --joy-border: #e2eae5;

}


/* =========================================================
   PAGE
   ========================================================= */

.member-register-page {

    min-height: calc(100vh - 70px);

    display: flex;

    justify-content: center;

    align-items: center;

    padding: 35px 20px;

    position: relative;

    overflow: hidden;

    background:

        radial-gradient(
            circle at 5% 10%,
            rgba(8,116,67,.11),
            transparent 30%
        ),

        radial-gradient(
            circle at 95% 90%,
            rgba(217,164,65,.12),
            transparent 30%
        ),

        linear-gradient(
            135deg,
            #f9fcfa,
            #eef5f1
        );

}


/* Decorative circles */

.member-register-page::before {

    content: "";

    position: absolute;

    width: 420px;

    height: 420px;

    border-radius: 50%;

    top: -230px;

    right: -170px;

    border: 1px solid rgba(8,116,67,.08);

}


.member-register-page::after {

    content: "";

    position: absolute;

    width: 330px;

    height: 330px;

    border-radius: 50%;

    bottom: -180px;

    left: -140px;

    border: 1px solid rgba(217,164,65,.12);

}


/* =========================================================
   MAIN CARD
   ========================================================= */

.member-register-card {

    width: 100%;

    max-width: 1050px;

    min-height: 650px;

    display: grid;

    grid-template-columns: .85fr 1.15fr;

    background: white;

    border-radius: 30px;

    overflow: hidden;

    position: relative;

    z-index: 2;

    box-shadow:

        0 30px 80px rgba(14,55,36,.14),

        0 8px 25px rgba(0,0,0,.05);

}


/* =========================================================
   BRAND PANEL
   ========================================================= */

.register-brand-panel {

    display: flex;

    flex-direction: column;

    justify-content: center;

    align-items: center;

    text-align: center;

    padding: 45px 38px;

    position: relative;

    color: white;

    overflow: hidden;

    background:

        linear-gradient(
            145deg,
            #04552f,
            #087443 58%,
            #0b8f55
        );

}


/* Decorative rings */

.register-brand-panel::before {

    content: "";

    position: absolute;

    width: 320px;

    height: 320px;

    border-radius: 50%;

    top: -170px;

    left: -150px;

    border: 1px solid rgba(255,255,255,.09);

}


.register-brand-panel::after {

    content: "";

    position: absolute;

    width: 380px;

    height: 380px;

    border-radius: 50%;

    right: -190px;

    bottom: -200px;

    border: 1px solid rgba(217,164,65,.23);

}


/* Logo */

.register-logo {

    width: 120px;

    height: 120px;

    padding: 13px;

    border-radius: 50%;

    background: white;

    display: flex;

    align-items: center;

    justify-content: center;

    box-shadow:

        0 18px 40px rgba(0,0,0,.20);

    margin-bottom: 25px;

    position: relative;

    z-index: 2;

}


.register-logo img {

    width: 100%;

    height: 100%;

    object-fit: contain;

}


.register-brand-panel h1 {

    font-size: 28px;

    font-weight: 800;

    margin: 0 0 10px;

    position: relative;

    z-index: 2;

}


.register-brand-description {

    max-width: 290px;

    color: rgba(255,255,255,.83);

    font-size: 14px;

    line-height: 1.75;

    margin-bottom: 28px;

    position: relative;

    z-index: 2;

}


/* Benefits */

.registration-benefits {

    width: 100%;

    max-width: 290px;

    text-align: left;

    position: relative;

    z-index: 2;

}


.registration-benefit {

    display: flex;

    align-items: center;

    gap: 11px;

    margin-bottom: 13px;

    color: rgba(255,255,255,.90);

    font-size: 12px;

}


.registration-benefit-icon {

    width: 30px;

    height: 30px;

    flex: 0 0 30px;

    border-radius: 9px;

    display: flex;

    align-items: center;

    justify-content: center;

    color: var(--joy-gold-light);

    background: rgba(217,164,65,.13);

    border: 1px solid rgba(217,164,65,.22);

}


/* =========================================================
   FORM PANEL
   ========================================================= */

.register-form-panel {

    padding: 48px 58px;

    display: flex;

    flex-direction: column;

    justify-content: center;

}


/* Heading */

.register-heading {

    margin-bottom: 25px;

}


.register-heading .eyebrow {

    display: inline-block;

    color: var(--joy-green);

    font-size: 11px;

    font-weight: 800;

    letter-spacing: 1.5px;

    text-transform: uppercase;

    margin-bottom: 8px;

}


.register-heading h2 {

    margin: 0 0 7px;

    color: var(--joy-text);

    font-size: 29px;

    font-weight: 800;

}


.register-heading p {

    margin: 0;

    color: var(--joy-muted);

    font-size: 13px;

    line-height: 1.6;

}


/* =========================================================
   ERROR
   ========================================================= */

.registration-error {

    display: flex;

    gap: 10px;

    align-items: flex-start;

    padding: 12px 14px;

    margin-bottom: 20px;

    border-radius: 12px;

    background: #fff5f4;

    border: 1px solid #ffd8d4;

    color: #b42318;

    font-size: 12px;

    line-height: 1.5;

}


.registration-error i {

    font-size: 17px;

}


/* =========================================================
   FORM
   ========================================================= */

.register-field {

    margin-bottom: 17px;

}


.register-label {

    display: block;

    margin-bottom: 7px;

    color: #34443b;

    font-size: 12px;

    font-weight: 700;

}


.register-input-wrapper {

    position: relative;

}


.register-input-icon {

    position: absolute;

    left: 15px;

    top: 50%;

    transform: translateY(-50%);

    color: #8a9890;

    font-size: 16px;

    pointer-events: none;

    z-index: 2;

}


.register-input,
.register-select {

    width: 100%;

    height: 49px;

    border-radius: 12px;

    border: 1px solid var(--joy-border);

    background: #f9fbfa;

    color: var(--joy-text);

    font-size: 13px;

    padding-left: 44px;

    transition: all .25s ease;

}


.register-select {

    padding-right: 38px;

}


.register-input::placeholder {

    color: #a1aba6;

}


.register-input:hover,
.register-select:hover {

    border-color: #c6d7cd;

}


.register-input:focus,
.register-select:focus {

    outline: none;

    background: white;

    border-color: var(--joy-green);

    box-shadow:

        0 0 0 4px rgba(8,116,67,.08);

}


/* =========================================================
   PASSWORD TOGGLE
   ========================================================= */

.register-password-toggle {

    position: absolute;

    right: 10px;

    top: 50%;

    transform: translateY(-50%);

    width: 34px;

    height: 34px;

    border: 0;

    border-radius: 8px;

    background: transparent;

    color: #87938d;

    display: flex;

    align-items: center;

    justify-content: center;

    cursor: pointer;

}


.register-password-toggle:hover {

    background: #edf5f0;

    color: var(--joy-green);

}


/* Password input needs right padding */

.password-input {

    padding-right: 48px;

}


/* =========================================================
   PASSWORD STRENGTH
   ========================================================= */

.password-strength {

    margin-top: 8px;

}


.password-strength-bar {

    width: 100%;

    height: 4px;

    background: #e9eeeb;

    border-radius: 10px;

    overflow: hidden;

}


.password-strength-fill {

    width: 0%;

    height: 100%;

    border-radius: 10px;

    transition: all .3s ease;

}


.password-strength-text {

    margin-top: 5px;

    font-size: 10px;

    color: #8a958f;

}


.password-requirements {

    display: flex;

    flex-wrap: wrap;

    gap: 5px 12px;

    margin-top: 7px;

}


.password-requirement {

    color: #8a958f;

    font-size: 10px;

}


.password-requirement.valid {

    color: var(--joy-green);

}


.password-requirement i {

    margin-right: 3px;

}


/* =========================================================
   SUBMIT
   ========================================================= */

.register-submit {

    width: 100%;

    height: 52px;

    border: 0;

    border-radius: 12px;

    color: white;

    background:

        linear-gradient(
            135deg,
            var(--joy-green),
            var(--joy-green-light)
        );

    font-size: 13px;

    font-weight: 800;

    box-shadow:

        0 10px 22px rgba(8,116,67,.21);

    transition: all .2s ease;

    margin-top: 5px;

}


.register-submit:hover {

    color: white;

    transform: translateY(-2px);

    box-shadow:

        0 15px 28px rgba(8,116,67,.27);

}


.register-submit:active {

    transform: translateY(0);

}


.register-submit:disabled {

    opacity: .75;

    cursor: not-allowed;

    transform: none;

}


/* =========================================================
   LOGIN LINK
   ========================================================= */

.register-login {

    text-align: center;

    margin-top: 20px;

    padding-top: 17px;

    border-top: 1px solid #edf1ee;

    color: #7b8780;

    font-size: 12px;

}


.register-login a {

    color: var(--joy-green);

    font-weight: 800;

    text-decoration: none;

}


.register-login a:hover {

    text-decoration: underline;

}


/* Back portal */

.register-back {

    text-align: center;

    margin-top: 13px;

}


.register-back a {

    color: #8a958f;

    font-size: 11px;

    font-weight: 600;

    text-decoration: none;

}


.register-back a:hover {

    color: var(--joy-green);

}


/* =========================================================
   SWEETALERT
   ========================================================= */

.joyland-registration-popup {

    border-radius: 25px !important;

    padding: 35px !important;

    max-width: 460px !important;

    box-shadow:

        0 30px 80px rgba(0,0,0,.20) !important;

}


.joyland-registration-popup .swal2-title {

    color: #18231d !important;

    font-size: 25px !important;

    font-weight: 800 !important;

}


.joyland-registration-popup
.swal2-icon.swal2-success {

    border-color:
        var(--joy-green) !important;

    color:
        var(--joy-green) !important;

}


.joyland-registration-popup
.swal2-success-ring {

    border-color:
        rgba(8,116,67,.20) !important;

}


.joyland-registration-popup
.swal2-timer-progress-bar {

    background:

        linear-gradient(
            90deg,
            var(--joy-green),
            var(--joy-gold)
        ) !important;

}


/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 900px) {

    .member-register-card {

        max-width: 600px;

        grid-template-columns: 1fr;

        min-height: auto;

    }


    .register-brand-panel {

        padding: 38px 25px;

    }


    .register-logo {

        width: 95px;

        height: 95px;

        margin-bottom: 18px;

    }


    .register-brand-panel h1 {

        font-size: 24px;

    }


    .registration-benefits {

        display: none;

    }


    .register-form-panel {

        padding: 40px 32px;

    }

}


@media (max-width: 500px) {

    .member-register-page {

        padding: 15px 10px;

    }


    .member-register-card {

        border-radius: 22px;

    }


    .register-brand-panel {

        padding: 30px 20px;

    }


    .register-form-panel {

        padding: 30px 20px;

    }


    .register-heading h2 {

        font-size: 25px;

    }

}


/* Reduced motion */

@media (prefers-reduced-motion: reduce) {

    * {

        transition: none !important;

    }

}

</style>

<div class="member-register-page">

<div class="member-register-card">


    <!-- =================================================
         BRAND / INFORMATION PANEL
         ================================================= -->

    <section class="register-brand-panel">

        <div class="register-logo">

            <img
                src="/fgck_joyland/assets/images/full_gospel_churches_logo.png"
                alt="FGCK Joyland Church Logo"
            >

        </div>


        <h1>
            Join FGCK Joyland
        </h1>


        <p class="register-brand-description">

            Create your member account and stay connected
            with the ministry through our secure digital
            member portal.

        </p>


        <div class="registration-benefits">

            <div class="registration-benefit">

                <span class="registration-benefit-icon">

                    <i class="bi bi-calendar-check"></i>

                </span>

                <span>
                    Book pastoral appointments easily
                </span>

            </div>


            <div class="registration-benefit">

                <span class="registration-benefit-icon">

                    <i class="bi bi-person-heart"></i>

                </span>

                <span>
                    Stay connected with pastoral ministry
                </span>

            </div>


            <div class="registration-benefit">

                <span class="registration-benefit-icon">

                    <i class="bi bi-shield-check"></i>

                </span>

                <span>
                    Your account is securely protected
                </span>

            </div>


            <div class="registration-benefit">

                <span class="registration-benefit-icon">

                    <i class="bi bi-stars"></i>

                </span>

                <span>
                    Experience a connected church community
                </span>

            </div>

        </div>

    </section>


    <!-- =================================================
         REGISTRATION FORM
         ================================================= -->

    <section class="register-form-panel">


        <div class="register-heading">

            <span class="eyebrow">
                Member Registration
            </span>


            <h2>
                Create Your Account
            </h2>


            <p>
                Enter your details below to get started
                with FGCK Joyland's member portal.
            </p>

        </div>


        <?php if ($error): ?>

            <div
                class="registration-error"
                role="alert"
            >

                <i class="bi bi-exclamation-circle-fill"></i>

                <div>
                    <?= e($error) ?>
                </div>

            </div>

        <?php endif; ?>


        <form
            method="post"
            id="registrationForm"
            novalidate
        >

            <input
                type="hidden"
                name="csrf"
                value="<?= e(csrf_token()) ?>"
            >


            <!-- FULL NAME -->

            <div class="register-field">

                <label
                    class="register-label"
                    for="fullName"
                >
                    Full name
                </label>


                <div class="register-input-wrapper">

                    <i
                        class="bi bi-person register-input-icon"
                        aria-hidden="true"
                    ></i>


                    <input
                        id="fullName"
                        class="register-input"
                        name="full_name"
                        type="text"
                        placeholder="Enter your full name"
                        value="<?= e($_POST['full_name'] ?? '') ?>"
                        autocomplete="name"
                        minlength="3"
                        required
                    >

                </div>

            </div>


            <!-- PHONE + GENDER -->

            <div class="row g-3">

                <div class="col-md-6">

                    <div class="register-field">

                        <label
                            class="register-label"
                            for="phone"
                        >
                            Phone number
                        </label>


                        <div class="register-input-wrapper">

                            <i
                                class="bi bi-telephone register-input-icon"
                                aria-hidden="true"
                            ></i>


                            <input
                                id="phone"
                                class="register-input"
                                name="phone"
                                type="tel"
                                placeholder="e.g. 0712345678"
                                value="<?= e($_POST['phone'] ?? '') ?>"
                                autocomplete="tel"
                                minlength="7"
                                required
                            >

                        </div>

                    </div>

                </div>


                <div class="col-md-6">

                    <div class="register-field">

                        <label
                            class="register-label"
                            for="gender"
                        >
                            Gender
                        </label>


                        <div class="register-input-wrapper">

                            <i
                                class="bi bi-people register-input-icon"
                                aria-hidden="true"
                            ></i>


                            <select
                                id="gender"
                                class="register-select"
                                name="gender"
                            >

                                <option value="">
                                    Prefer not to say
                                </option>

                                <option
                                    value="Male"
                                    <?= (
                                        ($_POST['gender'] ?? '') === 'Male'
                                    )
                                        ? 'selected'
                                        : ''
                                    ?>
                                >
                                    Male
                                </option>

                                <option
                                    value="Female"
                                    <?= (
                                        ($_POST['gender'] ?? '') === 'Female'
                                    )
                                        ? 'selected'
                                        : ''
                                    ?>
                                >
                                    Female
                                </option>

                            </select>

                        </div>

                    </div>

                </div>

            </div>


            <!-- EMAIL -->

            <div class="register-field">

                <label
                    class="register-label"
                    for="email"
                >
                    Email address
                </label>


                <div class="register-input-wrapper">

                    <i
                        class="bi bi-envelope register-input-icon"
                        aria-hidden="true"
                    ></i>


                    <input
                        id="email"
                        class="register-input"
                        type="email"
                        name="email"
                        placeholder="Enter your email address"
                        value="<?= e($_POST['email'] ?? '') ?>"
                        autocomplete="email"
                        required
                    >

                </div>

            </div>


            <!-- PASSWORD -->

            <div class="register-field">

                <label
                    class="register-label"
                    for="password"
                >
                    Create password
                </label>


                <div class="register-input-wrapper">

                    <i
                        class="bi bi-lock register-input-icon"
                        aria-hidden="true"
                    ></i>


                    <input
                        id="password"
                        class="register-input password-input"
                        type="password"
                        name="password"
                        minlength="8"
                        autocomplete="new-password"
                        placeholder="Create a secure password"
                        required
                    >


                    <button
                        type="button"
                        class="register-password-toggle"
                        id="passwordToggle"
                        aria-label="Show password"
                    >

                        <i
                            class="bi bi-eye"
                            id="passwordIcon"
                        ></i>

                    </button>

                </div>


                <!-- Strength -->

                <div class="password-strength">

                    <div class="password-strength-bar">

                        <div
                            class="password-strength-fill"
                            id="passwordStrengthFill"
                        ></div>

                    </div>


                    <div
                        class="password-strength-text"
                        id="passwordStrengthText"
                    >
                        Use at least 8 characters, including
                        a letter and a number.
                    </div>


                    <div class="password-requirements">

                        <span
                            class="password-requirement"
                            id="lengthRequirement"
                        >

                            <i class="bi bi-circle"></i>

                            8+ characters

                        </span>


                        <span
                            class="password-requirement"
                            id="letterRequirement"
                        >

                            <i class="bi bi-circle"></i>

                            Letter

                        </span>


                        <span
                            class="password-requirement"
                            id="numberRequirement"
                        >

                            <i class="bi bi-circle"></i>

                            Number

                        </span>

                    </div>

                </div>

            </div>


            <!-- CONFIRM PASSWORD -->

            <div class="register-field">

                <label
                    class="register-label"
                    for="confirmPassword"
                >
                    Confirm password
                </label>


                <div class="register-input-wrapper">

                    <i
                        class="bi bi-shield-lock register-input-icon"
                        aria-hidden="true"
                    ></i>


                    <input
                        id="confirmPassword"
                        class="register-input password-input"
                        type="password"
                        name="confirm_password"
                        minlength="8"
                        autocomplete="new-password"
                        placeholder="Confirm your password"
                        required
                    >


                    <button
                        type="button"
                        class="register-password-toggle"
                        id="confirmPasswordToggle"
                        aria-label="Show password"
                    >

                        <i
                            class="bi bi-eye"
                            id="confirmPasswordIcon"
                        ></i>

                    </button>

                </div>

            </div>


            <!-- SUBMIT -->

            <button
                type="submit"
                class="btn register-submit"
                id="registerButton"
            >

                <span id="registerButtonContent">

                    <i class="bi bi-person-plus-fill me-2"></i>

                    Create Member Account

                </span>

            </button>

        </form>


        <!-- LOGIN -->

        <div class="register-login">

            Already registered?

            <a href="/fgck_joyland/auth/login.php">
                Sign in to your account
            </a>

        </div>


        <!-- BACK -->

        <div class="register-back">

            <a href="/fgck_joyland/">

                <i class="bi bi-arrow-left me-1"></i>

                Back to FGCK Joyland Portal

            </a>

        </div>


    </section>

</div>

</div>

<?php if ($registration_success): ?>

<script>

Swal.fire({

    icon: 'success',

    title: 'Welcome to FGCK Joyland!',

    html: `

        <div style="
            margin-top:10px;
            line-height:1.7;
        ">

            <div style="
                width:68px;
                height:68px;
                margin:0 auto 15px;
                border-radius:50%;
                display:flex;
                align-items:center;
                justify-content:center;
                background:#edf8f2;
                color:#087443;
                font-size:28px;
            ">

                <i class="bi bi-person-check-fill"></i>

            </div>


            <strong style="
                color:#087443;
                font-size:19px;
                display:block;
                margin-bottom:7px;
            ">

                Account Created Successfully

            </strong>


            <p style="
                margin:0 0 7px;
                color:#66736c;
                font-size:14px;
            ">

                Your FGCK Joyland member account
                is ready.

            </p>


            <div style="
                display:inline-block;
                margin:5px 0 10px;
                padding:7px 14px;
                border-radius:8px;
                background:#f3f8f5;
                color:#087443;
                font-size:13px;
                font-weight:700;
            ">

                Membership No:
                <?= e($membership_no) ?>

            </div>


            <small style="
                color:#9aa49f;
                font-size:12px;
                display:block;
            ">

                Opening your Member Dashboard...

            </small>

        </div>

    `,

    showConfirmButton: false,

    timer: 3500,

    timerProgressBar: true,

    allowOutsideClick: false,

    allowEscapeKey: false,

    allowEnterKey: false,

    customClass: {

        popup:
            'joyland-registration-popup'

    }

}).then(() => {

    window.location.href =
        '/fgck_joyland/member/dashboard.php';

});


/*
 * Backup redirect.
 */

setTimeout(() => {

    window.location.href =
        '/fgck_joyland/member/dashboard.php';

}, 3800);

</script>

<?php endif; ?>

<script>

/* =========================================================
   PASSWORD VISIBILITY
   ========================================================= */

function setupPasswordToggle(
    inputId,
    buttonId,
    iconId
) {

    const input =
        document.getElementById(inputId);

    const button =
        document.getElementById(buttonId);

    const icon =
        document.getElementById(iconId);


    if (!input || !button || !icon) {
        return;
    }


    button.addEventListener(
        'click',
        function () {

            const hidden =
                input.type === 'password';


            input.type =
                hidden
                    ? 'text'
                    : 'password';


            icon.className =
                hidden
                    ? 'bi bi-eye-slash'
                    : 'bi bi-eye';


            button.setAttribute(
                'aria-label',
                hidden
                    ? 'Hide password'
                    : 'Show password'
            );

        }
    );

}


setupPasswordToggle(
    'password',
    'passwordToggle',
    'passwordIcon'
);


setupPasswordToggle(
    'confirmPassword',
    'confirmPasswordToggle',
    'confirmPasswordIcon'
);


/* =========================================================
   PASSWORD STRENGTH
   ========================================================= */

const password =
    document.getElementById('password');

const strengthFill =
    document.getElementById(
        'passwordStrengthFill'
    );

const strengthText =
    document.getElementById(
        'passwordStrengthText'
    );

const lengthRequirement =
    document.getElementById(
        'lengthRequirement'
    );

const letterRequirement =
    document.getElementById(
        'letterRequirement'
    );

const numberRequirement =
    document.getElementById(
        'numberRequirement'
    );


function updateRequirement(
    element,
    valid
) {

    if (!element) {
        return;
    }


    const icon =
        element.querySelector('i');


    if (valid) {

        element.classList.add('valid');

        if (icon) {

            icon.className =
                'bi bi-check-circle-fill';

        }

    } else {

        element.classList.remove('valid');

        if (icon) {

            icon.className =
                'bi bi-circle';

        }

    }

}


if (password) {

    password.addEventListener(
        'input',
        function () {

            const value =
                password.value;


            const hasLength =
                value.length >= 8;

            const hasLetter =
                /[A-Za-z]/.test(value);

            const hasNumber =
                /[0-9]/.test(value);


            updateRequirement(
                lengthRequirement,
                hasLength
            );


            updateRequirement(
                letterRequirement,
                hasLetter
            );


            updateRequirement(
                numberRequirement,
                hasNumber
            );


            let score = 0;

            if (hasLength) score++;

            if (hasLetter) score++;

            if (hasNumber) score++;

            if (value.length >= 12) score++;


            const percentages = [
                0,
                25,
                50,
                75,
                100
            ];


            strengthFill.style.width =
                percentages[score] + '%';


            if (!value) {

                strengthText.textContent =
                    'Use at least 8 characters, including a letter and a number.';

            } else if (score <= 1) {

                strengthText.textContent =
                    'Password is weak.';

            } else if (score === 2) {

                strengthText.textContent =
                    'Password is getting stronger.';

            } else if (score === 3) {

                strengthText.textContent =
                    'Good password.';

            } else {

                strengthText.textContent =
                    'Strong password.';

            }

        }
    );

}


/* =========================================================
   PREVENT DOUBLE REGISTRATION
   ========================================================= */

const registrationForm =
    document.getElementById(
        'registrationForm'
    );

const registerButton =
    document.getElementById(
        'registerButton'
    );

const registerButtonContent =
    document.getElementById(
        'registerButtonContent'
    );


if (registrationForm) {

    registrationForm.addEventListener(
        'submit',
        function () {

            if (
                !registrationForm.checkValidity()
            ) {

                return;

            }


            if (registerButton) {

                registerButton.disabled = true;

            }


            if (registerButtonContent) {

                registerButtonContent.innerHTML = `

                    <span
                        class="spinner-border
                               spinner-border-sm
                               me-2"
                        role="status"
                        aria-hidden="true">
                    </span>

                    Creating Account...

                `;

            }

        }
    );

}


/* =========================================================
   CONFIRM PASSWORD VISUAL CHECK
   ========================================================= */

const confirmPassword =
    document.getElementById(
        'confirmPassword'
    );


if (
    confirmPassword &&
    password
) {

    confirmPassword.addEventListener(
        'input',
        function () {

            if (
                confirmPassword.value &&
                password.value ===
                confirmPassword.value
            ) {

                confirmPassword.style.borderColor =
                    '#087443';

            } else if (
                confirmPassword.value
            ) {

                confirmPassword.style.borderColor =
                    '#dc3545';

            } else {

                confirmPassword.style.borderColor =
                    '';

            }

        }
    );

}


/* =========================================================
   AUTO FOCUS
   ========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const fullName =
            document.getElementById(
                'fullName'
            );


        if (
            fullName &&
            !fullName.value
        ) {

            fullName.focus();

        }

    }
);

</script>

<?php
require __DIR__ . '/../includes/footer.php';
?>
