<?php $member=$member??current_member($pdo); ?>
<aside class="sidebar" id="sidebar">
    <div class="brand">
        <div class="brand-logo"><img src="/fgck_joyland/assets/images/full_gospel_churches_logo.png" alt="FGCK Joyland">
        </div>
        <div><strong>FGCK Joyland</strong><small>Member Portal</small></div>
    </div>
    <div class="sidebar-label">MEMBER AREA</div>
    <nav><a href="/fgck_joyland/member/dashboard.php"
            class="<?=basename($_SERVER['PHP_SELF'])==='dashboard.php'?'active':''?>"><i class="bi bi-grid-1x2"></i>
            Dashboard</a><a href="/fgck_joyland/member/book.php"
            class="<?=basename($_SERVER['PHP_SELF'])==='book.php'?'active':''?>"><i class="bi bi-calendar-plus"></i>
            Book Appointment</a><a href="/fgck_joyland/member/appointments.php"
            class="<?=basename($_SERVER['PHP_SELF'])==='appointments.php'?'active':''?>"><i
                class="bi bi-calendar-check"></i> My Appointments</a><a href="/fgck_joyland/member/reports.php"
            class="<?=basename($_SERVER['PHP_SELF'])==='reports.php'?'active':''?>"><i class="bi bi-bar-chart-line"></i>
            My Reports</a><a href="/fgck_joyland/member/profile.php"
            class="<?=basename($_SERVER['PHP_SELF'])==='profile.php'?'active':''?>"><i class="bi bi-person"></i> My
            Profile</a></nav>
    <div class="sidebar-bottom">
        <div class="mini-user"><span>
                <?=e(strtoupper(substr($member['full_name']??'M',0,1)))?>
            </span>
            <div><b>
                    <?=e($member['full_name']??'Member')?>
                </b><small>
                    <?=e($member['membership_no']??'')?>
                </small></div>
        </div><a href="/fgck_joyland/auth/logout.php" class="logout"><i class="bi bi-box-arrow-right"></i> Sign out</a>
    </div>
</aside>