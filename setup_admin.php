<?php
$dataFile = 'data/intranet_data_utilisateurs.json';
if (file_exists($dataFile)) {
    $data = json_decode(file_get_contents($dataFile), true);
    
    // Remplacer les faux mots de passe par le mot de passe réel : "admin"
    foreach ($data['utilisateurs'] as &$u) {
        $u['mot_de_passe'] = password_hash('admin', PASSWORD_DEFAULT);
    }
    
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "<h1>Succès !</h1><p>Tous les mots de passe ont été réinitialisés à <strong>admin</strong>.</p><a href='intranet_login.php'>Aller se connecter</a>";
} else {
    echo "Erreur : Fichier introuvable.";
}
?>
