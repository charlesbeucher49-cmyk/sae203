<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: intranet_login.php');
    exit();
}
$groupes = $_SESSION['groupes'] ?? [];

// Chargement du JSON
$jsonFile = "intranet_data-partenaires.json";

if (!file_exists($jsonFile)) {
    die("<div class='alert alert-danger m-4'>Erreur : Le fichier JSON des fournisseurs est introuvable.</div>");
}

$data = json_decode(file_get_contents($jsonFile), true);

if (!$data || !isset($data["fournisseurs"])) {
    die("<div class='alert alert-danger m-4'>Erreur : Le fichier JSON est vide ou mal formaté.</div>");
}

$fournisseurs = $data["fournisseurs"];

// Recherche
$search = isset($_GET["search"]) ? strtolower(trim($_GET["search"])) : "";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annuaire Partenaires — Intranet TechRevive</title>
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
        <li class="nav-item"><a class="nav-link" href="accueil_intranet.php">Accueil</a></li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown">Annuaires</a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="intranet_annuaire-employes.php">Employés</a></li>
                <li><a class="dropdown-item active" href="intranet_annuaire-partenaires.php">Partenaires</a></li>
                <li><a class="dropdown-item" href="intranet_annuaire-clients.php">Clients</a></li>
            </ul>
        </li>
        <li class="nav-item"><a class="nav-link" href="intranet_fichiers.php">Fichiers partagés</a></li>
        <?php if (in_array('admin', $groupes) || in_array('direction', $groupes)): ?>
        <li class="nav-item"><a class="nav-link" href="intranet_gestion-utilisateurs.php">Gestion Utilisateurs</a></li>
        <?php endif; ?>
        <?php if (in_array('admin', $groupes) || in_array('direction', $groupes) || in_array('managers', $groupes)): ?>
        <li class="nav-item"><a class="nav-link" href="intranet_gestion-employes.php">Gestion Employés</a></li>
        <?php endif; ?>
      </ul>
      <span class="navbar-text me-3"><?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?></span>
      <a href="intranet_logout.php" class="btn btn-outline-danger btn-sm">Déconnexion</a>
    </div>
  </div>
</nav>

<div class="container mt-4 fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color:#1B2A4A;">Annuaire des Partenaires</h2>
        <span class="badge" style="background:#2D6A2E;font-size:0.9rem;padding:6px 14px;"><?= count($fournisseurs) ?> partenaires</span>
    </div>

    <!-- Barre de recherche -->
    <form method="GET" class="mb-4">
        <input type="text" name="search" class="form-control form-control-lg" style="max-width:500px;margin:0 auto;display:block;"
               placeholder="🔍 Rechercher un partenaire (nom, ville, produit...)"
               value="<?= htmlspecialchars($search) ?>">
    </form>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-3" style="width:60px;">ID</th>
                            <th>Nom</th>
                            <th>Contact</th>
                            <th>Adresse</th>
                            <th>Produits</th>
                            <th style="width:100px;">Fiabilité</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $resultFound = false;
                    foreach ($fournisseurs as $f) {
                        $haystack = strtolower($f["nom"] . " " . $f["ville"] . " " . implode(" ", $f["type_produits"]));
                        if ($search !== "" && !str_contains($haystack, $search)) {
                            continue;
                        }
                        $resultFound = true;
                    ?>
                        <tr>
                            <td class="ps-3 fw-bold"><?= $f["id"] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($f["nom"]) ?></strong><br>
                                <span class="text-muted" style="font-size:0.82rem;">Partenaire officiel</span>
                            </td>
                            <td>
                                <div style="font-size:0.88rem;">📞 <?= htmlspecialchars($f["telephone"]) ?></div>
                                <div style="font-size:0.88rem;">✉️ <a href="mailto:<?= htmlspecialchars($f["email"]) ?>"><?= htmlspecialchars($f["email"]) ?></a></div>
                            </td>
                            <td>
                                <?= htmlspecialchars($f["adresse"]) ?><br>
                                <?= htmlspecialchars($f["code_postal"]) . " " . htmlspecialchars($f["ville"]) ?><br>
                                <span class="text-muted"><?= htmlspecialchars($f["pays"]) ?></span>
                            </td>
                            <td>
                                <?php foreach ($f["type_produits"] as $p): ?>
                                    <span class="badge bg-primary me-1 mb-1"><?= htmlspecialchars($p) ?></span>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <span class="badge bg-success fs-6"><?= htmlspecialchars($f["fiabilite"]) ?></span>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (!$resultFound): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-danger">
                                Aucun fournisseur ne correspond à votre recherche.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
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
