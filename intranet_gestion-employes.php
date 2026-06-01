<?php
session_start();

// Vérification de la connexion
if (!isset($_SESSION['login'])) {
    header('Location: intranet_login.php');
    exit();
}

$groupes_user = $_SESSION['groupes'] ?? [];
// Vérification des droits d'accès
if (!in_array('admin', $groupes_user) && !in_array('managers', $groupes_user) && !in_array('direction', $groupes_user)) {
    echo "<h1>Accès refusé. Vous n'avez pas les droits nécessaires.</h1>";
    echo "<a href='accueil_intranet.php'>Retour à l'accueil</a>";
    exit();
}

$dataFile = 'intranet_data-employes.json';
$message = "";

// Fonction pour lire les données
function getDonnees() {
    global $dataFile;
    if (!file_exists($dataFile)) {
        return [];
    }
    $json = file_get_contents($dataFile);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

// Fonction pour sauvegarder les données
function saveDonnees($data) {
    global $dataFile;
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$employes = getDonnees();

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $nouveauId = 1;
        if (count($employes) > 0) {
            $ids = array_column($employes, 'id');
            $nouveauId = max($ids) + 1;
        }

        $nouvelEmploye = [
            'id' => $nouveauId,
            'nom' => $_POST['nom'],
            'prenom' => $_POST['prenom'],
            'fonction' => $_POST['fonction'],
            'photo' => $_POST['photo'],
            'bio' => $_POST['bio']
        ];

        $employes[] = $nouvelEmploye;
        saveDonnees($employes);
        $message = "<div class='alert alert-success'>Employé ajouté avec succès.</div>";
    } 
    elseif ($action === 'delete') {
        $id_suppr = (int) $_POST['id'];
        foreach ($employes as $k => $e) {
            if ($e['id'] === $id_suppr) {
                unset($employes[$k]);
                $employes = array_values($employes); // Réindexer correctement le tableau
                saveDonnees($employes);
                $message = "<div class='alert alert-success'>Employé supprimé avec succès.</div>";
                break;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Employés</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .photo-thumbnail { width: 50px; height: 50px; object-fit: cover; border-radius: 50%; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="accueil_intranet.php">Intranet Entreprise</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="accueil_intranet.php">Accueil</a></li>
        <li class="nav-item"><a class="nav-link" href="intranet_gestion-utilisateurs.php">Utilisateurs</a></li>
        <li class="nav-item"><a class="nav-link active text-warning" href="intranet_gestion-employes.php">Employés</a></li>
      </ul>
      <span class="navbar-text me-3">Connecté : <strong><?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?></strong></span>
      <a href="intranet_logout.php" class="btn btn-outline-danger btn-sm">Se déconnecter</a>
    </div>
  </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-warning mb-0">Administration : Annuaire des Employés</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddEmploye">+ Ajouter un employé</button>
    </div>
    
    <?= $message ?>

    <!-- Tableau des employés -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-3">ID</th>
                            <th>Photo</th>
                            <th>Nom & Prénom</th>
                            <th>Fonction</th>
                            <th>Bio</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employes as $e): ?>
                        <tr>
                            <td class="ps-3 fw-bold"><?= htmlspecialchars($e['id'] ?? '') ?></td>
                            <td>
                                <?php if (!empty($e['photo'])): ?>
                                    <img src="<?= htmlspecialchars($e['photo']) ?>" alt="Photo" class="photo-thumbnail">
                                <?php else: ?>
                                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center photo-thumbnail">?</div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars(($e['nom'] ?? '') . ' ' . ($e['prenom'] ?? '')) ?></strong></td>
                            <td><span class="badge bg-info text-dark"><?= htmlspecialchars($e['fonction'] ?? '') ?></span></td>
                            <td class="text-muted small"><?= htmlspecialchars($e['bio'] ?? '') ?></td>
                            <td class="text-end pe-3">
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Voulez-vous vraiment supprimer cet employé ?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($e['id'] ?? '') ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (count($employes) === 0): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">Aucun employé trouvé.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajout Employé -->
<div class="modal fade" id="modalAddEmploye" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Nouvel employé</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form action="" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom" class="form-control" required>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Prénom</label>
                    <input type="text" name="prenom" class="form-control" required>
                </div>
            </div>
            <div class="mb-2">
                <label class="form-label">Fonction</label>
                <input type="text" name="fonction" class="form-control" placeholder="ex: Développeur Web" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Nom du fichier photo (ex: jean.jpg)</label>
                <input type="text" name="photo" class="form-control" placeholder="image.jpg">
            </div>
            <div class="mb-3">
                <label class="form-label">Biographie</label>
                <textarea name="bio" class="form-control" rows="3" placeholder="Petite description..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Ajouter à l'annuaire</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
