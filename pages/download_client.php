<?php
session_start();
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

$filename = "fiche_client_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $client['nom'] . "_" . $client['prenom']) . ".txt";

header('Content-Type: text/plain; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

echo "====================================\n";
echo "          FICHE CLIENT\n";
echo "====================================\n\n";

echo "ID : " . $client['id'] . "\n";
echo "Nom : " . mb_strtoupper($client['nom']) . "\n";
echo "Prénom : " . $client['prenom'] . "\n";
echo "Téléphone : " . $client['telephone'] . "\n";
echo "Email : " . $client['email'] . "\n";
echo "Adresse : " . $client['adresse'] . "\n";
echo "Code Postal : " . $client['code_postal'] . "\n";
echo "Ville : " . mb_strtoupper($client['ville']) . "\n\n";

echo "====================================\n";
echo "          DERNIER ACHAT\n";
echo "====================================\n\n";

echo "Produit : " . $client['produit'] . "\n";
echo "Date d'achat : " . $client['date_achat'] . "\n";
echo "Prix : " . $client['prix'] . " €\n";
?>
