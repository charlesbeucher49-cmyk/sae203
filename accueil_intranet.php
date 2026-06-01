<?php
session_start();

// Redirection si l'utilisateur n'est pas connecté
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
    <title>Accueil Intranet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="accueil_intranet.php">Intranet Entreprise</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link active" href="accueil_intranet.php">Accueil</a></li>
        
        <!-- Annuaires -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Annuaires</a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="intranet_annuaire-employes.php">Employés</a></li>
                <li><a class="dropdown-item" href="intranet_annuaire-partenaires.php">Partenaires</a></li>
                <li><a class="dropdown-item" href="intranet_annuaire-clients.php">Clients</a></li>
            </ul>
        </li>
        
        <!-- Fichiers -->
        <li class="nav-item"><a class="nav-link" href="intranet_fichiers.php">Fichiers partagés</a></li>

        <!-- Affichage Admin / Direction -->
        <?php if (in_array('admin', $groupes) || in_array('direction', $groupes)): ?>
        <li class="nav-item"><a class="nav-link text-warning" href="intranet_gestion-utilisateurs.php">Gestion Utilisateurs</a></li>
        <?php endif; ?>
      </ul>
      
      <span class="navbar-text me-3">
        Connecté : <strong><?= htmlspecialchars($prenom . ' ' . $nom) ?></strong>
      </span>
      <a href="intranet_logout.php" class="btn btn-outline-danger btn-sm">Se déconnecter</a>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <div class="p-5 mb-4 bg-white rounded-3 shadow-sm border">
      <div class="container-fluid py-2">
        <h1 class="display-6 fw-bold text-primary">Bienvenue, <?= htmlspecialchars($prenom) ?> !</h1>
        <p class="fs-5 text-secondary">Cet espace vous donne accès aux ressources internes de l'entreprise. Vous faites partie des groupes : <span class="badge bg-secondary"><?= implode('</span> <span class="badge bg-secondary">', $groupes) ?></span></p>
        <hr class="my-4">
        
        <div class="row g-4 mt-2">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 bg-light">
                    <div class="card-body">
                        <h5 class="card-title text-primary">👥 Annuaires</h5>
                        <p class="card-text text-muted">Consultez et gérez les fiches des employés, clients et partenaires.</p>
                        <a href="intranet_annuaire-employes.php" class="btn btn-primary btn-sm">Accéder aux employés</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 bg-light">
                    <div class="card-body">
                        <h5 class="card-title text-success">📁 Fichiers Partagés</h5>
                        <p class="card-text text-muted">Téléversez ou téléchargez les documents (.txt, .csv) de l'entreprise.</p>
                        <a href="intranet_fichiers.php" class="btn btn-success btn-sm">Accéder aux fichiers</a>
                    </div>
                </div>
            </div>
            
            <?php if (in_array('admin', $groupes)): ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 bg-light">
                    <div class="card-body">
                        <h5 class="card-title text-warning">⚙️ Administration</h5>
                        <p class="card-text text-muted">Gérez les comptes utilisateurs, leurs groupes et les permissions.</p>
                        <a href="intranet_gestion-utilisateurs.php" class="btn btn-warning btn-sm">Gérer les accès</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
      </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
