<?php
require_once '../includes/intranet_fonctions.php';
verifierConnexion();

$groupes_user = $_SESSION['groupes'] ?? [];
verifierDroits(['admin', 'direction']);

$dataFile = '../data/intranet_data_utilisateurs.json';
$message = "";

$data = lireJSON($dataFile);
// Handle missing 'utilisateurs' key
if (!isset($data['utilisateurs'])) {
    $data['utilisateurs'] = [];
}
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
        sauvegarderJSON($dataFile, $data);
        enregistrerAudit('CREATE', 'utilisateur', 'Ajout de ' . $_POST['prenom'] . ' ' . $_POST['nom'] . ' (login: ' . $_POST['login'] . ')');
        $message = "<div class='alert alert-success'>Utilisateur ajouté avec succès.</div>";
    } 
    elseif ($action === 'edit') {
        $id_edit = (int) $_POST['id'];
        foreach ($utilisateurs as &$u) {
            if ($u['id'] === $id_edit) {
                $u['nom'] = $_POST['nom'];
                $u['prenom'] = $_POST['prenom'];
                $u['login'] = $_POST['login'];
                $u['profil'] = $_POST['profil'];
                
                if (!empty($_POST['mot_de_passe'])) {
                    $u['mot_de_passe'] = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
                }

                $groupes = $_POST['groupes'] ?? [];
                if (!is_array($groupes)) { $groupes = [$groupes]; }
                $u['groupes'] = $groupes;
                break;
            }
        }
        sauvegarderJSON($dataFile, $data);
        enregistrerAudit('UPDATE', 'utilisateur', 'Modification de ' . $_POST['prenom'] . ' ' . $_POST['nom'] . ' (login: ' . $_POST['login'] . ')');
        $message = "<div class='alert alert-success'>Utilisateur modifié avec succès.</div>";
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
                    sauvegarderJSON($dataFile, $data);
                    enregistrerAudit('DELETE', 'utilisateur', 'Suppression de ' . $u['prenom'] . ' ' . $u['nom']);
                    $message = "<div class='alert alert-success'>Utilisateur supprimé avec succès.</div>";
                }
                break;
            }
        }
    }
}

$page_title = 'Gestion Utilisateurs — Intranet TechRevive';
$active_page = 'gestion_utilisateurs';
require_once '../includes/intranet_header.php';
?>

<div class="container mt-4 fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color:var(--bs-primary);">Gestion des Utilisateurs</h2>
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
                    <thead style="background-color:var(--bs-primary); color:#fff;">
                        <tr>
                            <th class="ps-3">ID</th>
                            <th>Nom & Prénom</th>
                            <th>Identifiant</th>
                            <th>Profil</th>
                            <th>Groupes</th>
                            <th class="text-end pe-3 text-nowrap" style="width: 220px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilisateurs as $u): ?>
                        <tr>
                            <td class="ps-3 fw-bold"><?= $u['id'] ?></td>
                            <td><?= htmlspecialchars($u['nom'] . ' ' . $u['prenom']) ?></td>
                            <td><code style="background:rgba(var(--bs-primary-rgb),0.06);padding:2px 8px;border-radius:4px;color:var(--bs-primary);"><?= htmlspecialchars($u['login']) ?></code></td>
                            <td><?= htmlspecialchars($u['profil']) ?></td>
                            <td>
                                <?php foreach ($u['groupes'] as $grp): ?>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($grp) ?></span>
                                <?php endforeach; ?>
                            </td>
                            <td class="text-end pe-3 text-nowrap">
                                <button class="btn btn-outline-secondary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#modalEditUser<?= $u['id'] ?>">Éditer</button>
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

<?php foreach ($utilisateurs as $u): ?>
<!-- Modal Édition Utilisateur -->
<div class="modal fade" id="modalEditUser<?= $u['id'] ?>" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content text-start">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Modifier l'utilisateur</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form action="" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?= $u['id'] ?>">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label fw-semibold" style="font-size:0.88rem;">Nom</label>
                    <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($u['nom']) ?>" required>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label fw-semibold" style="font-size:0.88rem;">Prénom</label>
                    <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($u['prenom']) ?>" required>
                </div>
            </div>
            <div class="mb-2">
                <label class="form-label fw-semibold" style="font-size:0.88rem;">Identifiant (login)</label>
                <input type="text" name="login" class="form-control" value="<?= htmlspecialchars($u['login']) ?>" required>
            </div>
            <div class="mb-2">
                <label class="form-label fw-semibold" style="font-size:0.88rem;">Mot de passe (laisser vide pour ne pas modifier)</label>
                <input type="password" name="mot_de_passe" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:0.88rem;">Profil / Fonction</label>
                <input type="text" name="profil" class="form-control" value="<?= htmlspecialchars($u['profil']) ?>" required>
            </div>
            <div class="mb-4">
                <label class="form-label d-block fw-semibold" style="font-size:0.88rem;">Groupes d'accès</label>
                <?php $all_roles = ['admin', 'direction', 'managers', 'salariés', 'perso']; ?>
                <?php foreach ($all_roles as $r): ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="groupes[]" value="<?= $r ?>" <?= in_array($r, $u['groupes']) ? 'checked' : '' ?>>
                        <label class="form-check-label"><?= ucfirst($r) ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="btn btn-primary w-100">Enregistrer</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php endforeach; ?>

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

<?php require_once '../includes/intranet_footer.php'; ?>
