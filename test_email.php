<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/includes/email.php';

$to = 'fgckjoyland@gmail.com';

$subject = 'FGCK Makutano West Joyland - Email Test';

$htmlBody = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Email Test</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f4f6f9; padding:30px;">

    <div style="
        max-width:600px;
        margin:auto;
        background:#ffffff;
        padding:30px;
        border-radius:10px;
        border:1px solid #ddd;
    ">

        <h2 style="margin-top:0;">
            FGCK Makutano West Joyland
        </h2>

        <p>
            This is a test email from the
            <strong>Pastor Appointment System</strong>.
        </p>

        <p>
            If you have received this email, the SMTP
            email configuration is working correctly.
        </p>

        <p>
            <strong>Test time:</strong>
            ' . date('Y-m-d H:i:s') . '
        </p>

        <hr>

        <p style="color:#666;">
            FGCK Makutano West Joyland
        </p>

    </div>

</body>
</html>
';

$textBody = '
FGCK Makutano West Joyland

This is a test email from the Pastor Appointment System.

If you have received this email, the SMTP email configuration is working correctly.

Test time: ' . date('Y-m-d H:i:s') . '

FGCK Makutano West Joyland
';

echo '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>FGCK Email Test</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 40px;
        }

        .box {
            max-width: 750px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.10);
        }

        h1 {
            margin-top: 0;
        }

        .success {
            background: #d1e7dd;
            color: #0f5132;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .error {
            background: #f8d7da;
            color: #842029;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        pre {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .info {
            background: #e7f1ff;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

    </style>
</head>

<body>

<div class="box">

<h1>FGCK Makutano West Joyland</h1>

<h2>Email System Test</h2>

<p>
Testing email delivery to:
<strong>' . htmlspecialchars($to, ENT_QUOTES, 'UTF-8') . '</strong>
</p>
';

try {

    /*
     * send_system_email() requires FOUR arguments:
     *
     * 1. Recipient
     * 2. Subject
     * 3. HTML message
     * 4. Plain-text message
     */

    $result = send_system_email(
        $to,
        $subject,
        $htmlBody,
        $textBody
    );

    if ($result) {

        echo '
        <div class="success">
            <strong>SUCCESS</strong><br><br>
            The email function reported that the message was
            successfully sent.
        </div>
        ';

    } else {

        echo '
        <div class="error">
            <strong>FAILED</strong><br><br>
            The email function returned FALSE.
        </div>
        ';
    }

} catch (Throwable $e) {

    echo '
    <div class="error">

        <strong>ERROR</strong><br><br>

        ' .
        htmlspecialchars(
            $e->getMessage(),
            ENT_QUOTES,
            'UTF-8'
        )
        . '

    </div>
    ';
}

echo '

<div class="info">

    <strong>Email Log</strong>

</div>

';

$logFile = __DIR__ . '/storage/email.log';

if (file_exists($logFile)) {

    $log = file_get_contents($logFile);

    echo '<pre>';

    echo htmlspecialchars(
        $log,
        ENT_QUOTES,
        'UTF-8'
    );

    echo '</pre>';

} else {

    echo '
    <p>
        <strong>No email.log file was found.</strong>
    </p>
    ';
}

echo '

</div>

</body>
</html>
';