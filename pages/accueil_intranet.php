<?php
require_once '../includes/intranet_fonctions.php';
verifierConnexion();

$prenom = $_SESSION['prenom'] ?? 'Utilisateur';
$nom = $_SESSION['nom'] ?? '';
$groupes = $_SESSION['groupes'] ?? [];

$page_title = 'Accueil — Intranet TechRevive';
$active_page = 'accueil';
require_once '../includes/intranet_header.php';
?>

<div class="container mt-4">
    <!-- Hero -->
    <div class="p-5 mb-4 rounded-3" style="background:#1B2A4A;color:#fff;">
      <div class="container-fluid py-2">
        <div class="d-flex align-items-center mb-3">
          <img src="../images/logo.png" alt="Logo" style="height:50px;margin-right:16px;">
          <div>
            <h1 class="display-6 fw-bold mb-0">Bienvenue, <?= htmlspecialchars($prenom) ?></h1>
            <p class="mb-0" style="opacity:0.7;font-size:0.95rem;">Espace collaborateur — TechRevive Solutions</p>
          </div>
        </div>
        <p class="fs-6 mb-0" style="opacity:0.8;">
          Vos groupes : 
          <?php foreach ($groupes as $g): ?>
            <span class="badge" style="background:rgba(var(--bs-success-rgb),0.8);margin-right:4px;"><?= htmlspecialchars($g) ?></span>
          <?php endforeach; ?>
        </p>
      </div>
    </div>

    <!-- Dashboard Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 border-start border-4 border-primary bg-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width:42px;height:42px;background:rgba(var(--bs-primary-rgb),0.08);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-right:12px;">
                            <svg width="22" height="22" fill="none" stroke="#1B2A4A" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12c0-1.1.9-2 2-2h2V7c0-1.1.9-2 2-2h2c1.1 0 2 .9 2 2v3h4V7c0-1.1.9-2 2-2h2c1.1 0 2 .9 2 2v3h2c1.1 0 2 .9 2 2v2m0 7v2c0 1.1-.9 2-2 2H5c-1.1 0-2-.9-2-2v-2m18-4v4"/></svg>
                        </div>
                        <h5 class="card-title text-primary fw-bold mb-0">Dashboard</h5>
                    </div>
                    <p class="card-text text-secondary" style="font-size: 0.9rem;">Tableau de bord avec statistiques et dernières actions.</p>
                    <a href="intranet_dashboard.php" class="btn btn-primary btn-sm">Accéder</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 border-start border-4 border-success bg-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width:42px;height:42px;background:rgba(var(--bs-success-rgb),0.08);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-right:12px;">
                            <svg width="22" height="22" fill="none" stroke="#2D6A2E" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        </div>
                        <h5 class="card-title text-primary fw-bold mb-0">Fichiers Partagés</h5>
                    </div>
                    <p class="card-text text-secondary" style="font-size: 0.9rem;">Téléversez ou téléchargez les documents (.txt, .csv) de l'entreprise.</p>
                    <a href="intranet_fichiers.php" class="btn btn-primary btn-sm">Accéder</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 border-start border-4 border-warning bg-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width:42px;height:42px;background:rgba(232,168,56,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-right:12px;">
                            <svg width="22" height="22" fill="none" stroke="#e8a838" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </div>
                        <h5 class="card-title text-primary fw-bold mb-0">Notifications</h5>
                    </div>
                    <p class="card-text text-secondary" style="font-size: 0.9rem;">Consulter vos notifications et alertes système.</p>
                    <a href="intranet_notifications.php" class="btn btn-primary btn-sm">Accéder</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 border-start border-4 border-info bg-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width:42px;height:42px;background:rgba(13,110,253,0.08);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-right:12px;">
                            <svg width="22" height="22" fill="none" stroke="#0d6efd" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/><polyline points="10 17 14 13 10 9"/></svg>
                        </div>
                        <h5 class="card-title text-primary fw-bold mb-0">Exports</h5>
                    </div>
                    <p class="card-text text-secondary" style="font-size: 0.9rem;">Exportez les annuaires en PDF ou CSV.</p>
                    <a href="intranet_export.php" class="btn btn-primary btn-sm">Accéder</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Annuaires Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 border-start border-4 border-success bg-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width:42px;height:42px;background:rgba(var(--bs-primary-rgb),0.08);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-right:12px;">
                            <svg width="22" height="22" fill="none" stroke="#1B2A4A" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <h5 class="card-title text-primary fw-bold mb-0">Annuaires</h5>
                    </div>
                    <p class="card-text text-secondary" style="font-size: 0.9rem;">Consultez et gérez les fiches des employés, clients et partenaires.</p>
                    <a href="intranet_annuaire-employes.php" class="btn btn-primary btn-sm">Accéder</a>
                </div>
            </div>
        </div>
        <?php if (in_array('admin', $groupes) || in_array('direction', $groupes) || in_array('managers', $groupes)): ?>
        <div class="col-md-4">
            <div class="card border-0 border-start border-4 border-warning bg-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width:42px;height:42px;background:rgba(var(--bs-warning-rgb),0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-right:12px;">
                            <svg width="22" height="22" fill="none" stroke="#e8a838" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        </div>
                        <h5 class="card-title text-primary fw-bold mb-0">Administration</h5>
                    </div>
                    <p class="card-text text-secondary" style="font-size: 0.9rem;">Gérez les comptes, les habilitations et l'annuaire des employés.</p>
                    <div class="d-flex gap-2">
                        <?php if (in_array('admin', $groupes) || in_array('direction', $groupes)): ?>
                            <a href="intranet_gestion-utilisateurs.php" class="btn btn-primary btn-sm fw-bold">Utilisateurs</a>
                        <?php endif; ?>
                        <a href="intranet_gestion-employes.php" class="btn btn-outline-primary btn-sm fw-bold">Employés</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 border-start border-4 border-danger bg-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width:42px;height:42px;background:rgba(220,53,69,0.08);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-right:12px;">
                            <svg width="22" height="22" fill="none" stroke="#dc3545" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/></svg>
                        </div>
                        <h5 class="card-title text-primary fw-bold mb-0">Audit</h5>
                    </div>
                    <p class="card-text text-secondary" style="font-size: 0.9rem;">Consultez le journal d'audit et l'historique des modifications.</p>
                    <a href="intranet_audit-log.php" class="btn btn-primary btn-sm">Consulter</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/intranet_footer.php'; ?>
