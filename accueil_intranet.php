<?php
session_start();

if (!isset($_SESSION['login'])) {
    header('Location: intranet_login.php');
    exit();
}

$prenom = $_SESSION['prenom'] ?? 'Utilisateur';
$nom = $_SESSION['nom'] ?? '';
$groupes = $_SESSION['groupes'] ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil — Intranet TechRevive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style_intranet.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand" href="accueil_intranet.php">
      <img src="logo.png" alt="Logo">
      TechRevive Intranet
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link active" href="accueil_intranet.php">Accueil</a></li>
        
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Annuaires</a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="intranet_annuaire-employes.php">Employés</a></li>
                <li><a class="dropdown-item" href="intranet_annuaire-partenaires.php">Partenaires</a></li>
                <li><a class="dropdown-item" href="intranet_annuaire-clients.php">Clients</a></li>
            </ul>
        </li>
        
        <li class="nav-item"><a class="nav-link" href="intranet_fichiers.php">Fichiers partagés</a></li>

        <?php if (in_array('admin', $groupes) || in_array('direction', $groupes)): ?>
        <li class="nav-item"><a class="nav-link" href="intranet_gestion-utilisateurs.php">Gestion Utilisateurs</a></li>
        <?php endif; ?>
      </ul>
      
      <span class="navbar-text me-3">
        <?= htmlspecialchars($prenom . ' ' . $nom) ?>
      </span>
      <a href="intranet_logout.php" class="btn btn-outline-danger btn-sm">Déconnexion</a>
    </div>
  </div>
</nav>

<div class="container mt-4 fade-in-up">
    <!-- Hero -->
    <div class="p-5 mb-4 rounded-3" style="background:linear-gradient(135deg,#1B2A4A 0%,#243556 100%);color:#fff;">
      <div class="container-fluid py-2">
        <div class="d-flex align-items-center mb-3">
          <img src="logo.png" alt="Logo" style="height:50px;margin-right:16px;">
          <div>
            <h1 class="display-6 fw-bold mb-0">Bienvenue, <?= htmlspecialchars($prenom) ?></h1>
            <p class="mb-0" style="opacity:0.7;font-size:0.95rem;">Espace collaborateur — TechRevive Solutions</p>
          </div>
        </div>
        <p class="fs-6 mb-0" style="opacity:0.8;">
          Vos groupes : 
          <?php foreach ($groupes as $g): ?>
            <span class="badge" style="background:rgba(45,106,46,0.8);margin-right:4px;"><?= htmlspecialchars($g) ?></span>
          <?php endforeach; ?>
        </p>
      </div>
    </div>

    <!-- Dashboard Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card dash-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width:42px;height:42px;background:rgba(27,42,74,0.08);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-right:12px;">
                            <svg width="22" height="22" fill="none" stroke="#1B2A4A" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <h5 class="card-title mb-0">Annuaires</h5>
                    </div>
                    <p class="card-text">Consultez et gérez les fiches des employés, clients et partenaires.</p>
                    <a href="intranet_annuaire-employes.php" class="btn btn-primary btn-sm">Accéder</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card dash-card h-100" style="border-left-color:#2D6A2E;">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width:42px;height:42px;background:rgba(45,106,46,0.08);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-right:12px;">
                            <svg width="22" height="22" fill="none" stroke="#2D6A2E" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        </div>
                        <h5 class="card-title mb-0">Fichiers Partagés</h5>
                    </div>
                    <p class="card-text">Téléversez ou téléchargez les documents (.txt, .csv) de l'entreprise.</p>
                    <a href="intranet_fichiers.php" class="btn btn-success btn-sm">Accéder</a>
                </div>
            </div>
        </div>
        
        <?php if (in_array('admin', $groupes)): ?>
        <div class="col-md-4">
            <div class="card dash-card h-100" style="border-left-color:#e8a838;">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width:42px;height:42px;background:rgba(232,168,56,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-right:12px;">
                            <svg width="22" height="22" fill="none" stroke="#e8a838" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        </div>
                        <h5 class="card-title mb-0">Administration</h5>
                    </div>
                    <p class="card-text">Gérez les comptes utilisateurs, leurs groupes et les permissions.</p>
                    <a href="intranet_gestion-utilisateurs.php" class="btn btn-warning btn-sm">Gérer</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<footer class="footer-intranet text-center mt-auto">
  <div class="container">
    <p class="mb-0">&copy; <?= date('Y') ?> TechRevive Solutions — Intranet. Tous droits réservés.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
