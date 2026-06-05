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

/**
 * Enregistre une action dans le journal d'audit
 * @param string $action Type d'action (CREATE, UPDATE, DELETE, VIEW)
 * @param string $entite Type d'entité (client, employe, partenaire, utilisateur)
 * @param string $details Détails supplémentaires
 * @param string $ancienneDonnee Valeur avant modification
 * @param string $nouvelleDonnee Valeur après modification
 */
function enregistrerAudit($action, $entite, $details = '', $ancienneDonnee = '', $nouvelleDonnee = '') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $auditFile = __DIR__ . '/../data/audit_log.json';
    $logs = lireJSON($auditFile);
    if (!is_array($logs)) {
        $logs = [];
    }
    
    $entry = [
        'id' => uniqid(),
        'timestamp' => date('Y-m-d H:i:s'),
        'user' => $_SESSION['login'] ?? 'unknown',
        'user_nom' => $_SESSION['nom'] ?? '',
        'user_prenom' => $_SESSION['prenom'] ?? '',
        'action' => strtoupper($action),
        'entite' => $entite,
        'details' => $details,
        'ancienne_donnee' => $ancienneDonnee,
        'nouvelle_donnee' => $nouvelleDonnee,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
    ];
    
    $logs[] = $entry;
    sauvegarderJSON($auditFile, $logs);
}

/**
 * Crée une notification utilisateur
 * @param string $titre Titre de la notification
 * @param string $message Message de la notification
 * @param string $type Type (info, success, warning, danger)
 * @param string $icon_class Classe Bootstrap Icon
 */
function creerNotification($titre, $message, $type = 'info', $icon_class = 'info-circle') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $notifFile = __DIR__ . '/../data/notifications.json';
    $notifs = lireJSON($notifFile);
    if (!is_array($notifs)) {
        $notifs = [];
    }
    
    $notification = [
        'id' => uniqid(),
        'timestamp' => date('Y-m-d H:i:s'),
        'titre' => $titre,
        'message' => $message,
        'type' => $type,
        'icon' => $icon_class,
        'lu' => false,
        'user' => $_SESSION['login'] ?? 'all'
    ];
    
    $notifs[] = $notification;
    sauvegarderJSON($notifFile, $notifs);
}

/**
 * Récupère les statistiques globales
 */
function obtenirStatistiques() {
    $stats = [
        'total_clients' => 0,
        'total_employes' => 0,
        'total_partenaires' => 0,
        'total_utilisateurs' => 0,
        'notifications_non_lues' => 0
    ];
    
    $clients = lireJSON(__DIR__ . '/../data/intranet_data-clients.json');
    $stats['total_clients'] = is_array($clients) ? count($clients) : 0;
    
    $employes = lireJSON(__DIR__ . '/../data/intranet_data-employes.json');
    $stats['total_employes'] = is_array($employes) ? count($employes) : 0;
    
    $partenaires = lireJSON(__DIR__ . '/../data/intranet_data-partenaires.json');
    if (is_array($partenaires) && isset($partenaires['fournisseurs'])) {
        $stats['total_partenaires'] = count($partenaires['fournisseurs']);
    }
    
    $utilisateurs = lireJSON(__DIR__ . '/../data/intranet_data_utilisateurs.json');
    if (is_array($utilisateurs) && isset($utilisateurs['utilisateurs'])) {
        $stats['total_utilisateurs'] = count($utilisateurs['utilisateurs']);
    }
    
    $notifs = lireJSON(__DIR__ . '/../data/notifications.json');
    if (is_array($notifs)) {
        $stats['notifications_non_lues'] = count(array_filter($notifs, function($n) {
            return !$n['lu'];
        }));
    }
    
    return $stats;
}
