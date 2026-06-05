<?php
require_once '../includes/intranet_fonctions.php';
verifierConnexion();
verifierDroits(['admin', 'direction', 'managers']);

$audit_logs = lireJSON('../data/audit_log.json') ?? [];
$audit_logs = array_reverse($audit_logs);

// Filtrage
$filtre_user = $_GET['user'] ?? '';
$filtre_action = $_GET['action'] ?? '';
$filtre_entite = $_GET['entite'] ?? '';

if (!empty($filtre_user)) {
    $audit_logs = array_filter($audit_logs, function($log) use ($filtre_user) {
        return stripos($log['user'], $filtre_user) !== false;
    });
}

if (!empty($filtre_action)) {
    $audit_logs = array_filter($audit_logs, function($log) use ($filtre_action) {
        return $log['action'] === $filtre_action;
    });
}

if (!empty($filtre_entite)) {
    $audit_logs = array_filter($audit_logs, function($log) use ($filtre_entite) {
        return $log['entite'] === $filtre_entite;
    });
}

$audit_logs = array_values($audit_logs);

$page_title = 'Journal d\'Audit — Intranet TechRevive';
$active_page = 'audit_log';
require_once '../includes/intranet_header.php';
?>

<div class="container-fluid mt-4 fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <svg width="28" height="28" fill="currentColor" viewBox="0 0 16 16" style="margin-right:12px;vertical-align:text-bottom;">
                <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5z"/>
            </svg>
            Journal d'Audit
        </h2>
        <a href="intranet_dashboard.php" class="btn btn-outline-secondary">Retour au dashboard</a>
    </div>

    <!-- Filtres -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Utilisateur</label>
                    <input type="text" class="form-control" name="user" placeholder="Filtrer par utilisateur" value="<?= htmlspecialchars($filtre_user) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Action</label>
                    <select class="form-select" name="action">
                        <option value="">Toutes les actions</option>
                        <option value="CREATE" <?= $filtre_action === 'CREATE' ? 'selected' : '' ?>>CREATE</option>
                        <option value="UPDATE" <?= $filtre_action === 'UPDATE' ? 'selected' : '' ?>>UPDATE</option>
                        <option value="DELETE" <?= $filtre_action === 'DELETE' ? 'selected' : '' ?>>DELETE</option>
                        <option value="VIEW" <?= $filtre_action === 'VIEW' ? 'selected' : '' ?>>VIEW</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Entité</label>
                    <select class="form-select" name="entite">
                        <option value="">Toutes les entités</option>
                        <option value="client" <?= $filtre_entite === 'client' ? 'selected' : '' ?>>Client</option>
                        <option value="employe" <?= $filtre_entite === 'employe' ? 'selected' : '' ?>>Employé</option>
                        <option value="partenaire" <?= $filtre_entite === 'partenaire' ? 'selected' : '' ?>>Partenaire</option>
                        <option value="utilisateur" <?= $filtre_entite === 'utilisateur' ? 'selected' : '' ?>>Utilisateur</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                    <a href="intranet_audit-log.php" class="btn btn-outline-secondary">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="card-title mb-0">Total: <?= count($audit_logs) ?> enregistrements</h5>
        </div>
        <div class="card-body p-0">
            <?php if (count($audit_logs) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="padding:12px;">Date & Heure</th>
                                <th>Utilisateur</th>
                                <th>Action</th>
                                <th>Entité</th>
                                <th>Détails</th>
                                <th>Ancienne Valeur</th>
                                <th>Nouvelle Valeur</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($audit_logs as $log): ?>
                                <tr>
                                    <td style="padding:12px;">
                                        <small class="text-muted"><?= htmlspecialchars($log['timestamp']) ?></small>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($log['user']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($log['user_prenom'] . ' ' . $log['user_nom']) ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        $action = $log['action'];
                                        $color = ($action === 'CREATE') ? 'success' : (($action === 'DELETE') ? 'danger' : (($action === 'UPDATE') ? 'warning' : 'info'));
                                        ?>
                                        <span class="badge bg-<?= $color ?>"><?= htmlspecialchars($action) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($log['entite']) ?></td>
                                    <td>
                                        <small><?= htmlspecialchars($log['details']) ?></small>
                                    </td>
                                    <td>
                                        <small class="text-danger"><?= htmlspecialchars(substr($log['ancienne_donnee'], 0, 30)) ?><?= strlen($log['ancienne_donnee']) > 30 ? '...' : '' ?></small>
                                    </td>
                                    <td>
                                        <small class="text-success"><?= htmlspecialchars(substr($log['nouvelle_donnee'], 0, 30)) ?><?= strlen($log['nouvelle_donnee']) > 30 ? '...' : '' ?></small>
                                    </td>
                                    <td>
                                        <small class="text-muted font-monospace"><?= htmlspecialchars($log['ip']) ?></small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="p-5 text-center text-muted">
                    <p class="mb-0">Aucun enregistrement d'audit trouvé</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/intranet_footer.php'; ?>
