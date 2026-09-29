```php
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

        // Regenerate session ID for security
        session_regenerate_id(true);

        // Store member session
        $_SESSION['member_id'] = (int) $m['id'];

        // Update last login
        $pdo->prepare("
            UPDATE members
            SET last_login_at = NOW()
            WHERE id = ?
        ")->execute([$m['id']]);

        // Record activity
        log_activity(
            $pdo,
            $m['id'],
            null,
            'member_login',
            'Member login'
        );

        $login_success = true;

    } else {

        $error = 'The email or password is incorrect.';
    }
}

$page_title = 'Member Login';

require __DIR__ . '/../includes/header.php';
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="auth-page">

    <div class="auth-card">

        <div class="auth-logo">

            <div class="brand-logo">
                <img
                    src="/fgck_joyland/assets/images/full_gospel_churches_logo.png"
                    alt="FGCK Joyland"
                >
            </div>

            <h1>FGCK Joyland</h1>

            <p>Member Appointment Portal</p>

        </div>


        <?php if ($error): ?>

            <div class="alert alert-danger">
                <?= e($error) ?>
            </div>

        <?php endif; ?>


        <form method="post" id="memberLoginForm">

            <input
                type="hidden"
                name="csrf"
                value="<?= e(csrf_token()) ?>"
            >

            <label class="form-label">
                Email address
            </label>

            <input
                class="form-control mb-3"
                type="email"
                name="email"
                autocomplete="email"
                required
            >


            <label class="form-label">
                Password
            </label>

            <input
                class="form-control mb-3"
                type="password"
                name="password"
                autocomplete="current-password"
                required
            >


            <button
                type="submit"
                class="btn btn-primary w-100"
                id="memberLoginButton"
            >
                <i class="bi bi-box-arrow-in-right me-1"></i>
                Sign In
            </button>

        </form>


        <p class="text-center small text-muted mt-4">

            New member?

            <a href="/fgck_joyland/auth/register.php">
                Create an account
            </a>

        </p>

    </div>

</div>


<?php if ($login_success): ?>

<script>

Swal.fire({

    icon: 'success',

    title: 'Welcome Back!',

    html: `
        <div style="margin-top:10px;">

            <strong
                style="
                    color:#198754;
                    font-size:20px;
                    display:block;
                    margin-bottom:8px;
                "
            >
                FGCK Joyland
            </strong>

            <p
                style="
                    margin:0 0 6px;
                    color:#555;
                    font-size:15px;
                "
            >
                You have logged in successfully.
            </p>

            <small style="color:#999;">
                Opening your Member Dashboard...
            </small>

        </div>
    `,

    showConfirmButton: false,

    timer: 3000,

    timerProgressBar: true,

    allowOutsideClick: false,

    allowEscapeKey: false,

    allowEnterKey: false,

    customClass: {
        popup: 'joyland-member-login-popup'
    }

}).then(() => {

    window.location.href =
        '/fgck_joyland/member/dashboard.php';

});


/*
 * Backup redirect after 3 seconds.
 */
setTimeout(() => {

    window.location.href =
        '/fgck_joyland/member/dashboard.php';

}, 3000);

</script>

<?php endif; ?>


<style>

/* =========================================
   FGCK JOYLAND MEMBER LOGIN ALERT
   ========================================= */

.joyland-member-login-popup {

    border-radius: 22px !important;

    padding: 2rem !important;

    box-shadow:
        0 20px 60px rgba(0, 0, 0, 0.18) !important;

    max-width: 430px !important;
}


/* SweetAlert title */

.joyland-member-login-popup .swal2-title {

    font-weight: 700 !important;

    font-size: 25px !important;

    color: #222 !important;

}


/* Green success icon */

.joyland-member-login-popup
.swal2-icon.swal2-success {

    border-color: #198754 !important;

    color: #198754 !important;

}


/* Success ring */

.joyland-member-login-popup
.swal2-success-ring {

    border-color:
        rgba(25, 135, 84, 0.25) !important;

}


/* Progress bar */

.joyland-member-login-popup
.swal2-timer-progress-bar {

    background: #198754 !important;

}


/* Login button animation */

#memberLoginButton {

    transition:
        all 0.25s ease;

}


/* Disabled button */

#memberLoginButton:disabled {

    opacity: 0.75;

    cursor: not-allowed;

}

</style>


<script>

/*
 * Prevent multiple login submissions.
 */

const memberLoginForm =
    document.getElementById('memberLoginForm');

const memberLoginButton =
    document.getElementById('memberLoginButton');


if (memberLoginForm) {

    memberLoginForm.addEventListener(
        'submit',
        function () {

            if (memberLoginButton) {

                memberLoginButton.disabled = true;

                memberLoginButton.innerHTML = `
                    <span
                        class="spinner-border spinner-border-sm me-2"
                        role="status"
                        aria-hidden="true">
                    </span>

                    Signing In...
                `;

            }

        }
    );

}

</script>


<?php require __DIR__ . '/../includes/footer.php'; ?>
```
