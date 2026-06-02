<?php
session_start();

// Vérification de la connexion
if (!isset($_SESSION['login'])) {
    header('Location: intranet_login.php');
    exit();
}

$groupes = $_SESSION['groupes'] ?? [];
// Vérification des droits d'accès
if (!in_array('admin', $groupes) && !in_array('managers', $groupes) && !in_array('direction', $groupes)) {
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
        $message = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <strong>Succès !</strong> Collaborateur ajouté avec succès.
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>";
    } 
    elseif ($action === 'delete') {
        $id_suppr = (int) $_POST['id'];
        foreach ($employes as $k => $e) {
            if ($e['id'] === $id_suppr) {
                unset($employes[$k]);
                $employes = array_values($employes); // Réindexer correctement le tableau
                saveDonnees($employes);
                $message = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                                <strong>Succès !</strong> Collaborateur supprimé avec succès.
                                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                            </div>";
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
    <title>Gestion Employés — Intranet TechRevive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style_intranet.css" rel="stylesheet">
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
        <?php if (in_array('admin', $groupes) || in_array('direction', $groupes) || in_array('managers', $groupes)): ?>
        <li class="nav-item"><a class="nav-link active" href="intranet_gestion-employes.php">Gestion Employés</a></li>
        <?php endif; ?>
      </ul>
      <span class="navbar-text me-3"><?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?></span>
      <a href="intranet_logout.php" class="btn btn-outline-danger btn-sm">Déconnexion</a>
    </div>
  </div>
</nav>

<div class="container mt-4 fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color:#1B2A4A;">Gestion des Employés</h2>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddEmploye">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="me-1"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Ajouter un collaborateur
        </button>
    </div>
    
    <?= $message ?>

    <!-- Tableau des employés -->
    <div class="card border-0 shadow-sm overflow-hidden mb-5">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#1B2A4A; color:#fff;">
                        <tr>
                            <th class="ps-4 py-3" style="width: 80px;">ID</th>
                            <th class="py-3" style="width: 80px;">Photo</th>
                            <th class="py-3">Nom & Prénom</th>
                            <th class="py-3">Fonction</th>
                            <th class="py-3">Biographie</th>
                            <th class="text-end pe-4 py-3" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employes as $e): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-muted"><?= htmlspecialchars($e['id'] ?? '') ?></td>
                            <td>
                                <?php if (!empty($e['photo']) && file_exists($e['photo'])): ?>
                                    <img src="<?= htmlspecialchars($e['photo']) ?>" alt="Photo" class="photo-thumbnail">
                                <?php else: ?>
                                    <div class="photo-thumbnail text-white d-flex align-items-center justify-content-center fw-bold" style="background: linear-gradient(135deg, #1B2A4A, #2D6A2E); font-size: 0.85rem;">
                                        <?= strtoupper(mb_substr($e['prenom'] ?? '', 0, 1) . mb_substr($e['nom'] ?? '', 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars(($e['prenom'] ?? '') . ' ' . ($e['nom'] ?? '')) ?></strong></td>
                            <td><span class="badge" style="background: rgba(45,106,46,0.1); color:#2D6A2E; font-weight:600;"><?= htmlspecialchars($e['fonction'] ?? '') ?></span></td>
                            <td class="text-muted small text-truncate" style="max-width: 300px;" title="<?= htmlspecialchars($e['bio'] ?? '') ?>"><?= htmlspecialchars($e['bio'] ?? '') ?></td>
                            <td class="text-end pe-4">
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Voulez-vous vraiment supprimer ce collaborateur ?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($e['id'] ?? '') ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (count($employes) === 0): ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">Aucun collaborateur inscrit dans l'annuaire.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajout Employé -->
<div class="modal fade" id="modalAddEmploye" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius:14px;">
      <div class="modal-header border-0 pb-0" style="background:#f8f9fa; border-radius:14px 14px 0 0;">
        <h5 class="modal-title fw-bold" style="color:#1B2A4A;">Nouveau Collaborateur</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <form action="" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label text-muted fw-semibold small">Prénom</label>
                    <input type="text" name="prenom" class="form-control" placeholder="ex: Jean" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted fw-semibold small">Nom</label>
                    <input type="text" name="nom" class="form-control" placeholder="ex: Dupont" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted fw-semibold small">Fonction</label>
                <input type="text" name="fonction" class="form-control" placeholder="ex: Développeur Web" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted fw-semibold small">Fichier photo (ex: jean.jpg)</label>
                <input type="text" name="photo" class="form-control" placeholder="ex: jean.jpg">
                <div class="form-text text-muted" style="font-size:0.75rem;">Le fichier doit être présent à la racine du projet.</div>
            </div>
            <div class="mb-4">
                <label class="form-label text-muted fw-semibold small">Biographie / Description</label>
                <textarea name="bio" class="form-control" rows="3" placeholder="Quelques mots sur le collaborateur..."></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-light w-50" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary w-50 shadow-sm">Ajouter</button>
            </div>
        </form>
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
