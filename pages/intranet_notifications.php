<?php
require_once '../includes/intranet_fonctions.php';
verifierConnexion();

$message = "";
$notifications = lireJSON('../data/notifications.json') ?? [];

// Marquer comme lu
if (isset($_POST['mark_read'])) {
    verifierCSRFToken();
    $notif_id = $_POST['mark_read'];
    foreach ($notifications as &$notif) {
        if ($notif['id'] === $notif_id) {
            $notif['lu'] = true;
            break;
        }
    }
    sauvegarderJSON('../data/notifications.json', $notifications);
    $message = "<div class='alert alert-success alert-dismissible fade show'><strong>Succès!</strong> Notification marquée comme lue. <button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
}

// Supprimer
if (isset($_POST['delete_notif'])) {
    verifierCSRFToken();
    $notif_id = $_POST['delete_notif'];
    $notifications = array_filter($notifications, function($n) use ($notif_id) {
        return $n['id'] !== $notif_id;
    });
    $notifications = array_values($notifications);
    sauvegarderJSON('../data/notifications.json', $notifications);
    $message = "<div class='alert alert-success alert-dismissible fade show'><strong>Supprimé!</strong> Notification supprimée. <button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
}

$notifications = array_reverse($notifications);

$page_title = 'Notifications — Intranet TechRevive';
$active_page = 'notifications';
require_once '../includes/intranet_header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="var(--bs-primary)" class="bi bi-bell-fill" viewBox="0 0 16 16" style="margin-right:12px;vertical-align:text-bottom;">
              <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2m.995-14.901a1 1 0 1 0-1.99 0A5 5 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901"/>
            </svg>
            Notifications
        </h2>
        <div>
            <a href="intranet_dashboard.php" class="btn btn-outline-secondary">Retour au dashboard</a>
        </div>
    </div>

    <?= $message ?>

    <div class="row">
        <div class="col-lg-8">
            <?php if (count($notifications) > 0): ?>
                <div class="list-group">
                    <?php foreach ($notifications as $notif): ?>
                        <div class="list-group-item p-4 mb-2 rounded-2" style="border-left: 4px solid <?= ($notif['type'] === 'success') ? '#2D6A2E' : (($notif['type'] === 'danger') ? '#dc3545' : '#1B2A4A') ?>; background: <?= !$notif['lu'] ? 'rgba(232,168,56,0.05)' : '#fff' ?>;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div style="flex:1;">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <h5 class="mb-0" style="color:#1B2A4A;">
                                            <?= htmlspecialchars($notif['titre']) ?>
                                        </h5>
                                        <?php if (!$notif['lu']): ?>
                                            <span class="badge bg-warning text-dark">Nouveau</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="mb-2 text-secondary"><?= htmlspecialchars($notif['message']) ?></p>
                                    <small class="text-muted">
                                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16" style="margin-right:4px;">
                                            <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 7-7z"/>
                                        </svg>
                                        <?= $notif['timestamp'] ?>
                                    </small>
                                </div>
                                <form method="POST" style="display:inline;">
<input type="hidden" name="csrf_token" value="<?= genererCSRFToken() ?>">
                                    <?php if (!$notif['lu']): ?>
                                        <button type="submit" name="mark_read" value="<?= $notif['id'] ?>" class="btn btn-sm btn-outline-primary" style="margin-right:8px;">Marquer comme lu</button>
                                    <?php endif; ?>
                                    <button type="submit" name="delete_notif" value="<?= $notif['id'] ?>" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="card border-0 bg-light p-5 text-center">
                    <svg width="48" height="48" fill="#ccc" viewBox="0 0 16 16" style="margin:0 auto 16px;">
                        <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742.61.195 1.125.645 1.13 1.271.002.085.002.17 0 .255h1.338c.002-.086.002-.17 0-.255.005-.626.52-1.076 1.129-1.271-.325-1.545-.459-3.114-.459-3.742 0-1.82.894-3.433 2.268-4.409A3.986 3.986 0 0 0 8 1.918z"/>
                    </svg>
                    <h4 class="text-secondary">Aucune notification</h4>
                    <p class="text-muted">Vous êtes à jour!</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="card-title mb-0">Statistiques</h6>
                </div>
                <div class="card-body">
                    <?php 
                    $total_notifs = count($notifications);
                    $non_lues = count(array_filter($notifications, function($n) { return !$n['lu']; }));
                    $lues = $total_notifs - $non_lues;
                    ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total</span>
                            <strong><?= $total_notifs ?></strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Non lues</span>
                            <strong style="color:#dc3545;"><?= $non_lues ?></strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Lues</span>
                            <strong style="color:#2D6A2E;"><?= $lues ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/intranet_footer.php'; ?>
