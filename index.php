<?php require_once __DIR__.'/includes/bootstrap.php'; ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>FGCK Joyland | Pastor Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/fgck_joyland/assets/css/app.css" rel="stylesheet">
    
    <style>
        /* Beautiful church background with a professional, rich deep-slate color overlay */
        body.landing {
            background: linear-gradient(rgba(15, 23, 42, 0.78), rgba(15, 23, 42, 0.88)), 
                        url('https://unsplash.com') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            color: #f8fafc;
        }

        /* Sleek glassmorphism nav with modern transparent dark border */
        .landing-nav {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 15px 30px;
        }

        /* Custom text colors for professional contrast hierarchy */
        .text-light-sub {
            color: #cbd5e1;
        }

        /* High-end color adjustments for buttons */
        .btn-success {
            background-color: #10b981; /* Premium Emerald Green */
            border-color: #10b981;
        }
        .btn-success:hover {
            background-color: #059669;
            border-color: #059669;
        }

        .btn-warning {
            background-color: #f59e0b; /* Deep rich Amber/Gold */
            border-color: #f59e0b;
            color: #0f172a;
            font-weight: 600;
        }
        .btn-warning:hover {
            background-color: #d97706;
            border-color: #d97706;
            color: #ffffff;
        }

        .btn-outline-light {
            border-color: rgba(255, 255, 255, 0.3);
        }
        .btn-outline-light:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: #ffffff;
        }

        /* Translucent, elegant glass feature cards */
        .feature {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 24px;
            height: 100%;
            transition: all 0.3s ease;
        }
        .feature:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(245, 158, 11, 0.3); /* Subtle gold hint on hover */
            transform: translateY(-2px);
        }
        
        /* Vibrant custom color for design icons */
        .icon-accent {
            color: #f59e0b;
        }
    </style>
</head>

<body class="landing">
    <!-- Navbar Layout -->
    <nav class="landing-nav d-flex justify-content-between align-items-center sticky-top">
        <div class="d-flex align-items-center gap-3">
            <div class="brand-logo"><img src="/fgck_joyland/assets/images/full_gospel_churches_logo.png" alt="FGCK Joyland"></div>
            <div><b class="fs-5 text-white">FGCK Joyland</b><small class="d-block text-light-sub opacity-75">Pastor Appointment System</small></div>
        </div>
        <div>
            <a href="/fgck_joyland/auth/login.php" class="btn btn-outline-light btn-sm me-2 px-3">Sign in</a>
            <a href="/fgck_joyland/auth/register.php" class="btn btn-success btn-sm me-2 px-3">Register</a>
            <span class="text-white-50 me-2">|</span>
            <a href="/fgck_joyland/staff/login.php" class="btn btn-outline-light btn-sm px-3">Pastor Login</a>
        </div>
    </nav>
    
    <!-- Hero Main Section -->
    <section class="hero container text-center py-5 mt-5">
        <span class="badge rounded-pill text-bg-light text-dark px-3 py-2 mb-4 fw-semibold">EVERY WEDNESDAY • 9:00 AM ONWARD</span>
        <h1 class="display-4 fw-bold text-white mb-3">Meet the Pastor with purpose,<br class="d-none d-md-block"> peace and convenience.</h1>
        <p class="lead max-width-600 mx-auto text-light-sub mb-4">Book a private 30-minute appointment with the pastor through a simple, secure and organized church member portal.</p>
        
        <div class="mt-4 mb-5">
            <a href="/fgck_joyland/auth/register.php" class="btn btn-warning btn-lg px-4 me-3 py-2 fs-6 shadow-sm">Book an Appointment</a>
            <a href="/fgck_joyland/auth/login.php" class="btn btn-outline-light btn-lg px-4 py-2 fs-6">Member Login</a> 
        </div>
        
        <!-- Info Cards Grid -->
        <div class="row g-4 mt-5 text-start">
            <div class="col-md-4">
                <div class="feature">
                    <i class="bi bi-clock-history fs-3 icon-accent"></i>
                    <h3 class="h5 mt-3 text-white fw-bold">30-minute sessions</h3>
                    <p class="mb-0 text-light-sub opacity-90">Every booking receives one dedicated 30-minute time slot.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature">
                    <i class="bi bi-calendar2-week fs-3 icon-accent"></i>
                    <h3 class="h5 mt-3 text-white fw-bold">Wednesday appointments</h3>
                    <p class="mb-0 text-light-sub opacity-90">Members choose from slots opened by the pastor.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature">
                    <i class="bi bi-shield-check fs-3 icon-accent"></i>
                    <h3 class="h5 mt-3 text-white fw-bold">Private & secure</h3>
                    <p class="mb-0 text-light-sub opacity-90">Your appointment details stay inside your member account.</p>
                </div>
            </div>
        </div>
    </section>
</body>

</html>
