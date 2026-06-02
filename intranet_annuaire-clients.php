<?php
// Charger le fichier JSON
$jsonData = file_get_contents("intranet_data-clients.json");
$clients = json_decode($jsonData, true);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Intranet - Annuaire Clients</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style_intranet.css" rel="stylesheet">

    <style>
        .table-hover tbody tr:hover { background-color: #f1f1f1; }
        .search-box { max-width: 350px; }
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
                    <th>Achat</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($clients as $client): ?>
                    <tr>
                        <td><?= $client["id"] ?></td>

                        <td>
                            <strong><?= $client["prenom"] . " " . $client["nom"] ?></strong>
                        </td>

                        <td>
                            <div>📧 <?= $client["email"] ?></div>
                            <div>📞 <?= $client["telephone"] ?></div>
                        </td>

                        <td>
                            <?= $client["adresse"] ?><br>
                            <?= $client["code_postal"] . " " . $client["ville"] ?>
                        </td>

                        <td>
                            <strong><?= $client["produit"] ?></strong><br>
                            <small class="text-muted">
                                Acheté le : <?= $client["date_achat"] ?> — <?= $client["prix"] ?> €
                            </small>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
