<?php
session_start();

if (!isset($_SESSION['login'])) {
    header('Location: intranet_login.php');
    exit();
}

$groupes_user = $_SESSION['groupes'] ?? [];
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
                if ($u['login'] === $_SESSION['login']) {
                    $message = "<div class='alert alert-danger'>Vous ne pouvez pas supprimer votre propre compte.</div>";
                } else {
                    unset($utilisateurs[$k]);
                    $utilisateurs = array_values($utilisateurs);
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
    <title>Gestion Utilisateurs — Intranet TechRevive</title>
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
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Annuaires</a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="intranet_annuaire-employes.php">Employés</a></li>
                <li><a class="dropdown-item" href="intranet_annuaire-partenaires.php">Partenaires</a></li>
                <li><a class="dropdown-item" href="intranet_annuaire-clients.php">Clients</a></li>
            </ul>
        </li>
        <li class="nav-item"><a class="nav-link" href="intranet_fichiers.php">Fichiers partagés</a></li>
        <?php if (in_array('admin', $groupes_user) || in_array('direction', $groupes_user)): ?>
        <li class="nav-item"><a class="nav-link active" href="intranet_gestion-utilisateurs.php">Gestion Utilisateurs</a></li>
        <?php endif; ?>
        <?php if (in_array('admin', $groupes_user) || in_array('direction', $groupes_user) || in_array('managers', $groupes_user)): ?>
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
        <h2 class="fw-bold mb-0" style="color:#1B2A4A;">Gestion des Utilisateurs</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddUser">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:4px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Ajouter un utilisateur
        </button>
    </div>
    
    <?= $message ?>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-3">ID</th>
                            <th>Nom & Prénom</th>
                            <th>Identifiant</th>
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
                            <td><code style="background:rgba(27,42,74,0.06);padding:2px 8px;border-radius:4px;color:#1B2A4A;"><?= htmlspecialchars($u['login']) ?></code></td>
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
        <h5 class="modal-title fw-bold">Nouvel utilisateur</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form action="" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label fw-semibold" style="font-size:0.88rem;">Nom</label>
                    <input type="text" name="nom" class="form-control" required>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label fw-semibold" style="font-size:0.88rem;">Prénom</label>
                    <input type="text" name="prenom" class="form-control" required>
                </div>
            </div>
            <div class="mb-2">
                <label class="form-label fw-semibold" style="font-size:0.88rem;">Identifiant (login)</label>
                <input type="text" name="login" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label fw-semibold" style="font-size:0.88rem;">Mot de passe</label>
                <input type="password" name="mot_de_passe" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:0.88rem;">Profil / Fonction</label>
                <input type="text" name="profil" class="form-control" placeholder="ex: Développeur" required>
            </div>
            <div class="mb-4">
                <label class="form-label d-block fw-semibold" style="font-size:0.88rem;">Groupes d'accès</label>
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

<footer class="footer-intranet text-center mt-auto">
  <div class="container">
    <p class="mb-0">&copy; <?= date('Y') ?> TechRevive Solutions — Intranet. Tous droits réservés.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
