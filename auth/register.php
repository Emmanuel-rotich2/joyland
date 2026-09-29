
<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if (!empty($_SESSION['member_id'])) {
    redirect('/fgck_joyland/member/dashboard.php');
}

$error = '';
$registration_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $n  = trim($_POST['full_name'] ?? '');
    $p  = trim($_POST['phone'] ?? '');
    $em = trim($_POST['email'] ?? '');
    $g  = trim($_POST['gender'] ?? '');
    $pw = $_POST['password'] ?? '';
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
            'Please use a valid name, phone number and email. ' .
            'Password must be at least 8 characters and include ' .
            'a letter and a number.';

    } elseif ($pw !== $cpw) {

        $error = 'Passwords do not match.';

    } else {

        try {

            /*
             * Generate membership number
             */
            $mem =
                'FGCK-' .
                date('Y') .
                '-' .
                strtoupper(bin2hex(random_bytes(3)));

            /*
             * Create member account
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
             * Secure session
             */
            session_regenerate_id(true);

            $_SESSION['member_id'] =
                (int) $pdo->lastInsertId();

            /*
             * Record registration activity
             */
            log_activity(
                $pdo,
                $_SESSION['member_id'],
                null,
                'registration',
                'Member account created'
            );

            /*
             * Show success popup instead of
             * immediately redirecting.
             */
            $registration_success = true;

        } catch (PDOException $e) {

            if ($e->getCode() === '23000') {

                $error =
                    'Email or phone number is already registered.';

            } else {

                $error =
                    'Registration could not be completed. Please try again.';
            }
        }
    }
}

$page_title = 'Member Registration';

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

            <h1>Join FGCK Joyland</h1>

            <p>Create your member account</p>

        </div>


        <?php if ($error): ?>

            <div class="alert alert-danger">

                <?= e($error) ?>

            </div>

        <?php endif; ?>


        <form
            method="post"
            id="registrationForm"
        >

            <input
                type="hidden"
                name="csrf"
                value="<?= e(csrf_token()) ?>"
            >


            <!-- Full Name -->

            <label class="form-label">
                Full name
            </label>

            <input
                class="form-control mb-3"
                name="full_name"
                value="<?= e($_POST['full_name'] ?? '') ?>"
                autocomplete="name"
                required
            >


            <div class="row">

                <!-- Phone -->

                <div class="col-6">

                    <label class="form-label">
                        Phone
                    </label>

                    <input
                        class="form-control mb-3"
                        name="phone"
                        value="<?= e($_POST['phone'] ?? '') ?>"
                        autocomplete="tel"
                        required
                    >

                </div>


                <!-- Gender -->

                <div class="col-6">

                    <label class="form-label">
                        Gender
                    </label>

                    <select
                        class="form-select mb-3"
                        name="gender"
                    >

                        <option value="">
                            Prefer not to say
                        </option>

                        <option
                            value="Male"
                            <?= (($_POST['gender'] ?? '') === 'Male')
                                ? 'selected'
                                : '' ?>
                        >
                            Male
                        </option>

                        <option
                            value="Female"
                            <?= (($_POST['gender'] ?? '') === 'Female')
                                ? 'selected'
                                : '' ?>
                        >
                            Female
                        </option>

                    </select>

                </div>

            </div>


            <!-- Email -->

            <label class="form-label">
                Email address
            </label>

            <input
                class="form-control mb-3"
                type="email"
                name="email"
                value="<?= e($_POST['email'] ?? '') ?>"
                autocomplete="email"
                required
            >


            <!-- Password -->

            <label class="form-label">
                Password
            </label>

            <input
                class="form-control mb-3"
                type="password"
                name="password"
                minlength="8"
                autocomplete="new-password"
                required
            >


            <!-- Confirm Password -->

            <label class="form-label">
                Confirm password
            </label>

            <input
                class="form-control mb-3"
                type="password"
                name="confirm_password"
                minlength="8"
                autocomplete="new-password"
                required
            >


            <!-- Submit -->

            <button
                type="submit"
                class="btn btn-primary w-100"
                id="registerButton"
            >

                <i class="bi bi-person-plus me-1"></i>

                Create Member Account

            </button>

        </form>


        <p class="text-center small text-muted mt-4">

            Already registered?

            <a href="/fgck_joyland/auth/login.php">
                Sign in
            </a>

        </p>

    </div>

</div>


<?php if ($registration_success): ?>

<script>

Swal.fire({

    icon: 'success',

    title: 'Welcome to FGCK Joyland!',

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
                Account Created Successfully
            </strong>

            <p
                style="
                    margin:0 0 6px;
                    color:#555;
                    font-size:15px;
                "
            >
                Your FGCK Joyland member account
                has been created successfully.
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
        popup: 'joyland-registration-popup'
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
   FGCK JOYLAND REGISTRATION ALERT
   ========================================= */

.joyland-registration-popup {

    border-radius: 22px !important;

    padding: 2rem !important;

    box-shadow:
        0 20px 60px rgba(0, 0, 0, 0.18) !important;

    max-width: 450px !important;

}


/* SweetAlert title */

.joyland-registration-popup .swal2-title {

    font-weight: 700 !important;

    font-size: 25px !important;

    color: #222 !important;

}


/* Green success icon */

.joyland-registration-popup
.swal2-icon.swal2-success {

    border-color: #198754 !important;

    color: #198754 !important;

}


/* Success ring */

.joyland-registration-popup
.swal2-success-ring {

    border-color:
        rgba(25, 135, 84, 0.25) !important;

}


/* Timer */

.joyland-registration-popup
.swal2-timer-progress-bar {

    background: #198754 !important;

}


/* Register button */

#registerButton {

    transition:
        all 0.25s ease;

}


/* Disabled button */

#registerButton:disabled {

    opacity: 0.75;

    cursor: not-allowed;

}

</style>


<script>

/*
 * Prevent accidental double registration.
 */

const registrationForm =
    document.getElementById('registrationForm');

const registerButton =
    document.getElementById('registerButton');


if (registrationForm) {

    registrationForm.addEventListener(
        'submit',
        function () {

            if (registerButton) {

                registerButton.disabled = true;

                registerButton.innerHTML = `
                    <span
                        class="spinner-border spinner-border-sm me-2"
                        role="status"
                        aria-hidden="true">
                    </span>

                    Creating Account...
                `;

            }

        }
    );

}

</script>


<?php
require __DIR__ . '/../includes/footer.php';
?>
```
