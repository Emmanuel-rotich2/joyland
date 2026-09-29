```php
<?php
require __DIR__ . '/../includes/bootstrap.php';

if (!empty($_SESSION['staff_id'])) {
    redirect('/fgck_joyland/staff/dashboard.php');
}

$error = '';
$login_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $q = $pdo->prepare("
        SELECT *
        FROM users
        WHERE username = ?
        AND status = 'active'
        LIMIT 1
    ");

    $q->execute([$username]);
    $u = $q->fetch();

    if ($u && password_verify($password, $u['password_hash'])) {

        // Regenerate session ID for security
        session_regenerate_id(true);

        // Store logged-in staff
        $_SESSION['staff_id'] = $u['id'];

        // Update last login
        $pdo->prepare("
            UPDATE users
            SET last_login_at = NOW()
            WHERE id = ?
        ")->execute([$u['id']]);

        // Record activity
        log_activity(
            $pdo,
            null,
            $u['id'],
            'staff_login',
            'Staff login'
        );

        $login_success = true;

    } else {

        $error = 'Invalid username or password.';
    }
}

$page_title = 'Pastor Login';

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

            <h1>Pastor Portal</h1>

            <p>
                FGCK Joyland Appointment Management
            </p>

        </div>

        <?php if ($error): ?>

            <div class="alert alert-danger small">
                <?= e($error) ?>
            </div>

        <?php endif; ?>

        <form method="post" id="loginForm">

            <input
                type="hidden"
                name="csrf"
                value="<?= e(csrf_token()) ?>"
            >

            <label class="form-label">
                Username
            </label>

            <input
                class="form-control mb-3"
                name="username"
                autocomplete="username"
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
                id="loginButton"
            >
                <i class="bi bi-box-arrow-in-right me-1"></i>
                Sign In
            </button>

        </form>

        <p class="small text-muted text-center mt-4 mb-0">
            <a href="/fgck_joyland/">
                Back to portal
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
                Opening your Pastor Dashboard...
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
        popup: 'joyland-login-popup'
    }

}).then(() => {

    window.location.href =
        '/fgck_joyland/staff/dashboard.php';

});


/*
 * Backup redirect.
 * This ensures the dashboard opens even if
 * the SweetAlert promise is interrupted.
 */
setTimeout(() => {

    window.location.href =
        '/fgck_joyland/staff/dashboard.php';

}, 3000);

</script>

<?php endif; ?>


<style>

/* FGCK Joyland Login Alert */

.joyland-login-popup {

    border-radius: 22px !important;

    padding: 2rem !important;

    box-shadow:
        0 20px 60px rgba(0, 0, 0, 0.18) !important;

    max-width: 430px !important;
}


/* SweetAlert title */

.joyland-login-popup .swal2-title {

    font-weight: 700 !important;

    font-size: 25px !important;

    color: #222 !important;

}


/* Green success icon */

.joyland-login-popup
.swal2-icon.swal2-success {

    border-color: #198754 !important;

    color: #198754 !important;

}


/* Success icon ring */

.joyland-login-popup
.swal2-success-ring {

    border-color:
        rgba(25, 135, 84, 0.25) !important;

}


/* Timer progress */

.joyland-login-popup
.swal2-timer-progress-bar {

    background: #198754 !important;

}


/* Login button */

#loginButton {

    transition:
        all 0.25s ease;

}


/* Prevent accidental double submission */

#loginButton:disabled {

    opacity: 0.75;

    cursor: not-allowed;

}

</style>


<script>

/*
 * Prevent multiple submissions while
 * the login request is being processed.
 */

const loginForm =
    document.getElementById('loginForm');

const loginButton =
    document.getElementById('loginButton');

if (loginForm) {

    loginForm.addEventListener('submit', function () {

        if (loginButton) {

            loginButton.disabled = true;

            loginButton.innerHTML = `
                <span
                    class="spinner-border spinner-border-sm me-2"
                    role="status"
                    aria-hidden="true">
                </span>
                Signing In...
            `;

        }

    });

}

</script>


<?php require __DIR__ . '/../includes/footer.php'; ?>
```
