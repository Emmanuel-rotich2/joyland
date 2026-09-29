<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if (!empty($_SESSION['member_id'])) {
    redirect('/fgck_joyland/member/dashboard.php');
}

$error = '';
$login_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {

        $error = 'Please enter your email address and password.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = 'Please enter a valid email address.';

    } else {

        $q = $pdo->prepare("
            SELECT *
            FROM members
            WHERE email = ?
            AND status = 'active'
            LIMIT 1
        ");

        $q->execute([$email]);
        $m = $q->fetch();

        if ($m && password_verify($password, $m['password_hash'])) {

            /*
             * Regenerate session ID for security.
             */
            session_regenerate_id(true);

            /*
             * Store member session.
             */
            $_SESSION['member_id'] = (int) $m['id'];

            /*
             * Update last login.
             */
            $pdo->prepare("
                UPDATE members
                SET last_login_at = NOW()
                WHERE id = ?
            ")->execute([$m['id']]);

            /*
             * Record member activity.
             */
            log_activity(
                $pdo,
                $m['id'],
                null,
                'member_login',
                'Member login'
            );

            $login_success = true;

        } else {

            $error = 'The email or password you entered is incorrect.';
        }
    }
}

$page_title = 'Member Login';

require __DIR__ . '/../includes/header.php';
?>

<!-- SweetAlert2 -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>

/* =========================================================
   FGCK JOYLAND MEMBER PORTAL
   PROFESSIONAL AUTHENTICATION DESIGN
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

    --joy-bg: #f4f8f5;

}


/* =========================================================
   PAGE
   ========================================================= */

.member-auth-page {

    min-height: calc(100vh - 70px);

    display: flex;

    align-items: center;

    justify-content: center;

    padding: 35px 20px;

    position: relative;

    overflow: hidden;

    background:

        radial-gradient(
            circle at 5% 15%,
            rgba(8,116,67,.11),
            transparent 30%
        ),

        radial-gradient(
            circle at 95% 85%,
            rgba(217,164,65,.12),
            transparent 30%
        ),

        linear-gradient(
            135deg,
            #f9fcfa,
            #eef5f1
        );

}


/* Decorative background */

.member-auth-page::before {

    content: "";

    position: absolute;

    width: 400px;

    height: 400px;

    border-radius: 50%;

    top: -220px;

    right: -150px;

    border: 1px solid rgba(8,116,67,.08);

}


.member-auth-page::after {

    content: "";

    position: absolute;

    width: 330px;

    height: 330px;

    border-radius: 50%;

    bottom: -180px;

    left: -130px;

    border: 1px solid rgba(217,164,65,.12);

}


/* =========================================================
   MAIN CARD
   ========================================================= */

.member-auth-card {

    width: 100%;

    max-width: 1000px;

    min-height: 600px;

    display: grid;

    grid-template-columns:
        .95fr 1.05fr;

    background: #fff;

    border-radius: 30px;

    overflow: hidden;

    position: relative;

    z-index: 2;

    box-shadow:

        0 30px 80px rgba(14,55,36,.14),

        0 8px 25px rgba(0,0,0,.05);

    border: 1px solid rgba(255,255,255,.9);

}


/* =========================================================
   LEFT BRAND PANEL
   ========================================================= */

.member-brand-panel {

    position: relative;

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

    text-align: center;

    padding: 50px 40px;

    color: white;

    background:

        linear-gradient(
            145deg,
            #04552f 0%,
            #087443 55%,
            #0b8f55 100%
        );

}


/* Decorative rings */

.member-brand-panel::before {

    content: "";

    position: absolute;

    width: 300px;

    height: 300px;

    border-radius: 50%;

    top: -150px;

    left: -130px;

    border: 1px solid rgba(255,255,255,.09);

}


.member-brand-panel::after {

    content: "";

    position: absolute;

    width: 360px;

    height: 360px;

    border-radius: 50%;

    right: -180px;

    bottom: -190px;

    border: 1px solid rgba(217,164,65,.22);

}


/* Logo */

.member-logo {

    width: 125px;

    height: 125px;

    padding: 13px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 50%;

    background: rgba(255,255,255,.98);

    box-shadow:

        0 18px 40px rgba(0,0,0,.20);

    margin-bottom: 26px;

    position: relative;

    z-index: 2;

}


.member-logo img {

    width: 100%;

    height: 100%;

    object-fit: contain;

}


.member-brand-panel h1 {

    font-size: 30px;

    font-weight: 800;

    margin: 0 0 9px;

    letter-spacing: -.5px;

    position: relative;

    z-index: 2;

}


.member-brand-panel .brand-description {

    max-width: 300px;

    color: rgba(255,255,255,.82);

    font-size: 14px;

    line-height: 1.75;

    margin: 0 0 27px;

    position: relative;

    z-index: 2;

}


/* Member badge */

.member-badge {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    padding: 10px 18px;

    border-radius: 50px;

    background: rgba(217,164,65,.14);

    border: 1px solid rgba(217,164,65,.35);

    color: var(--joy-gold-light);

    font-size: 12px;

    font-weight: 800;

    position: relative;

    z-index: 2;

}


.member-badge i {

    font-size: 15px;

}


/* =========================================================
   RIGHT LOGIN PANEL
   ========================================================= */

.member-login-panel {

    padding: 55px 65px;

    display: flex;

    flex-direction: column;

    justify-content: center;

    background: white;

}


/* Heading */

.member-login-heading {

    margin-bottom: 30px;

}


.member-login-heading .eyebrow {

    display: inline-block;

    color: var(--joy-green);

    font-size: 11px;

    font-weight: 800;

    letter-spacing: 1.6px;

    text-transform: uppercase;

    margin-bottom: 9px;

}


.member-login-heading h2 {

    margin: 0 0 8px;

    color: var(--joy-text);

    font-size: 31px;

    font-weight: 800;

    letter-spacing: -.5px;

}


.member-login-heading p {

    margin: 0;

    color: var(--joy-muted);

    font-size: 14px;

    line-height: 1.65;

}


/* =========================================================
   ERROR
   ========================================================= */

.member-login-error {

    display: flex;

    align-items: flex-start;

    gap: 10px;

    padding: 13px 14px;

    margin-bottom: 21px;

    border-radius: 12px;

    background: #fff5f4;

    border: 1px solid #ffd8d4;

    color: #b42318;

    font-size: 13px;

    line-height: 1.5;

}

.member-login-error i {

    font-size: 17px;

}


/* =========================================================
   FORM
   ========================================================= */

.member-form-group {

    margin-bottom: 20px;

}


.member-form-label {

    display: block;

    margin-bottom: 8px;

    color: #34443b;

    font-size: 13px;

    font-weight: 700;

}


.member-input-wrapper {

    position: relative;

}


.member-input-icon {

    position: absolute;

    left: 16px;

    top: 50%;

    transform: translateY(-50%);

    color: #8b9991;

    font-size: 17px;

    pointer-events: none;

}


.member-input {

    width: 100%;

    height: 54px;

    border-radius: 13px;

    border: 1px solid var(--joy-border);

    background: #f9fbfa;

    padding: 0 48px;

    color: var(--joy-text);

    font-size: 14px;

    transition: all .25s ease;

}


.member-input::placeholder {

    color: #a0aaa5;

}


.member-input:hover {

    border-color: #c7d8cd;

}


.member-input:focus {

    outline: none;

    background: white;

    border-color: var(--joy-green);

    box-shadow:

        0 0 0 4px rgba(8,116,67,.09);

}


/* =========================================================
   PASSWORD TOGGLE
   ========================================================= */

.member-password-toggle {

    position: absolute;

    right: 12px;

    top: 50%;

    transform: translateY(-50%);

    width: 36px;

    height: 36px;

    border: 0;

    border-radius: 8px;

    background: transparent;

    color: #84928a;

    display: flex;

    align-items: center;

    justify-content: center;

    cursor: pointer;

    transition: .2s;

}


.member-password-toggle:hover {

    color: var(--joy-green);

    background: #edf5f0;

}


/* =========================================================
   SIGN IN BUTTON
   ========================================================= */

.member-login-button {

    width: 100%;

    height: 55px;

    border: 0;

    border-radius: 13px;

    color: white;

    font-size: 14px;

    font-weight: 800;

    background:

        linear-gradient(
            135deg,
            var(--joy-green),
            var(--joy-green-light)
        );

    box-shadow:

        0 10px 24px rgba(8,116,67,.22);

    transition:

        transform .2s ease,

        box-shadow .2s ease,

        opacity .2s ease;

}


.member-login-button:hover {

    color: white;

    transform: translateY(-2px);

    box-shadow:

        0 15px 30px rgba(8,116,67,.28);

}


.member-login-button:active {

    transform: translateY(0);

}


.member-login-button:disabled {

    opacity: .75;

    cursor: not-allowed;

    transform: none;

}


/* =========================================================
   SECURITY
   ========================================================= */

.member-security {

    display: flex;

    justify-content: center;

    align-items: center;

    gap: 7px;

    color: #89958e;

    font-size: 11px;

    margin-top: 18px;

}


.member-security i {

    color: var(--joy-green);

}


/* =========================================================
   REGISTER AREA
   ========================================================= */

.member-register {

    margin-top: 26px;

    padding-top: 21px;

    border-top: 1px solid #edf1ee;

    text-align: center;

    color: #7b8780;

    font-size: 13px;

}


.member-register a {

    color: var(--joy-green);

    font-weight: 800;

    text-decoration: none;

}


.member-register a:hover {

    color: var(--joy-green-dark);

    text-decoration: underline;

}


/* Back portal */

.member-back {

    text-align: center;

    margin-top: 18px;

}


.member-back a {

    color: #87928c;

    font-size: 12px;

    font-weight: 600;

    text-decoration: none;

    transition: .2s;

}


.member-back a:hover {

    color: var(--joy-green);

}


/* =========================================================
   SWEETALERT
   ========================================================= */

.joyland-member-login-popup {

    border-radius: 25px !important;

    padding: 35px !important;

    box-shadow:

        0 30px 80px rgba(0,0,0,.20) !important;

    max-width: 430px !important;

}


.joyland-member-login-popup .swal2-title {

    color: #18231d !important;

    font-size: 25px !important;

    font-weight: 800 !important;

}


.joyland-member-login-popup
.swal2-icon.swal2-success {

    border-color:
        var(--joy-green) !important;

    color:
        var(--joy-green) !important;

}


.joyland-member-login-popup
.swal2-success-ring {

    border-color:
        rgba(8,116,67,.20) !important;

}


.joyland-member-login-popup
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

@media (max-width: 850px) {

    .member-auth-card {

        max-width: 530px;

        min-height: auto;

        grid-template-columns: 1fr;

    }


    .member-brand-panel {

        padding: 40px 25px;

    }


    .member-logo {

        width: 100px;

        height: 100px;

        margin-bottom: 20px;

    }


    .member-brand-panel h1 {

        font-size: 25px;

    }


    .member-login-panel {

        padding: 42px 32px;

    }

}


@media (max-width: 480px) {

    .member-auth-page {

        padding: 16px 11px;

    }


    .member-auth-card {

        border-radius: 22px;

    }


    .member-brand-panel {

        padding: 32px 20px;

    }


    .member-login-panel {

        padding: 32px 21px;

    }


    .member-login-heading h2 {

        font-size: 26px;

    }


    .member-brand-panel h1 {

        font-size: 23px;

    }

}


/* Reduced motion */

@media (prefers-reduced-motion: reduce) {

    * {

        transition: none !important;

    }

}

</style>

<div class="member-auth-page">

<div class="member-auth-card">


    <!-- =================================================
         BRAND PANEL
         ================================================= -->

    <section class="member-brand-panel">

        <div class="member-logo">

            <img
                src="/fgck_joyland/assets/images/full_gospel_churches_logo.png"
                alt="FGCK Joyland Church Logo"
            >

        </div>


        <h1>
            FGCK Joyland
        </h1>


        <p class="brand-description">

            Welcome to your member portal.

            Book pastoral appointments, manage your
            conversations and stay connected with the ministry.

        </p>


        <div class="member-badge">

            <i class="bi bi-people-fill"></i>

            <span>
                Member Appointment Portal
            </span>

        </div>

    </section>


    <!-- =================================================
         LOGIN PANEL
         ================================================= -->

    <section class="member-login-panel">


        <div class="member-login-heading">

            <span class="eyebrow">
                Member Portal
            </span>


            <h2>
                Welcome Back
            </h2>


            <p>
                Sign in to continue to your FGCK Joyland
                member dashboard.
            </p>

        </div>


        <?php if ($error): ?>

            <div
                class="member-login-error"
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
            id="memberLoginForm"
            novalidate
        >

            <input
                type="hidden"
                name="csrf"
                value="<?= e(csrf_token()) ?>"
            >


            <!-- EMAIL -->

            <div class="member-form-group">

                <label
                    class="member-form-label"
                    for="memberEmail"
                >
                    Email address
                </label>


                <div class="member-input-wrapper">

                    <i
                        class="bi bi-envelope member-input-icon"
                        aria-hidden="true"
                    ></i>


                    <input
                        id="memberEmail"
                        class="member-input"
                        type="email"
                        name="email"
                        placeholder="Enter your email address"
                        autocomplete="email"
                        autocapitalize="none"
                        spellcheck="false"
                        value="<?= e($_POST['email'] ?? '') ?>"
                        required
                    >

                </div>

            </div>


            <!-- PASSWORD -->

            <div class="member-form-group">

                <label
                    class="member-form-label"
                    for="memberPassword"
                >
                    Password
                </label>


                <div class="member-input-wrapper">

                    <i
                        class="bi bi-lock member-input-icon"
                        aria-hidden="true"
                    ></i>


                    <input
                        id="memberPassword"
                        class="member-input"
                        type="password"
                        name="password"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required
                    >


                    <button
                        type="button"
                        class="member-password-toggle"
                        id="memberPasswordToggle"
                        aria-label="Show password"
                    >

                        <i
                            class="bi bi-eye"
                            id="memberPasswordIcon"
                        ></i>

                    </button>

                </div>

            </div>


            <!-- LOGIN -->

            <button
                type="submit"
                class="btn member-login-button"
                id="memberLoginButton"
            >

                <span id="memberLoginContent">

                    <i class="bi bi-box-arrow-in-right me-2"></i>

                    Sign In Securely

                </span>

            </button>


            <div class="member-security">

                <i class="bi bi-shield-lock-fill"></i>

                <span>
                    Your account is protected with secure authentication
                </span>

            </div>

        </form>


        <!-- REGISTER -->

        <div class="member-register">

            <span>
                New to FGCK Joyland?
            </span>

            <a href="/fgck_joyland/auth/register.php">

                Create your member account

            </a>

        </div>


        <!-- BACK -->

        <div class="member-back">

            <a href="/fgck_joyland/">

                <i class="bi bi-arrow-left me-1"></i>

                Back to FGCK Joyland Portal

            </a>

        </div>


    </section>

</div>

</div>

<?php if ($login_success): ?>

<script>

Swal.fire({

    icon: 'success',

    title: 'Welcome Back!',

    html: `

        <div style="
            margin-top:10px;
            line-height:1.7;
        ">

            <div style="
                width:65px;
                height:65px;
                margin:0 auto 15px;
                border-radius:50%;
                display:flex;
                align-items:center;
                justify-content:center;
                background:#edf8f2;
                color:#087443;
                font-size:27px;
            ">

                <i class="bi bi-person-check-fill"></i>

            </div>


            <strong style="
                color:#087443;
                font-size:19px;
                display:block;
                margin-bottom:6px;
            ">

                FGCK Joyland

            </strong>


            <p style="
                margin:0 0 5px;
                color:#66736c;
                font-size:14px;
            ">

                You have signed in successfully.

            </p>


            <small style="
                color:#9aa49f;
                font-size:12px;
            ">

                Opening your Member Dashboard...

            </small>

        </div>

    `,

    showConfirmButton: false,

    timer: 2500,

    timerProgressBar: true,

    allowOutsideClick: false,

    allowEscapeKey: false,

    allowEnterKey: false,

    customClass: {

        popup:
            'joyland-member-login-popup'

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

}, 2800);

</script>

<?php endif; ?>

<script>

/* =========================================================
   PASSWORD SHOW / HIDE
   ========================================================= */

const memberPassword =
    document.getElementById('memberPassword');

const memberPasswordToggle =
    document.getElementById('memberPasswordToggle');

const memberPasswordIcon =
    document.getElementById('memberPasswordIcon');


if (
    memberPassword &&
    memberPasswordToggle
) {

    memberPasswordToggle.addEventListener(
        'click',
        function () {

            const isHidden =
                memberPassword.type === 'password';


            memberPassword.type =
                isHidden
                    ? 'text'
                    : 'password';


            memberPasswordIcon.className =
                isHidden
                    ? 'bi bi-eye-slash'
                    : 'bi bi-eye';


            memberPasswordToggle.setAttribute(
                'aria-label',
                isHidden
                    ? 'Hide password'
                    : 'Show password'
            );

        }
    );

}


/* =========================================================
   PREVENT DOUBLE SUBMISSION
   ========================================================= */

const memberLoginForm =
    document.getElementById('memberLoginForm');

const memberLoginButton =
    document.getElementById('memberLoginButton');

const memberLoginContent =
    document.getElementById('memberLoginContent');


if (memberLoginForm) {

    memberLoginForm.addEventListener(
        'submit',
        function () {

            if (
                memberLoginButton &&
                memberLoginForm.checkValidity()
            ) {

                memberLoginButton.disabled = true;


                if (memberLoginContent) {

                    memberLoginContent.innerHTML = `

                        <span
                            class="spinner-border
                                   spinner-border-sm
                                   me-2"
                            role="status"
                            aria-hidden="true">
                        </span>

                        Signing In...

                    `;

                }

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

        const email =
            document.getElementById('memberEmail');

        if (
            email &&
            !email.value
        ) {

            email.focus();

        }

    }
);

</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
