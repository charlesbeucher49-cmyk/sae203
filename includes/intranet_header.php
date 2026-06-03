<?php
// intranet_header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$groupes_user = $_SESSION['groupes'] ?? [];
$prenom = $_SESSION['prenom'] ?? '';
$nom = $_SESSION['nom'] ?? '';
$page_title = $page_title ?? 'Intranet TechRevive';
$active_page = $active_page ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style_intranet.css" rel="stylesheet">
    <style>
        .photo-thumbnail { 
            width: 42px; 
            height: 42px; 
            object-fit: cover; 
            border-radius: 50%; 
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand" href="accueil_intranet.php">
      <img src="../images/logo.png" alt="Logo">
      TechRevive Intranet
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link <?= ($active_page === 'accueil') ? 'active' : '' ?>" href="accueil_intranet.php">Accueil</a></li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?= (in_array($active_page, ['annuaire_employes', 'annuaire_partenaires', 'annuaire_clients'])) ? 'active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown">Annuaires</a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item <?= ($active_page === 'annuaire_employes') ? 'active' : '' ?>" href="intranet_annuaire-employes.php">Employés</a></li>
                <li><a class="dropdown-item <?= ($active_page === 'annuaire_partenaires') ? 'active' : '' ?>" href="intranet_annuaire-partenaires.php">Partenaires</a></li>
                <li><a class="dropdown-item <?= ($active_page === 'annuaire_clients') ? 'active' : '' ?>" href="intranet_annuaire-clients.php">Clients</a></li>
            </ul>
        </li>
        <li class="nav-item"><a class="nav-link <?= ($active_page === 'fichiers') ? 'active' : '' ?>" href="intranet_fichiers.php">Fichiers partagés</a></li>
        <?php if (in_array('admin', $groupes_user) || in_array('direction', $groupes_user)): ?>
        <li class="nav-item"><a class="nav-link <?= ($active_page === 'gestion_utilisateurs') ? 'active' : '' ?>" href="intranet_gestion-utilisateurs.php">Gestion Utilisateurs</a></li>
        <?php endif; ?>
        <?php if (in_array('admin', $groupes_user) || in_array('direction', $groupes_user) || in_array('managers', $groupes_user)): ?>
        <li class="nav-item"><a class="nav-link <?= ($active_page === 'gestion_employes') ? 'active' : '' ?>" href="intranet_gestion-employes.php">Gestion Employés</a></li>
        <li class="nav-item"><a class="nav-link <?= ($active_page === 'gestion_clients') ? 'active' : '' ?>" href="intranet_gestion-clients.php">Gestion Clients</a></li>
        <li class="nav-item"><a class="nav-link <?= ($active_page === 'gestion_partenaires') ? 'active' : '' ?>" href="intranet_gestion-partenaires.php">Gestion Partenaires</a></li>
        <?php endif; ?>
      </ul>
      <span class="navbar-text me-3"><?= htmlspecialchars($prenom . ' ' . $nom) ?></span>
      <a href="intranet_logout.php" class="btn btn-outline-danger btn-sm">Déconnexion</a>
    </div>
  </div>
</nav>
