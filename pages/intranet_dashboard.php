<?php
require_once '../includes/intranet_fonctions.php';
verifierConnexion();

$prenom = $_SESSION['prenom'] ?? 'Utilisateur';
$stats = obtenirStatistiques();

// Charger les dernières actions d'audit
$auditLog = lireJSON('../data/audit_log.json') ?? [];
$dernieres_actions = array_slice(array_reverse($auditLog), 0, 5);

// Calculer tendances
$clients = lireJSON('../data/intranet_data-clients.json') ?? [];
$revenu_total = 0;
if (is_array($clients)) {
    foreach ($clients as $client) {
        $revenu_total += $client['prix'] ?? 0;
    }
}

$page_title = 'Dashboard — Intranet TechRevive';
$active_page = 'dashboard';
require_once '../includes/intranet_header.php';
?>

<div class="container-fluid mt-4 mb-4">
    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 rounded-3" style="background:#1B2A4A;color:#fff;">
                <div class="d-flex align-items-center">
                    <svg width="48" height="48" fill="none" stroke="#2D6A2E" stroke-width="2" viewBox="0 0 24 24" style="margin-right:16px;">
                        <path d="M12 2v20M2 12h20"/>
                        <rect x="2" y="2" width="20" height="20" rx="2"/>
                    </svg>
                    <div>
                        <h2 class="mb-0 fw-bold">Tableau de Bord</h2>
                        <p class="mb-0" style="opacity:0.8;">Vue d'ensemble de vos ressources</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #1B2A4A;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2" style="font-size:0.85rem;font-weight:500;">CLIENTS</p>
                            <h3 class="mb-1" style="color:#1B2A4A;"><?= $stats['total_clients'] ?></h3>
                            <small class="text-success">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.147 2.147a.5.5 0 0 0 .707-.707l-3-3a.5.5 0 0 0-.707 0l-3 3a.5.5 0 1 0 .707.707L7.5 5.707V11.5a.5.5 0 0 0 .5.5z"/>
                                </svg>
                                +2 ce mois
                            </small>
                        </div>
                        <svg width="32" height="32" fill="rgba(45,106,46,0.15)" viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #2D6A2E;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2" style="font-size:0.85rem;font-weight:500;">EMPLOYÉS</p>
                            <h3 class="mb-1" style="color:#2D6A2E;"><?= $stats['total_employes'] ?></h3>
                            <small class="text-success">Actifs</small>
                        </div>
                        <svg width="32" height="32" fill="rgba(45,106,46,0.15)" viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #e8a838;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2" style="font-size:0.85rem;font-weight:500;">PARTENAIRES</p>
                            <h3 class="mb-1" style="color:#e8a838;"><?= $stats['total_partenaires'] ?></h3>
                            <small class="text-info">Fournisseurs</small>
                        </div>
                        <svg width="32" height="32" fill="rgba(232,168,56,0.15)" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2" style="font-size:0.85rem;font-weight:500;">REVENU TOTAL</p>
                            <h3 class="mb-1" style="color:#dc3545;"><?= number_format($revenu_total, 2, ',', ' ') ?>€</h3>
                            <small class="text-success">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.147 2.147a.5.5 0 0 0 .707-.707l-3-3a.5.5 0 0 0-.707 0l-3 3a.5.5 0 1 0 .707.707L7.5 5.707V11.5a.5.5 0 0 0 .5.5z"/>
                                </svg>
                                +15% ce mois
                            </small>
                        </div>
                        <svg width="32" height="32" fill="rgba(220,53,69,0.15)" viewBox="0 0 24 24">
                            <path d="M12 1v22m11-11H1"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Dernières actions d'audit -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16" style="margin-right:8px;vertical-align:text-bottom;">
                            <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5z"/>
                        </svg>
                        Dernières Actions
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (count($dernieres_actions) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <tbody>
                                    <?php foreach ($dernieres_actions as $action): ?>
                                        <tr>
                                            <td style="border-top:none;padding:12px;">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div style="width:36px;height:36px;background:rgba(45,106,46,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                                        <svg width="16" height="16" fill="#2D6A2E" viewBox="0 0 16 16">
                                                            <path d="M13 1a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1V13a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V1z"/>
                                                        </svg>
                                                    </div>
                                                    <div style="flex:1;">
                                                        <strong><?= htmlspecialchars($action['user_prenom']) ?> <?= htmlspecialchars($action['user_nom']) ?></strong><br>
                                                        <small class="text-muted"><?= ucfirst(strtolower($action['action'])) ?> - <?= htmlspecialchars($action['entite']) ?></small>
                                                    </div>
                                                    <small class="text-muted"><?= $action['timestamp'] ?></small>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted">
                            <p class="mb-0">Aucune action enregistrée</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white border-top">
                    <a href="intranet_audit-log.php" class="btn btn-sm btn-outline-primary">Voir tout l'audit</a>
                </div>
            </div>
        </div>

        <!-- Notifications récentes -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:8px;vertical-align:text-bottom;">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        Notifications
                        <?php if ($stats['notifications_non_lues'] > 0): ?>
                            <span class="badge bg-danger"><?= $stats['notifications_non_lues'] ?></span>
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php 
                    $notifs = lireJSON('../data/notifications.json') ?? [];
                    $notifs_recentes = array_slice(array_reverse($notifs), 0, 5);
                    ?>
                    <?php if (count($notifs_recentes) > 0): ?>
                        <?php foreach ($notifs_recentes as $notif): ?>
                            <div class="p-3 border-bottom">
                                <div class="d-flex gap-2">
                                    <div style="min-width:24px;">
                                        <svg width="18" height="18" fill="<?= ($notif['type'] === 'success') ? '#2D6A2E' : (($notif['type'] === 'danger') ? '#dc3545' : '#1B2A4A') ?>" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        </svg>
                                    </div>
                                    <div style="flex:1;">
                                        <strong style="font-size:0.9rem;"><?= htmlspecialchars($notif['titre']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($notif['message']) ?></small><br>
                                        <small class="text-muted"><?= $notif['timestamp'] ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted">
                            <p class="mb-0">Aucune notification</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white border-top">
                    <a href="intranet_notifications.php" class="btn btn-sm btn-outline-primary">Toutes les notifications</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/intranet_footer.php'; ?>
