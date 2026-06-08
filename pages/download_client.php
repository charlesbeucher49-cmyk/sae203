<?php
// session_start() enlevé car déjà dans verifierConnexion() si besoin (ou on s'assure d'inclure fonctions)
require_once '../includes/intranet_fonctions.php';
verifierConnexion();

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID client non spécifié.");
}

$clients = lireJSON("../data/intranet_data-clients.json");
$client = null;
foreach ($clients as $c) {
    if ($c['id'] == $id) {
        $client = $c;
        break;
    }
}

if (!$client) {
    die("Client introuvable.");
}

$filename = "fiche_client_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $client['nom'] . "_" . $client['prenom']) . ".pdf";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Fiche Client - <?= htmlspecialchars($client['nom']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #333; }
        .header { border-bottom: 2px solid #1B2A4A; padding-bottom: 20px; margin-bottom: 30px; text-align: center; }
        .header img { height: 60px; margin-bottom: 10px; }
        .header h1 { color: #1B2A4A; margin: 0; }
        .section { margin-bottom: 30px; }
        .section h2 { background-color: #f4f4f4; padding: 10px; border-left: 4px solid #1B2A4A; font-size: 1.2rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { width: 30%; color: #555; }
        .footer { margin-top: 40px; text-align: center; font-size: 0.9rem; color: #777; border-top: 1px solid #ddd; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="<?= 'data:image/png;base64,' . base64_encode(file_get_contents('../images/logo.png')) ?>" alt="Logo">
        <h1>Fiche Client</h1>
        <p>TechRevive Solutions</p>
    </div>

    <div class="section">
        <h2>Informations Générales</h2>
        <table>
            <tr><th>ID Client</th><td><?= htmlspecialchars($client['id']) ?></td></tr>
            <tr><th>Nom complet</th><td><strong><?= htmlspecialchars(mb_strtoupper($client['nom'])) ?> <?= htmlspecialchars($client['prenom']) ?></strong></td></tr>
            <tr><th>Téléphone</th><td><?= htmlspecialchars($client['telephone']) ?></td></tr>
            <tr><th>Email</th><td><?= htmlspecialchars($client['email']) ?></td></tr>
        </table>
    </div>

    <div class="section">
        <h2>Adresse</h2>
        <table>
            <tr><th>Adresse</th><td><?= htmlspecialchars($client['adresse']) ?></td></tr>
            <tr><th>Code Postal</th><td><?= htmlspecialchars($client['code_postal']) ?></td></tr>
            <tr><th>Ville</th><td><?= htmlspecialchars(mb_strtoupper($client['ville'])) ?></td></tr>
        </table>
    </div>

    <div class="section">
        <h2>Dernier Achat</h2>
        <table>
            <tr><th>Produit</th><td><?= htmlspecialchars($client['produit']) ?></td></tr>
            <tr><th>Date d'achat</th><td><?= htmlspecialchars($client['date_achat']) ?></td></tr>
            <tr><th>Montant</th><td><strong><?= htmlspecialchars($client['prix']) ?> €</strong></td></tr>
        </table>
    </div>

    <div class="footer">
        Document généré le <?= date('d/m/Y à H:i:s') ?> — Confidentiel
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
    window.onload = function() {
        const element = document.body;
        const opt = {
            margin:       10,
            filename:     '<?= $filename ?>',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        html2pdf().set(opt).from(element).save().then(() => {
            setTimeout(() => { window.history.back(); }, 500);
        });
    };
    </script>
</body>
</html>
