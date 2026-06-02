<?php
// Chargement du JSON
$jsonFile = "intranet_data-partenaires.json"; // Ton fichier JSON

if (!file_exists($jsonFile)) {
    die("<div class='alert alert-danger m-4'>Erreur : Le fichier JSON des fournisseurs est introuvable.</div>");
}

$data = json_decode(file_get_contents($jsonFile), true);

if (!$data || !isset($data["fournisseurs"])) {
    die("<div class='alert alert-danger m-4'>Erreur : Le fichier JSON est vide ou mal formaté.</div>");
}

$fournisseurs = $data["fournisseurs"];

// Recherche
$search = isset($_GET["search"]) ? strtolower(trim($_GET["search"])) : "";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Annuaire des Partenaires</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style_intranet.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">

    <h1 class="text-center mb-4">📚 Annuaire des Fournisseurs Partenaires</h1>

    <!-- Barre de recherche -->
    <form method="GET" class="mb-4">
        <input type="text" name="search" class="form-control form-control-lg"
               placeholder="Rechercher un partenaire (nom, ville, produit...)"
               value="<?= htmlspecialchars($search) ?>">
    </form>

    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Nom</th>
                        <th>Contact</th>
                        <th>Adresse</th>
                        <th>Produits</th>
                        <th style="width: 100px;">Fiabilité</th>
                    </tr>
                </thead>
                <tbody>

                <?php
                $resultFound = false;

                foreach ($fournisseurs as $f) {

                    // Filtre de recherche
                    $haystack = strtolower(
                        $f["nom"] . " " .
                        $f["ville"] . " " .
                        implode(" ", $f["type_produits"])
                    );

                    if ($search !== "" && !str_contains($haystack, $search)) {
                        continue;
                    }

                    $resultFound = true;
                ?>

                    <tr>
                        <td class="fw-bold"><?= $f["id"] ?></td>

                        <td>
                            <strong><?= htmlspecialchars($f["nom"]) ?></strong><br>
                            <span class="text-muted">Partenaire officiel</span>
                        </td>

                        <td>
                            <div>📞 <?= htmlspecialchars($f["telephone"]) ?></div>
                            <div>✉️ <a href="mailto:<?= htmlspecialchars($f["email"]) ?>">
                                <?= htmlspecialchars($f["email"]) ?>
                            </a></div>
                        </td>

                        <td>
                            <?= htmlspecialchars($f["adresse"]) ?><br>
                            <?= htmlspecialchars($f["code_postal"]) . " " . htmlspecialchars($f["ville"]) ?><br>
                            <span class="text-muted"><?= htmlspecialchars($f["pays"]) ?></span>
                        </td>

                        <td>
                            <?php foreach ($f["type_produits"] as $p): ?>
                                <span class="badge bg-primary me-1"><?= htmlspecialchars($p) ?></span>
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

</body>
</html>
