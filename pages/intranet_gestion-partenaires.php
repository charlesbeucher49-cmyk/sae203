<?php
require_once '../includes/intranet_fonctions.php';
verifierConnexion();

$groupes_user = $_SESSION['groupes'] ?? [];
verifierDroits(['admin', 'direction', 'managers']);

$dataFile = '../data/intranet_data-partenaires.json';
$message = "";

// Charger les partenaires
$json = lireJSON($dataFile);
$partenaires = $json['fournisseurs'] ?? [];

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    /* -------------------------
       AJOUT PARTENAIRE
    -------------------------- */
    if ($action === 'add') {

        $nouveauId = 1;
        if (count($partenaires) > 0) {
            $ids = array_column($partenaires, 'id');
            $nouveauId = max($ids) + 1;
        }

        $logoPath = '';
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $filename = 'partenaire_' . $nouveauId . '_' . time() . '.' . $ext;
            if (!is_dir('../images/partenaires')) {
                mkdir('../images/partenaires', 0777, true);
            }
            if (move_uploaded_file($_FILES['logo']['tmp_name'], '../images/partenaires/' . $filename)) {
                $logoPath = 'images/partenaires/' . $filename;
            }
        }

        $nouveau = [
            'id' => $nouveauId,
            'nom' => $_POST['nom'] ?? '',
            'telephone' => $_POST['telephone'] ?? '',
            'email' => $_POST['email'] ?? '',
            'adresse' => $_POST['adresse'] ?? '',
            'ville' => $_POST['ville'] ?? '',
            'code_postal' => $_POST['code_postal'] ?? '',
            'pays' => $_POST['pays'] ?? '',
            'type_produits' => array_map('trim', explode(',', $_POST['type_produits'] ?? '')),
            'fiabilite' => $_POST['fiabilite'] ?? '',
            'description' => $_POST['description'] ?? '',
            'logo' => $logoPath
        ];

        $partenaires[] = $nouveau;
        $json['fournisseurs'] = $partenaires;
        sauvegarderJSON($dataFile, $json);

        enregistrerAudit('CREATE', 'partenaire', 'Ajout de ' . ($_POST['nom'] ?? ''));
        $message = "<div class='alert alert-success alert-dismissible fade show'><strong>Succès !</strong> Partenaire ajouté avec succès.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }

    /* -------------------------
       MODIFICATION PARTENAIRE
    -------------------------- */
    elseif ($action === 'edit') {
        $id_edit = (int) $_POST['id'];

        foreach ($partenaires as &$p) {
            if ($p['id'] === $id_edit) {
                $p['nom'] = $_POST['nom'] ?? '';
                $p['telephone'] = $_POST['telephone'] ?? '';
                $p['email'] = $_POST['email'] ?? '';
                $p['adresse'] = $_POST['adresse'] ?? '';
                $p['ville'] = $_POST['ville'] ?? '';
                $p['code_postal'] = $_POST['code_postal'] ?? '';
                $p['pays'] = $_POST['pays'] ?? '';
                $p['type_produits'] = array_map('trim', explode(',', $_POST['type_produits'] ?? ''));
                $p['fiabilite'] = $_POST['fiabilite'] ?? '';
                $p['description'] = $_POST['description'] ?? '';

                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                    $filename = 'partenaire_' . $p['id'] . '_' . time() . '.' . $ext;
                    if (!is_dir('../images/partenaires')) {
                        mkdir('../images/partenaires', 0777, true);
                    }
                    if (move_uploaded_file($_FILES['logo']['tmp_name'], '../images/partenaires/' . $filename)) {
                        $p['logo'] = 'images/partenaires/' . $filename;
                    }
                }
                break;
            }
        }

        $json['fournisseurs'] = $partenaires;
        sauvegarderJSON($dataFile, $json);

        enregistrerAudit('UPDATE', 'partenaire', 'Modification de ' . ($_POST['nom'] ?? ''));
        $message = "<div class='alert alert-success alert-dismissible fade show'><strong>Succès !</strong> Partenaire modifié avec succès.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }

    /* -------------------------
       SUPPRESSION PARTENAIRE
    -------------------------- */
    elseif ($action === 'delete') {
        $id_suppr = (int) $_POST['id'];

        foreach ($partenaires as $k => $p) {
            if ($p['id'] === $id_suppr) {
                unset($partenaires[$k]);
                $partenaires = array_values($partenaires);
                $json['fournisseurs'] = $partenaires;
                sauvegarderJSON($dataFile, $json);
                break;
            }
        }

        enregistrerAudit('DELETE', 'partenaire', 'Suppression du partenaire ID ' . $id_suppr);
        $message = "<div class='alert alert-success alert-dismissible fade show'><strong>Succès !</strong> Partenaire supprimé avec succès.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

$page_title = 'Gestion Partenaires — Intranet TechRevive';
$active_page = 'gestion_partenaires';
require_once '../includes/intranet_header.php';
?>

<div class="container mt-4 fade-in-up">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color:var(--bs-primary);">Gestion des Partenaires</h2>

        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddPartenaire">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="me-1">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Ajouter un partenaire
        </button>
    </div>

    <?= $message ?>

    <!-- Tableau -->
    <div class="card border-0 shadow-sm overflow-hidden mb-5">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:var(--bs-primary); color:#fff;">
                        <tr>
                            <th class="ps-4 py-3">ID</th>
                            <th class="py-3">Logo</th>
                            <th class="py-3">Nom</th>
                            <th class="py-3">Contact</th>
                            <th class="py-3">Adresse</th>
                            <th class="py-3">Produits</th>
                            <th class="py-3">Fiabilité</th>
                            <th class="text-end pe-4 py-3 text-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php foreach ($partenaires as $p): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-muted"><?= $p['id'] ?></td>

                            <td>
                                <?php if (!empty($p['logo'])): ?>
                                    <img src="../<?= htmlspecialchars($p['logo']) ?>" alt="Logo" style="height:32px; width:auto; border-radius:4px;">
                                <?php else: ?>
                                    <span class="text-muted small">Aucun logo</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <strong><?= htmlspecialchars($p['nom']) ?></strong>
                            </td>

                            <td>
                                📞 <?= htmlspecialchars($p['telephone']) ?><br>
                                ✉️ <a href="mailto:<?= htmlspecialchars($p['email']) ?>"><?= htmlspecialchars($p['email']) ?></a>
                            </td>

                            <td>
                                <?= htmlspecialchars($p['adresse']) ?><br>
                                <?= htmlspecialchars($p['code_postal'] . ' ' . $p['ville']) ?><br>
                                <?= htmlspecialchars($p['pays']) ?>
                            </td>

                            <td>
                                <?php foreach ($p['type_produits'] as $prod): ?>
                                    <span class="badge bg-secondary me-1"><?= htmlspecialchars($prod) ?></span>
                                <?php endforeach; ?>
                            </td>

                            <td>
                                <span class="badge bg-primary px-3 py-2" style="font-size:0.85rem;">
                                    <?= htmlspecialchars($p['fiabilite']) ?>
                                </span>
                            </td>

                            <td class="text-end pe-4 text-nowrap">
                                <button class="btn btn-outline-secondary btn-sm  px-3 me-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditPartenaire<?= $p['id'] ?>">
                                    Éditer
                                </button>

                                <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ce partenaire ?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm px-3">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (count($partenaires) === 0): ?>
                        <tr><td colspan="8" class="text-center py-5 text-muted">Aucun partenaire enregistré.</td></tr>
                    <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ------------------------------
     MODALS EDITION
-------------------------------- -->
<?php foreach ($partenaires as $p): ?>
<div class="modal fade" id="modalEditPartenaire<?= $p['id'] ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg" style="border-radius:14px;">
      <div class="modal-header border-0 pb-0" style="background:#f8f9fa; border-radius:14px 14px 0 0;">
        <h5 class="modal-title fw-bold" style="color:var(--bs-primary);">Modifier le Partenaire</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-4">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?= $p['id'] ?>">

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label small text-muted">Nom</label>
                    <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($p['nom'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted">Logo (optionnel)</label>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small text-muted">Téléphone</label>
                    <input type="text" name="telephone" class="form-control" value="<?= htmlspecialchars($p['telephone'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($p['email'] ?? '') ?>">
                </div>
            </div>

            <div class="mt-3">
                <label class="form-label small text-muted">Adresse</label>
                <input type="text" name="adresse" class="form-control" value="<?= htmlspecialchars($p['adresse']) ?>">
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-4">
                    <label class="form-label small text-muted">Ville</label>
                    <input type="text" name="ville" class="form-control" value="<?= htmlspecialchars($p['ville']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Code Postal</label>
                    <input type="text" name="code_postal" class="form-control" value="<?= htmlspecialchars($p['code_postal']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Pays</label>
                    <input type="text" name="pays" class="form-control" value="<?= htmlspecialchars($p['pays']) ?>">
                </div>
            </div>

            <div class="mt-3">
                <label class="form-label small text-muted">Produits (séparés par des virgules)</label>
                <input type="text" name="type_produits" class="form-control"
                       value="<?= htmlspecialchars(implode(', ', $p['type_produits'])) ?>">
            </div>

            <div class="mt-3">
                <label class="form-label small text-muted">Fiabilité</label>
                <select name="fiabilite" class="form-select">
                    <?php foreach (['A+', 'A', 'B+', 'B', 'C'] as $f): ?>
                        <option value="<?= $f ?>" <?= ($p['fiabilite'] === $f ? 'selected' : '') ?>><?= $f ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mt-3">
                <label class="form-label small text-muted">Description (pour la vitrine)</label>
                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($p['description'] ?? '') ?></textarea>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="button" class="btn btn-light w-50" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary w-50 shadow-sm">Enregistrer</button>
            </div>

        </form>
      </div>
    </div>
  </div>
</div>
<?php endforeach; ?>

<!-- ------------------------------
     MODAL AJOUT
-------------------------------- -->
<div class="modal fade" id="modalAddPartenaire" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg" style="border-radius:14px;">
      <div class="modal-header border-0 pb-0" style="background:#f8f9fa; border-radius:14px 14px 0 0;">
        <h5 class="modal-title fw-bold" style="color:var(--bs-primary);">Nouveau Partenaire</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-4">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label small text-muted">Nom</label>
                    <input type="text" name="nom" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted">Logo (optionnel)</label>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small text-muted">Téléphone</label>
                    <input type="text" name="telephone" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
            </div>

            <div class="mt-3">
                <label class="form-label small text-muted">Adresse</label>
                <input type="text" name="adresse" class="form-control">
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-4">
                    <label class="form-label small text-muted">Ville</label>
                    <input type="text" name="ville" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Code Postal</label>
                    <input type="text" name="code_postal" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Pays</label>
                    <input type="text" name="pays" class="form-control">
                </div>
            </div>

            <div class="mt-3">
                <label class="form-label small text-muted">Produits (séparés par des virgules)</label>
                <input type="text" name="type_produits" class="form-control" placeholder="ex: Smartphones, PC portables">
            </div>

            <div class="mt-3">
                <label class="form-label small text-muted">Fiabilité</label>
                <select name="fiabilite" class="form-select">
                    <option value="A+">A+</option>
                    <option value="A">A</option>
                    <option value="B+">B+</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                </select>
            </div>

            <div class="mt-3">
                <label class="form-label small text-muted">Description (pour la vitrine)</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="button" class="btn btn-light w-50" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary w-50 shadow-sm">Ajouter</button>
            </div>

        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once '../includes/intranet_footer.php'; ?>
