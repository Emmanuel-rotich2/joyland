<?php require_once __DIR__.'/../includes/bootstrap.php';member_required();$member=current_member($pdo);$q=$pdo->prepare("SELECT a.*,s.appointment_date,COALESCE(a.adjusted_start_time,s.start_time) AS start_time,COALESCE(a.adjusted_end_time,s.end_time) AS end_time FROM appointments a JOIN appointment_slots s ON s.id=a.slot_id WHERE a.member_id=? AND a.status IN('pending','confirmed') AND s.appointment_date>=CURDATE() ORDER BY s.appointment_date,s.start_time LIMIT 1");$q->execute([$member['id']]);$up=$q->fetch();$q=$pdo->prepare('SELECT COUNT(*) FROM appointments WHERE member_id=?');$q->execute([$member['id']]);$total=$q->fetchColumn();$q=$pdo->prepare("SELECT COUNT(*) FROM appointments WHERE member_id=? AND status='completed'");$q->execute([$member['id']]);$done=$q->fetchColumn();$q=$pdo->prepare("SELECT COUNT(*) FROM appointments WHERE member_id=? AND status='cancelled'");$q->execute([$member['id']]);$cancel=$q->fetchColumn();$officeStatus=setting($pdo,'office_status','available');
$page_title='Member Dashboard';require __DIR__.'/../includes/header.php';?>
<div>
    <?php require __DIR__.'/../includes/sidebar.php';?>
    <main class="main">
        <header class="topbar">
            <div class="d-flex align-items-center gap-3"><button class="menu-btn" data-sidebar-toggle><i
                        class="bi bi-list"></i></button>
                <div>
                    <h1>Member Dashboard</h1>
                    <p>Welcome back,
                        <?=e(explode(' ',$member['full_name'])[0])?>.
                    </p>
                </div>
            </div><a class="btn btn-primary btn-sm" href="/fgck_joyland/member/book.php">Book Appointment</a>
        </header>
        <div class="content">
            <div id="dashboardOfficeStatus"
                class="alert <?= $officeStatus==='available'?'alert-success':'alert-danger' ?> py-2 small"><i
                    class="bi <?= $officeStatus==='available'?'bi-check-circle':'bi-door-closed' ?> me-1"></i>
                <?= $officeStatus==='available'?"Pastor's Office is currently open for appointments.":"Pastor's Office is currently closed. New bookings are temporarily unavailable." ?>
            </div>
            <div class="welcome mb-4">
                <h2>Welcome to your appointment portal.</h2>
                <p>Book a private 30-minute session with the pastor. Appointments are held every Wednesday.</p>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="cardx stat"><i class="bi bi-calendar-check"></i>
                        <div class="num">
                            <?=$total?>
                        </div><small>Total appointments</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="cardx stat"><i class="bi bi-check-circle"></i>
                        <div class="num">
                            <?=$done?>
                        </div><small>Completed</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="cardx stat"><i class="bi bi-calendar-x"></i>
                        <div class="num">
                            <?=$cancel?>
                        </div><small>Cancelled</small>
                    </div>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="cardx p-4">
                        <div class="d-flex justify-content-between">
                            <h3 class="section-title">Upcoming appointment</h3><a
                                href="/fgck_joyland/member/appointments.php" class="small">History</a>
                        </div>
                        <?php if($up):?>
                        <div class="border rounded-4 p-4 mt-3"><span class="badge-soft">30 MINUTES</span>
                            <h3 class="mt-3 mb-1">
                                <?=e(fd($up['appointment_date']))?>
                            </h3>
                            <div class="text-muted">
                                <?=e(ft($up['start_time']))?> –
                                <?=e(ft($up['end_time']))?>
                            </div>
                            <div class="small mt-3"><b>Reference:</b>
                                <?=e($up['appointment_no'])?>
                            </div>
                            <div class="mt-2 small"><b>Purpose:</b>
                                <?=e($up['purpose'])?>
                            </div>
                        </div>
                        <?php else:?>
                        <div class="empty"><i class="bi bi-calendar2-plus fs-1"></i>
                            <p class="mt-3">You do not have an upcoming appointment.</p><a class="btn btn-primary"
                                href="/fgck_joyland/member/book.php">Find an available slot</a>
                        </div>
                        <?php endif;?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="cardx p-4">
                        <h3 class="section-title">Appointment guidelines</h3>
                        <ul class="small text-muted ps-3 lh-lg">
                            <li>Appointments are held on Wednesdays.</li>
                            <li>Each session is exactly 30 minutes.</li>
                            <li>Arrive a few minutes early.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<?php if($up):?>
<script>(function () {let sig = ''; async function sync() {try {const r = await fetch('/fgck_joyland/api/availability.php?date=<?=rawurlencode($up['appointment_date'])?>', {cache: 'no-store'}); const d = await r.json(); if (!d.ok) return; if (sig && sig !== d.signature) location.reload(); sig = d.signature;} catch (e) { } } setInterval(sync, 5000); sync();})();</script>
<?php endif;?>
<?php require __DIR__.'/../includes/footer.php';?>