<?php

require __DIR__ . '/../includes/bootstrap.php';

if (!empty($_SESSION['staff_id'])) {
    redirect('/fgck_joyland/staff/dashboard.php');
}

if (empty($_SESSION['pending_staff_id'])) {
    redirect('/fgck_joyland/staff/login.php');
}

$error = '';
$success = false;

$pending_staff_id = (int) $_SESSION['pending_staff_id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $otp = trim($_POST['otp'] ?? '');

    if (!preg_match('/^[0-9]{6}$/', $otp)) {

        $error = 'Please enter the 6-digit verification code.';

    } else {

        /*
         * Get latest active OTP.
         */
        $q = $pdo->prepare("
            SELECT *
            FROM staff_login_otps
            WHERE user_id = ?
            AND used_at IS NULL
            ORDER BY id DESC
            LIMIT 1
        ");

        $q->execute([
            $pending_staff_id
        ]);

        $record = $q->fetch();


        if (!$record) {

            $error =
                'This verification code is no longer valid. Please request a new code.';

        } elseif (
            strtotime($record['expires_at']) < time()
        ) {

            $error =
                'Your verification code has expired. Please request a new code.';

        } elseif ((int)$record['attempts'] >= 5) {

            $error =
                'Too many incorrect attempts. Please request a new code.';

        } else {

            /*
             * Count attempt.
             */
            $pdo->prepare("
                UPDATE staff_login_otps
                SET attempts = attempts + 1
                WHERE id = ?
            ")->execute([
                $record['id']
            ]);


            /*
             * Verify OTP.
             */
            if (!password_verify(
                $otp,
                $record['otp_hash']
            )) {

                $remaining =
                    4 - (int)$record['attempts'];

                if ($remaining > 0) {

                    $error =
                        'Incorrect verification code. ' .
                        $remaining .
                        ' attempt(s) remaining.';

                } else {

                    $error =
                        'Incorrect verification code. Please request a new code.';
                }

            } else {

                /*
                 * Mark OTP as used.
                 */
                $pdo->prepare("
                    UPDATE staff_login_otps
                    SET used_at = NOW()
                    WHERE id = ?
                ")->execute([
                    $record['id']
                ]);


                /*
                 * Retrieve staff account.
                 */
                $userQuery = $pdo->prepare("
                    SELECT *
                    FROM users
                    WHERE id = ?
                    AND status = 'active'
                    LIMIT 1
                ");

                $userQuery->execute([
                    $pending_staff_id
                ]);

                $u = $userQuery->fetch();


                if (!$u) {

                    unset(
                        $_SESSION['pending_staff_id']
                    );

                    $error =
                        'Your account could not be verified.';

                } else {

                    /*
                     * Create authenticated session.
                     */
                    session_regenerate_id(true);

                    $_SESSION['staff_id'] =
                        $u['id'];


                    /*
                     * Remove temporary login session.
                     */
                    unset(
                        $_SESSION['pending_staff_id']
                    );


                    /*
                     * Update last login.
                     */
                    $pdo->prepare("
                        UPDATE users
                        SET last_login_at = NOW()
                        WHERE id = ?
                    ")->execute([
                        $u['id']
                    ]);


                    /*
                     * Activity log.
                     */
                    log_activity(
                        $pdo,
                        null,
                        $u['id'],
                        'staff_login',
                        'Staff login with email verification'
                    );


                    /*
                     * Remove remaining OTPs.
                     */
                    $pdo->prepare("
                        DELETE FROM staff_login_otps
                        WHERE user_id = ?
                    ")->execute([
                        $u['id']
                    ]);


                    /*
                     * Mark verification as successful.
                     */
                    $success = true;
                }
            }
        }
    }
}

$page_title = 'Verify Pastor Login';

require __DIR__ . '/../includes/header.php';

?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<style>

    .otp-page {
        min-height: calc(100vh - 70px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 35px 15px;

        background:
            radial-gradient(
                circle at 10% 10%,
                rgba(37, 99, 235, .10),
                transparent 30%
            ),
            radial-gradient(
                circle at 90% 90%,
                rgba(15, 42, 85, .10),
                transparent 30%
            ),
            linear-gradient(
                135deg,
                #f8fafc,
                #eef4ff
            );
    }


    .otp-card {
        width: 100%;
        max-width: 500px;

        background: #fff;

        border-radius: 24px;

        overflow: hidden;

        box-shadow:
            0 25px 70px rgba(15, 42, 85, .15);
    }


    .otp-header {
        text-align: center;

        padding: 35px 25px;

        background:
            linear-gradient(
                135deg,
                #0f2a55,
                #2563eb
            );

        color: #fff;
    }


    .otp-icon {
        width: 70px;
        height: 70px;

        border-radius: 50%;

        background: rgba(255,255,255,.15);

        display: flex;
        align-items: center;
        justify-content: center;

        margin: 0 auto 15px;

        font-size: 28px;
    }


    .otp-header h3 {
        font-weight: 800;
        margin-bottom: 5px;
    }


    .otp-header p {
        margin: 0;
        opacity: .75;
    }


    .otp-body {
        padding: 40px;
    }


    .otp-description {
        text-align: center;

        color: #64748b;

        line-height: 1.7;

        margin-bottom: 25px;
    }


    .otp-input {
        height: 62px;

        border-radius: 14px;

        font-size: 26px;

        font-weight: 800;

        letter-spacing: 9px;

        text-align: center;

        background: #f8fafc;

        border: 1px solid #e2e8f0;
    }


    .otp-input:focus {
        border-color: #2563eb;

        box-shadow:
            0 0 0 4px rgba(37, 99, 235, .10);
    }


    .verify-button {
        height: 55px;

        border: 0;

        border-radius: 13px;

        background:
            linear-gradient(
                135deg,
                #1d4ed8,
                #3b82f6
            );

        color: #fff;

        font-weight: 800;

        transition: .2s ease;
    }


    .verify-button:hover {
        color: #fff;

        transform: translateY(-1px);

        box-shadow:
            0 10px 25px rgba(37, 99, 235, .20);
    }


    .login-success-popup {
        border-radius: 24px !important;
        padding: 30px !important;
    }


    .login-success-title {
        font-weight: 800 !important;
        color: #17221c !important;
    }


    .swal2-html-container {
        color: #64748b !important;
        line-height: 1.7 !important;
    }


    .swal2-timer-progress-bar {
        background: #198754 !important;
    }


    @media (max-width: 576px) {

        .otp-body {
            padding: 30px 22px;
        }

        .otp-input {
            font-size: 22px;
            letter-spacing: 7px;
        }

    }

</style>


<div class="otp-page">

    <div class="otp-card">


        <!-- HEADER -->

        <div class="otp-header">

            <div class="otp-icon">

                <i class="bi bi-shield-lock-fill"></i>

            </div>


            <h3>
                Verify Your Login
            </h3>


            <p>
                FGCK Joyland Pastor Portal
            </p>

        </div>



        <!-- BODY -->

        <div class="otp-body">


            <div class="otp-description">

                <p class="mb-2">

                    A verification code has been
                    sent to your registered email.

                </p>


                <p class="small mb-0">

                    The code expires in
                    <strong>5 minutes</strong>.

                </p>

            </div>



            <?php if ($error): ?>

                <div
                    class="alert alert-danger"
                    style="border-radius:12px;"
                >

                    <i
                        class="bi bi-exclamation-circle-fill me-2"
                    ></i>

                    <?= e($error) ?>

                </div>

            <?php endif; ?>



            <form method="post" autocomplete="off">

                <input
                    type="hidden"
                    name="csrf"
                    value="<?= e(csrf_token()) ?>"
                >


                <div class="mb-4">

                    <label
                        class="form-label fw-bold"
                    >

                        Verification Code

                    </label>


                    <input
                        type="text"
                        name="otp"
                        class="form-control otp-input"
                        maxlength="6"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        placeholder="000000"
                        pattern="[0-9]{6}"
                        required
                        autofocus
                    >

                </div>



                <button
                    type="submit"
                    class="btn verify-button w-100"
                >

                    <i
                        class="bi bi-shield-check me-2"
                    ></i>

                    Verify & Continue

                </button>

            </form>



            <div class="text-center mt-4">

                <a
                    href="/fgck_joyland/staff/resend_login_otp.php"
                    class="text-decoration-none fw-bold"
                >

                    <i
                        class="bi bi-arrow-repeat me-1"
                    ></i>

                    Resend Code

                </a>

            </div>



            <div class="text-center mt-3">

                <a
                    href="/fgck_joyland/staff/login.php"
                    class="text-muted text-decoration-none small"
                >

                    <i
                        class="bi bi-arrow-left me-1"
                    ></i>

                    Back to Login

                </a>

            </div>


        </div>

    </div>

</div>



<?php if ($success): ?>

<script>

document.addEventListener('DOMContentLoaded', function () {

    Swal.fire({

        icon: 'success',

        title: 'Login Successful!',

        html: `
            <div style="font-size:15px;">

                <strong>
                    Welcome to the FGCK Joyland Pastor Portal.
                </strong>

                <br><br>

                <span style="color:#64748b;">
                    Your identity has been successfully verified.
                </span>

                <br>

                <small style="color:#94a3b8;">
                    Redirecting you to your dashboard...
                </small>

            </div>
        `,

        showConfirmButton: false,

        timer: 2500,

        timerProgressBar: true,

        allowOutsideClick: false,

        allowEscapeKey: false,

        customClass: {
            popup: 'login-success-popup',
            title: 'login-success-title'
        }

    }).then(function () {

        window.location.href =
            '/fgck_joyland/staff/dashboard.php';

    });


    /*
     * Backup redirect.
     */
    setTimeout(function () {

        window.location.href =
            '/fgck_joyland/staff/dashboard.php';

    }, 3000);

});

</script>

<?php endif; ?>


<?php require __DIR__ . '/../includes/footer.php'; ?>