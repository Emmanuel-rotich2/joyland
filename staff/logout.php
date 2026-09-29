
<?php
require __DIR__ . '/../includes/bootstrap.php';

unset($_SESSION['staff_id']);
session_regenerate_id(true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Logged Out | FGCK Joyland</title>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<script>
Swal.fire({
    icon: 'success',
    title: 'Logged Out Successfully',
    html: `
        <div style="margin-top:10px;">
            <strong style="color:#198754; font-size:18px;">
                FGCK Joyland
            </strong>

            <p style="margin:10px 0 5px; color:#666;">
                You have been safely logged out of your account.
            </p>

            <small style="color:#999;">
                Redirecting to the home page in 3 seconds...
            </small>
        </div>
    `,
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    allowOutsideClick: false,
    allowEscapeKey: false,

    customClass: {
        popup: 'joyland-popup'
    }
}).then(() => {
    window.location.href = '/fgck_joyland/';
});

// Backup redirect after 3 seconds
setTimeout(() => {
    window.location.href = '/fgck_joyland/';
}, 3000);
</script>

<style>
.joyland-popup {
    border-radius: 20px !important;
    padding: 2rem !important;
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15) !important;
}

.swal2-title {
    font-weight: 700 !important;
}

.swal2-icon.swal2-success {
    border-color: #198754 !important;
    color: #198754 !important;
}

.swal2-success-ring {
    border-color: rgba(25, 135, 84, 0.3) !important;
}

.swal2-timer-progress-bar {
    background: #198754 !important;
}
</style>

</body>
</html>
