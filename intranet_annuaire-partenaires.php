<?php
require_once 'intranet_fonctions.php';
verifierConnexion();

$data = lireJSON("intranet_data-partenaires.json");
if (!$data || !isset($data["fournisseurs"])) {
    die("<div class='alert alert-danger m-4'>Erreur : Le fichier JSON est vide ou mal formaté.</div>");
}
$fournisseurs = $data["fournisseurs"];

$search = isset($_GET["search"]) ? strtolower(trim($_GET["search"])) : "";

$page_title = 'Annuaire Partenaires — Intranet TechRevive';
$active_page = 'annuaire_partenaires';
require_once 'intranet_header.php';
?>

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

<?php require_once 'intranet_footer.php'; ?>
