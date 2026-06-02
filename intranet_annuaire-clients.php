<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: intranet_login.php');
    exit();
}
$groupes = $_SESSION['groupes'] ?? [];

// Charger le fichier JSON
$jsonData = file_get_contents("intranet_data-clients.json");
$clients = json_decode($jsonData, true);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annuaire Clients — Intranet TechRevive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style_intranet.css" rel="stylesheet">
    <style>
        .search-box { max-width: 400px; }
    </style>
    <script>
        function searchClient() {
            let input = document.getElementById("search").value.toLowerCase();
            let rows = document.querySelectorAll("tbody tr");
            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(input) ? "" : "none";
            });
        }
    </script>
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
                <li><a class="dropdown-item" href="intranet_annuaire-partenaires.php">Partenaires</a></li>
                <li><a class="dropdown-item active" href="intranet_annuaire-clients.php">Clients</a></li>
            </ul>
        </li>
        <li class="nav-item"><a class="nav-link" href="intranet_fichiers.php">Fichiers partagés</a></li>
        <?php if (in_array('admin', $groupes) || in_array('direction', $groupes)): ?>
        <li class="nav-item"><a class="nav-link" href="intranet_gestion-utilisateurs.php">Gestion Utilisateurs</a></li>
        <?php endif; ?>
      </ul>
      <span class="navbar-text me-3"><?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?></span>
      <a href="intranet_logout.php" class="btn btn-outline-danger btn-sm">Déconnexion</a>
    </div>
  </div>
</nav>

<div class="container mt-4 fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color:#1B2A4A;">Annuaire Clients</h2>
        <span class="badge" style="background:#1B2A4A;font-size:0.9rem;padding:6px 14px;"><?= count($clients) ?> clients</span>
    </div>

    <div class="d-flex justify-content-center mb-4">
        <input type="text" id="search" class="form-control search-box" onkeyup="searchClient()" placeholder="🔍 Rechercher un client (nom, ville, produit...)">
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-3">ID</th>
                            <th>Client</th>
                            <th>Contact</th>
                            <th>Adresse</th>
                            <th>Achat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <td class="ps-3 fw-bold"><?= $client["id"] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($client["prenom"] . " " . $client["nom"]) ?></strong>
                                </td>
                                <td>
                                    <div style="font-size:0.88rem;">📧 <?= htmlspecialchars($client["email"]) ?></div>
                                    <div style="font-size:0.88rem;">📞 <?= htmlspecialchars($client["telephone"]) ?></div>
                                </td>
                                <td>
                                    <?= htmlspecialchars($client["adresse"]) ?><br>
                                    <span class="text-muted"><?= htmlspecialchars($client["code_postal"] . " " . $client["ville"]) ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($client["produit"]) ?></strong><br>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($client["date_achat"]) ?> — <?= $client["prix"] ?> €
                                    </small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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
