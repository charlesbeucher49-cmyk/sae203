<?php
session_start();

// Vérification de la connexion
if (!isset($_SESSION['login'])) {
    header('Location: intranet_login.php');
    exit();
}

$groupes_user = $_SESSION['groupes'] ?? [];
// Vérification des droits d'accès
if (!in_array('admin', $groupes_user) && !in_array('direction', $groupes_user)) {
    echo "<h1>Accès refusé. Vous n'avez pas les droits nécessaires.</h1>";
    echo "<a href='accueil_intranet.php'>Retour à l'accueil</a>";
    exit();
}

$dataFile = 'intranet_data_utilisateurs.json';
$message = "";

// Fonction pour lire les données
function getDonnees() {
    global $dataFile;
    if (!file_exists($dataFile)) {
        return ['utilisateurs' => []];
    }
    $json = file_get_contents($dataFile);
    $data = json_decode($json, true);
    return $data ?? ['utilisateurs' => []];
}

// Fonction pour sauvegarder les données
function saveDonnees($data) {
    global $dataFile;
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$data = getDonnees();
$utilisateurs = &$data['utilisateurs'];

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $nouveauId = 1;
        if (count($utilisateurs) > 0) {
            $ids = array_column($utilisateurs, 'id');
            $nouveauId = max($ids) + 1;
        }

        $mdp = $_POST['mot_de_passe'];
        $hash = password_hash($mdp, PASSWORD_DEFAULT);

        $groupes = $_POST['groupes'] ?? [];
        if (!is_array($groupes)) { $groupes = [$groupes]; }

        $nouvelUtilisateur = [
            'id' => $nouveauId,
            'login' => $_POST['login'],
            'mot_de_passe' => $hash,
            'nom' => $_POST['nom'],
            'prenom' => $_POST['prenom'],
            'profil' => $_POST['profil'],
            'groupes' => $groupes
        ];

        $utilisateurs[] = $nouvelUtilisateur;
        saveDonnees($data);
        $message = "<div class='alert alert-success'>Utilisateur ajouté avec succès.</div>";
    } 
    elseif ($action === 'delete') {
        $id_suppr = (int) $_POST['id'];
        foreach ($utilisateurs as $k => $u) {
            if ($u['id'] === $id_suppr) {
                // On empêche la suppression de son propre compte pour éviter de se bloquer
                if ($u['login'] === $_SESSION['login']) {
                    $message = "<div class='alert alert-danger'>Vous ne pouvez pas supprimer votre propre compte.</div>";
                } else {
                    unset($utilisateurs[$k]);
                    $utilisateurs = array_values($utilisateurs); // Réindexer correctement le tableau
                    saveDonnees($data);
                    $message = "<div class='alert alert-success'>Utilisateur supprimé avec succès.</div>";
                }
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
    <title>Gestion des Utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style_intranet.css" rel="stylesheet">
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
        <li class="nav-item"><a class="nav-link active text-warning" href="intranet_gestion-utilisateurs.php">Gestion Utilisateurs</a></li>
      </ul>
      <span class="navbar-text me-3">Connecté : <strong><?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?></strong></span>
      <a href="intranet_logout.php" class="btn btn-outline-danger btn-sm">Se déconnecter</a>
    </div>
  </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-warning mb-0">Gestion des Utilisateurs</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddUser">+ Ajouter un utilisateur</button>
    </div>
    
    <?= $message ?>

    <!-- Tableau des utilisateurs -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-3">ID</th>
                            <th>Nom & Prénom</th>
                            <th>Identifiant (login)</th>
                            <th>Profil</th>
                            <th>Groupes</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilisateurs as $u): ?>
                        <tr>
                            <td class="ps-3 fw-bold"><?= $u['id'] ?></td>
                            <td><?= htmlspecialchars($u['nom'] . ' ' . $u['prenom']) ?></td>
                            <td><code><?= htmlspecialchars($u['login']) ?></code></td>
                            <td><?= htmlspecialchars($u['profil']) ?></td>
                            <td>
                                <?php foreach ($u['groupes'] as $grp): ?>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($grp) ?></span>
                                <?php endforeach; ?>
                            </td>
                            <td class="text-end pe-3">
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" <?= ($u['login'] === $_SESSION['login']) ? 'disabled' : '' ?>>Supprimer</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (count($utilisateurs) === 0): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">Aucun utilisateur trouvé.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajout Utilisateur -->
<div class="modal fade" id="modalAddUser" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Nouvel utilisateur</h5>
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
                <label class="form-label">Identifiant (login)</label>
                <input type="text" name="login" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="mot_de_passe" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Profil / Fonction</label>
                <input type="text" name="profil" class="form-control" placeholder="ex: Développeur" required>
            </div>
            <div class="mb-4">
                <label class="form-label d-block">Groupes d'accès</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="groupes[]" value="admin">
                    <label class="form-check-label">Admin</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="groupes[]" value="direction">
                    <label class="form-check-label">Direction</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="groupes[]" value="managers">
                    <label class="form-check-label">Managers</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="groupes[]" value="salariés">
                    <label class="form-check-label">Salariés</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="groupes[]" value="perso">
                    <label class="form-check-label">Perso</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Créer l'utilisateur</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
