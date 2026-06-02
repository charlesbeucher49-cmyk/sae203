<?php
// intranet_fonctions.php

/**
 * Vérifie si l'utilisateur est connecté. Si non, le redirige vers la page de connexion.
 */
function verifierConnexion() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['login'])) {
        header('Location: intranet_login.php');
        exit();
    }
}

/**
 * Vérifie si l'utilisateur possède au moins un des groupes autorisés.
 * @param array $groupesAutorises Tableau des groupes autorisés (ex: ['admin', 'direction'])
 */
function verifierDroits($groupesAutorises) {
    verifierConnexion();
    $groupes_user = $_SESSION['groupes'] ?? [];
    
    $accesOk = false;
    foreach ($groupesAutorises as $grp) {
        if (in_array($grp, $groupes_user)) {
            $accesOk = true;
            break;
        }
    }
    
    if (!$accesOk) {
        echo "<h1>Accès refusé. Vous n'avez pas les droits nécessaires.</h1>";
        echo "<a href='accueil_intranet.php'>Retour à l'accueil</a>";
        exit();
    }
}

/**
 * Lit un fichier JSON et retourne le tableau associatif correspondant.
 * @param string $fichier Le chemin vers le fichier JSON
 * @return array Le contenu du JSON ou un tableau vide
 */
function lireJSON($fichier) {
    if (!file_exists($fichier)) {
        return [];
    }
    $json = file_get_contents($fichier);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

/**
 * Sauvegarde un tableau associatif dans un fichier JSON.
 * @param string $fichier Le chemin vers le fichier JSON
 * @param array $data Les données à sauvegarder
 */
function sauvegarderJSON($fichier, $data) {
    file_put_contents($fichier, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
