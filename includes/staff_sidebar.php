<?php $s=$staff??staff($pdo); ?>
<aside class="sidebar">
    <div class="brand">
        <div class="brand-logo"><img src="/fgck_joyland/assets/images/full_gospel_churches_logo.png" alt="FGCK Joyland">
        </div>
        <div><b>FGCK Joyland</b><small>Pastor Portal</small></div>
    </div>
    <nav>
        <a href="/fgck_joyland/staff/dashboard.php"><i class="bi bi-grid"></i> Dashboard</a><a
            href="/fgck_joyland/staff/calendar.php"><i class="bi bi-calendar3"></i> Calendar</a><a
            href="/fgck_joyland/staff/appointments.php"><i class="bi bi-calendar-check"></i> Appointments</a><a
            href="/fgck_joyland/staff/availability.php"><i class="bi bi-clock"></i> Slot Availability</a><a
            href="/fgck_joyland/staff/members.php"><i class="bi bi-people"></i> Members</a><a
            href="/fgck_joyland/staff/reports.php"><i class="bi bi-bar-chart"></i> Reports & Analytics</a><a
            href="/fgck_joyland/staff/settings.php"><i class="bi bi-gear"></i> Settings</a><a
            href="/fgck_joyland/staff/change_password.php"><i class="bi bi-key"></i> Change Password</a>
    </nav>
    <div class="side-bottom">
        <div class="mini-user"><span><i class="bi bi-person"></i></span>
            <div><b>
                    <?=e($s['full_name']??'Staff')?>
                </b><small>
                    <?=e(ucfirst($s['role']??'staff'))?>
                </small></div>
        </div><a href="/fgck_joyland/staff/logout.php" class="logout"><i class="bi bi-box-arrow-right"></i> Sign out</a>
    </div>
</aside>