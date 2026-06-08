<?php
require_once '../includes/intranet_fonctions.php';

$dataFile = '../data/intranet_data_utilisateurs.json';
$data = lireJSON($dataFile);

if (!empty($data) && isset($data['utilisateurs'])) {
    // Remplacer les faux mots de passe par le mot de passe réel : "admin"
    foreach ($data['utilisateurs'] as &$u) {
        $u['mot_de_passe'] = password_hash('admin', PASSWORD_DEFAULT);
    }
    
    sauvegarderJSON($dataFile, $data);
    echo "<h1>Succès !</h1><p>Tous les mots de passe ont été réinitialisés à <strong>admin</strong>.</p><a href='pages/intranet_login.php'>Aller se connecter</a>";
} else {
    echo "Erreur : Fichier introuvable ou vide.";
}
?>
