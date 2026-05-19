<?php
// Charger le fichier JSON
$jsonData = file_get_contents("intranet_data-clients.json");
$clients = json_decode($jsonData, true)["clients"];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Intranet - Annuaire Clients</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f8f9fa;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }
        .search-box {
            max-width: 350px;
        }
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

<div class="container py-4">

    <h1 class="text-center mb-4">📁 Intranet – Annuaire Clients</h1>

    <div class="d-flex justify-content-center mb-3">
        <input type="text" id="search" class="form-control search-box" onkeyup="searchClient()" placeholder="Rechercher un client...">
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Contact</th>
                    <th>Adresse</th>
                    <th>Achats</th>
                    <th>SAV</th>
                    <th>Fidélité</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($clients as $client): ?>
                    <tr>
                        <td><?= $client["id"] ?></td>

                        <td>
                            <strong><?= $client["prenom"] . " " . $client["nom"] ?></strong><br>
                            <span class="badge bg-primary"><?= ucfirst($client["type_client"]) ?></span>
                        </td>

                        <td>
                            <div>📧 <?= $client["email"] ?></div>
                            <div>📞 <?= $client["telephone"] ?></div>
                        </td>

                        <td><?= $client["adresse"] ?></td>

                        <td>
                            <?php foreach ($client["historique_achats"] as $achat): ?>
                                <div class="mb-2">
                                    <strong><?= $achat["produit"] ?></strong><br>
                                    <small class="text-muted">
                                        Acheté le : <?= $achat["date_achat"] ?> — <?= $achat["prix"] ?> €
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        </td>

                        <td>
                            <?php if (count($client["sav"]) === 0): ?>
                                <span class="text-success">Aucun</span>
                            <?php else: ?>
                                <?php foreach ($client["sav"] as $sav): ?>
                                    <div class="text-danger fw-bold mb-2">
                                        Ticket <?= $sav["ticket_id"] ?><br>
                                        <?= $sav["probleme"] ?><br>
                                        <small class="text-muted">Statut : <?= $sav["statut"] ?></small>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </td>

                        <td>
                            <div>Achats : <?= $client["fidelite"]["total_achats"] ?></div>
                            <div>Total : <?= $client["fidelite"]["montant_total"] ?> €</div>
                            <div>Préférence : <?= $client["fidelite"]["categorie_preferee"] ?></div>
                            <div><strong><?= ucfirst($client["fidelite"]["score_client"]) ?></strong></div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
