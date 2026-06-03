<?php
require_once '../includes/intranet_fonctions.php';
verifierConnexion();

$clients = lireJSON("../data/intranet_data-clients.json");

$page_title = 'Annuaire Clients — Intranet TechRevive';
$active_page = 'annuaire_clients';
require_once '../includes/intranet_header.php';
?>

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

<div class="container mt-4 fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <h2 class="fw-bold mb-0" style="color:var(--bs-primary);">Annuaire Clients</h2>
            <?php if (in_array('admin', $_SESSION['groupes'] ?? []) || in_array('direction', $_SESSION['groupes'] ?? []) || in_array('managers', $_SESSION['groupes'] ?? [])): ?>
                <a href="intranet_gestion-clients.php" class="btn btn-outline-primary btn-sm">⚙️ Gérer</a>
            <?php endif; ?>
        </div>
        <span class="badge" style="background:var(--bs-primary);font-size:0.9rem;padding:6px 14px;"><?= count($clients) ?> clients</span>
    </div>

    <div class="d-flex justify-content-center mb-4">
        <input type="text" id="search" class="form-control search-box" onkeyup="searchClient()" placeholder="🔍 Rechercher un client (nom, ville, produit...)">
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:var(--bs-primary); color:#fff;">
                        <tr>
                            <th class="ps-3">ID</th>
                            <th>Client</th>
                            <th>Contact</th>
                            <th>Adresse</th>
                            <th>Achat</th>
                            <th class="text-end pe-3">Action</th>
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
                                <td class="text-end pe-3">
                                    <a href="download_client.php?id=<?= $client['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Télécharger la fiche">📄</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/intranet_footer.php'; ?>
