<?php
require __DIR__.'/../includes/bootstrap.php';
staff_required();

$msg = '';
$error = '';
$keys = ['church_name','opening_time','office_status','booking_notice'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $values = [];
    foreach ($keys as $k) $values[$k] = trim((string)($_POST[$k] ?? ''));
    $values['closing_time'] = '15:00';

    if (!preg_match('/^\d{2}:\d{2}$/', $values['opening_time'])) {
        $error = 'Please enter a valid opening time.';
    } elseif ($values['opening_time'] > '14:30') {
        $error = 'Opening time must be 2:30 PM or earlier so a full 30-minute slot can end at 3:00 PM.';
    } else {
        try {
            $pdo->beginTransaction();
            $save = $pdo->prepare("INSERT INTO settings(setting_key,setting_value) VALUES(?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)");
            foreach ($keys as $k) $save->execute([$k, $values[$k]]);
            $save->execute(['closing_time', '15:00']);
            // Immediately synchronize future Wednesday slots using the new opening time.
            sync_future_wednesday_slots($pdo, 52);
            // Remove any legacy arrival-time setting so it cannot be exposed or edited.
            $pdo->exec("DELETE FROM settings WHERE setting_key='arrival_time'");
            $pdo->commit();
            $msg = 'Settings saved successfully.';
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error = 'The settings could not be saved. Please try again.';
        }
    }
}

$v = [];
foreach ($keys as $k) $v[$k] = setting($pdo, $k);
$page_title = 'Settings';
require __DIR__.'/../includes/header.php';
?>
<div>
    <?php require __DIR__.'/../includes/staff_sidebar.php'; ?>
    <main class="main">
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="menu-btn" data-sidebar-toggle><i class="bi bi-list"></i></button>
                <div>
                    <h1>System Settings</h1>
                    <p>Configure appointment operations and office availability.</p>
                </div>
            </div>
        </header>
        <div class="content">
            <div class="cardx p-4" style="max-width:820px">
                <?php if ($msg): ?>
                <div class="alert alert-success small"><i class="bi bi-check-circle me-1"></i>
                    <?=e($msg)?>
                </div>
                <?php endif; ?>
                <?php if ($error): ?>
                <div class="alert alert-danger small"><i class="bi bi-exclamation-triangle me-1"></i>
                    <?=e($error)?>
                </div>
                <?php endif; ?>
                <form method="post">
                    <input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Church name</label><input class="form-control"
                                name="church_name" value="<?=e($v['church_name'])?>"></div>
                        <div class="col-md-6"><label class="form-label">Office status</label><select class="form-select"
                                name="office_status">
                                <option value="available" <?=$v['office_status']==='available' ?'selected':''?>
                                    >Available</option>
                                <option value="unavailable" <?=$v['office_status']==='unavailable' ?'selected':''?>
                                    >Unavailable / Closed</option>
                            </select></div>
                        <div class="col-md-6"><label class="form-label">Opening time</label><input class="form-control"
                                type="time" name="opening_time" value="<?=e($v['opening_time'])?>" required></div>
                        <div class="col-md-6"><label class="form-label">Closing time</label><input class="form-control"
                                type="time" value="15:00" disabled>
                            <div class="form-text">Fixed closing time: 3:00 PM. Only the opening time can be changed.
                            </div>
                        </div>
                        <div class="col-12"><label class="form-label">Booking notice</label><textarea
                                class="form-control" rows="4"
                                name="booking_notice"><?=e($v['booking_notice'])?></textarea></div>
                    </div>
                    <button class="btn btn-primary mt-4"><i class="bi bi-save me-1"></i> Save Settings</button>
                </form>
            </div>
        </div>
    </main>
</div>
<?php require __DIR__.'/../includes/footer.php'; ?>