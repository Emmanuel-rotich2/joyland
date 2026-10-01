<?php
declare(strict_types=1);

require __DIR__.'/../includes/bootstrap.php';
staff_required();

$staff = staff($pdo);
if (!$staff) {
    redirect('/fgck_joyland/staff/login.php');
}

$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'read') {
        $id = (int)($_POST['id'] ?? 0);
        $q = $pdo->prepare("UPDATE notifications SET is_read=1 WHERE id=? AND user_id=?");
        $q->execute([$id, (int)$staff['id']]);
        $msg = 'Notification marked as read.';
    } elseif ($action === 'all_read') {
        $q = $pdo->prepare("UPDATE notifications SET is_read=1 WHERE user_id=? AND is_read=0");
        $q->execute([(int)$staff['id']]);
        $msg = 'All notifications marked as read.';
    }
}

$q = $pdo->prepare("
    SELECT *
    FROM notifications
    WHERE user_id=?
    ORDER BY created_at DESC
    LIMIT 100
");
$q->execute([(int)$staff['id']]);
$rows = $q->fetchAll();

$unread = unread_staff_notification_count($pdo, (int)$staff['id']);

$page_title = 'Notifications';
require __DIR__.'/../includes/header.php';
?>
<div>
<?php require __DIR__.'/../includes/staff_sidebar.php'; ?>

<main class="main">
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="menu-btn" data-sidebar-toggle><i class="bi bi-list"></i></button>
            <div>
                <h1>Notifications</h1>
                <p>Appointment requests and important pastor portal alerts.</p>
            </div>
        </div>

        <?php if ($unread): ?>
            <form method="post">
                <input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
                <input type="hidden" name="action" value="all_read">
                <button class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-check2-all me-1"></i> Mark all read
                </button>
            </form>
        <?php endif; ?>
    </header>

    <div class="content">
        <?php if ($msg): ?>
            <div class="alert alert-success small"><?=e($msg)?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger small"><?=e($error)?></div>
        <?php endif; ?>

        <div class="cardx p-4">
            <div class="analytics-head mb-3">
                <div>
                    <h3>Pastor notification inbox</h3>
                    <p class="mb-0"><?=$unread?> unread notification<?=$unread===1?'':'s'?>.</p>
                </div>
                <div class="insight-icon"><i class="bi bi-bell fs-4"></i></div>
            </div>

            <div class="d-grid gap-3">
                <?php foreach ($rows as $n): ?>
                    <div class="p-3 rounded-3 border <?=$n['is_read'] ? '' : 'unread-card'?>" style="background:<?=$n['is_read'] ? '#fff' : '#fffdf7'?>">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="insight-icon">
                                <i class="bi bi-<?=($n['type']==='success'?'check-circle':($n['type']==='warning'?'exclamation-triangle':($n['type']==='danger'?'exclamation-octagon':'bell')))?>"></i>
                            </div>

                            <div class="flex-grow-1">
                                <strong><?=e($n['title'])?></strong>
                                <div class="small text-secondary mt-1"><?=e($n['message'])?></div>
                                <div class="small text-muted mt-2">
                                    <?=e(date('d M Y, g:i A', strtotime($n['created_at'])))?>
                                </div>

                                <?php if (stripos((string)$n['title'], 'appointment') !== false): ?>
                                    <a href="/fgck_joyland/staff/appointments.php" class="btn btn-sm btn-outline-primary mt-2">
                                        <i class="bi bi-calendar-check me-1"></i> Review appointments
                                    </a>
                                <?php endif; ?>
                            </div>

                            <?php if (!$n['is_read']): ?>
                                <form method="post">
                                    <input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
                                    <input type="hidden" name="action" value="read">
                                    <input type="hidden" name="id" value="<?=$n['id']?>">
                                    <button class="btn btn-sm btn-outline-secondary">Read</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (!$rows): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-bell-slash fs-1 d-block mb-2"></i>
                        No notifications yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
</div>
<?php require __DIR__.'/../includes/footer.php'; ?>
