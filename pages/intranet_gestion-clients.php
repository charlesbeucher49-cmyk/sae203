<?php
// session_start() est déjà appelé par verifierConnexion()
require_once '../includes/intranet_fonctions.php';
verifierConnexion();

$groupes_user = $_SESSION['groupes'] ?? [];
verifierDroits(['admin', 'direction', 'managers']);

$dataFile = '../data/intranet_data-clients.json';
$message = "";

$clients = lireJSON($dataFile);

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'ajouter' || $action === 'modifier') {
        $id = $_POST['id'] ?? uniqid();
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $adresse = trim($_POST['adresse'] ?? '');
        $code_postal = trim($_POST['code_postal'] ?? '');
        $ville = trim($_POST['ville'] ?? '');
        $produit = trim($_POST['produit'] ?? '');
        $date_achat = trim($_POST['date_achat'] ?? '');
        $prix = floatval($_POST['prix'] ?? 0);

        if (!empty($nom) && !empty($prenom) && !empty($email)) {
            $nouveauClient = [
                "id" => $id,
                "nom" => $nom,
                "prenom" => $prenom,
                "telephone" => $telephone,
                "email" => $email,
                "adresse" => $adresse,
                "code_postal" => $code_postal,
                "ville" => $ville,
                "produit" => $produit,
                "date_achat" => $date_achat,
                "prix" => $prix
            ];

            if ($action === 'ajouter') {
                // Trouver le dernier ID
                $maxId = 0;
                foreach ($clients as $c) {
                    if (is_numeric($c['id']) && $c['id'] > $maxId) {
                        $maxId = $c['id'];
                    }
                }
                $nouveauClient['id'] = $maxId + 1;
                $clients[] = $nouveauClient;
                enregistrerAudit('CREATE', 'client', 'Ajout de ' . $prenom . ' ' . $nom);
                $message = "<div class='alert alert-success'>Client ajouté avec succès.</div>";
            } else {
                foreach ($clients as &$c) {
                    if ($c['id'] == $id) {
                        $c = $nouveauClient;
                        break;
                    }
                }
                enregistrerAudit('UPDATE', 'client', 'Modification de ' . $prenom . ' ' . $nom);
                $message = "<div class='alert alert-success'>Client modifié avec succès.</div>";
            }
            sauvegarderJSON($dataFile, $clients);
        } else {
            $message = "<div class='alert alert-danger'>Veuillez remplir au moins le nom, le prénom et l'email.</div>";
        }
    } elseif ($action === 'supprimer') {
        $id = $_POST['id'] ?? '';
        if ($id) {
            $clients = array_filter($clients, function($c) use ($id) {
                return $c['id'] != $id;
            });
            $clients = array_values($clients);
            sauvegarderJSON($dataFile, $clients);
            enregistrerAudit('DELETE', 'client', 'Suppression du client ID ' . $id);
            $message = "<div class='alert alert-success'>Client supprimé avec succès.</div>";
        }
    }
}

$page_title = 'Gestion Clients — Intranet';
$active_page = 'gestion_clients';
require_once '../includes/intranet_header.php';
?>

<div class="container mt-4 fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestion des Clients</h2>
        <a href="intranet_annuaire-clients.php" class="btn btn-outline-secondary">Retour à l'annuaire</a>
    </div>

    <?= $message ?>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header text-white" style="background:var(--bs-primary);">
            <h5 class="mb-0">Ajouter un nouveau client</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="action" value="ajouter">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom *</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Prénom *</label>
                        <input type="text" name="prenom" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="telephone" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Adresse</label>
                    <input type="text" name="adresse" class="form-control">
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Code Postal</label>
                        <input type="text" name="code_postal" class="form-control">
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Ville</label>
                        <input type="text" name="ville" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5 mb-3">
                        <label class="form-label">Dernier Produit</label>
                        <input type="text" name="produit" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Date d'achat</label>
                        <input type="date" name="date_achat" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Prix (€)</label>
                        <input type="number" step="0.01" name="prix" class="form-control">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Ajouter le client</button>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header text-white" style="background:var(--bs-primary);">
            <h5 class="mb-0">Liste des clients existants</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:var(--bs-primary); color:#fff;">
                        <tr>
                            <th class="ps-3">ID</th>
                            <th>Nom complet</th>
                            <th>Contact</th>
                            <th>Ville</th>
                            <th>Produit</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $c): ?>
                            <tr>
                                <td class="ps-3 fw-bold"><?= $c['id'] ?></td>
                                <td><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></td>
                                <td><?= htmlspecialchars($c['email']) ?><br><small><?= htmlspecialchars($c['telephone']) ?></small></td>
                                <td><?= htmlspecialchars($c['ville']) ?></td>
                                <td><?= htmlspecialchars($c['produit']) ?></td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $c['id'] ?>">Modifier</button>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?');">
                                        <input type="hidden" name="action" value="supprimer">
                                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Modal de modification -->
                            <div class="modal fade" id="editModal<?= $c['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Modifier le client : <?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <input type="hidden" name="action" value="modifier">
                                                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                                
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Nom *</label>
                                                        <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($c['nom'] ?? '') ?>" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Prénom *</label>
                                                        <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($c['prenom'] ?? '') ?>" required>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Téléphone</label>
                                                        <input type="text" name="telephone" class="form-control" value="<?= htmlspecialchars($c['telephone'] ?? '') ?>">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Email *</label>
                                                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($c['email'] ?? '') ?>" required>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Adresse</label>
                                                    <input type="text" name="adresse" class="form-control" value="<?= htmlspecialchars($c['adresse'] ?? '') ?>">
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Code Postal</label>
                                                        <input type="text" name="code_postal" class="form-control" value="<?= htmlspecialchars($c['code_postal'] ?? '') ?>">
                                                    </div>
                                                    <div class="col-md-8 mb-3">
                                                        <label class="form-label">Ville</label>
                                                        <input type="text" name="ville" class="form-control" value="<?= htmlspecialchars($c['ville'] ?? '') ?>">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-5 mb-3">
                                                        <label class="form-label">Dernier Produit</label>
                                                        <input type="text" name="produit" class="form-control" value="<?= htmlspecialchars($c['produit'] ?? '') ?>">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Date d'achat</label>
                                                        <input type="date" name="date_achat" class="form-control" value="<?= htmlspecialchars($c['date_achat'] ?? '') ?>">
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label">Prix (€)</label>
                                                        <input type="number" step="0.01" name="prix" class="form-control" value="<?= htmlspecialchars($c['prix'] ?? 0) ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/intranet_footer.php'; ?>
