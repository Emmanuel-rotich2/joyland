<?php require_once __DIR__.'/includes/bootstrap.php'; ?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>FGCK Joyland | Pastor Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/fgck_joyland/assets/css/app.css" rel="stylesheet">
</head>

<body class="landing">
    <nav class="landing-nav">
        <div class="d-flex align-items-center gap-2">
            <div class="brand-logo"><img src="/fgck_joyland/assets/images/full_gospel_churches_logo.png"
                    alt="FGCK Joyland"></div>
            <div><b>FGCK Joyland</b><small class="d-block opacity-75">Pastor Appointment System</small></div>
        </div>
        <div><a href="/fgck_joyland/auth/login.php" class="btn btn-outline-light btn-sm me-2">Sign in</a><a
                href="/fgck_joyland/auth/register.php" class="btn btn-outline-light btn-sm me-2" style="color:green;">Register</a>
                
            <a href="/fgck_joyland/staff/login.php" class="btn btn-outline-light btn-sm me-2">pastor login</a>
            </div>
    </nav>
    <section class="hero"><span class="badge rounded-pill text-bg-light text-dark px-3 py-2 mb-3">EVERY WEDNESDAY • 9:00
            AM ONWARD</span>
        <h1>Meet the Pastor with purpose, peace and convenience.</h1>
        <p>Book a private 30-minute appointment with the pastor through a simple, secure and organized church member
            portal.</p>
        <div class="mt-4"><a href="/fgck_joyland/auth/register.php" class="btn btn-warning btn-lg px-4 me-2">Book an
                Appointment</a><a href="/fgck_joyland/auth/login.php" class="btn btn-outline-light btn-lg px-4">Member
                Login</a> </div>
        <div class="row g-3 mt-5">
            <div class="col-md-4">
                <div class="feature"><i class="bi bi-clock-history"></i>
                    <h3>30-minute sessions</h3>
                    <p>Every booking receives one dedicated 30-minute time slot.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature"><i class="bi bi-calendar2-week"></i>
                    <h3>Wednesday appointments</h3>
                    <p>Members choose from slots opened by the pastor.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature"><i class="bi bi-shield-check"></i>
                    <h3>Private & secure</h3>
                    <p>Your appointment details stay inside your member account.</p>
                </div>
            </div>
        </div>
    </section>
</body>

</html>