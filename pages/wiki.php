<?php
session_start();
/*if (!isset($_SESSION['prenom'])) {
  header("Location:portail_connexion.php");
  exit;
}*/

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
  <title>INTRANET</title>
  <meta charset='utf-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
  <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
  <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'></script>
</head>
<body class='d-flex flex-column min-vh-100'>
<header>
  <div>
  <nav class='navbar navbar-expand-lg navbar-dark bg-dark'>
    <div class='container-fluid'>
      <a class='navbar-brand' href='./accueil_intranet.php'>GMG</a>
      <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarNav'>
        <span class='navbar-toggler-icon'></span>
      </button>
      <div class='collapse navbar-collapse' id='navbarNav'>
        <ul class='navbar-nav me-auto'>
          <li class='nav-item'>
            <a class='nav-link text-light' href='./annuaire.php'>Annuaire</a>
          </li>
          <li class='nav-item'>
            <a class='nav-link text-light' href='./gestion_fichier.php'>Gestion fichier</a>
          </li>
          <li class='nav-item'>
            <a class='nav-link text-light' href='./wiki.php'>Wiki</a>
          </li>
          <li>
            <a class='nav-link text-light' href='./profil.php'>Mon profil</a>
          </li>
        </ul>
        <div class='d-flex align-items-center'>";
          if (isset($_SESSION['prenom']) && isset($_SESSION['nom']) && isset($_SESSION['fonction'])) {
            echo "<span class='text-light me-2'>Connecté en tant que ". htmlspecialchars($_SESSION['prenom']) . " ". htmlspecialchars($_SESSION['nom']) . ", ". htmlspecialchars($_SESSION['fonction']). "</span>";
            echo "<a href='./portail_deconnexion.php' class='btn btn-outline-light btn-sm'>Se déconnecter</a>";
          }
        echo "
        </div>
      </div>
    </div>
  </nav>
  </div>
  <div class='jumbotron jumbotron-fluid p-5 bg-primary text-white'>
    <div class='container'>
      <h1 class='text-center'>Bienvenue sur le Wiki</h1>
      <p class='text-center'>Retrouvez ici les pages utilisées dans le projet et leur utilité</p>
    </div>
  </div>
</header>

<section class='flex-grow-1 container my-5'>
  <div class='table-responsive'>
    <table class='table table-bordered table-hover'>
      <thead class='table-dark'>
        <tr>
          <th>Nom</th>
          <th>Fichier ou Lien</th>
          <th>Description / Utilité</th>
        </tr>
      </thead>
      <tbody>
  <tr>
    <td>Connexion</td>
    <td>portail_connexion.php</td>
    <td>Permet aux utilisateurs de se connecter à l’intranet avec leurs identifiants.</td>
  </tr>
  <tr>
    <td>Accueil</td>
    <td>accueil_intranet.php</td>
    <td>Page d’accueil après connexion, point de départ vers les autres sections.</td>
  </tr>
  <tr>
    <td>Annuaire</td>
    <td>annuaire.php</td>
    <td>Affiche les informations des utilisateurs enregistrés.</td>
  </tr>
  <tr>
    <td>Gestion des fichiers</td>
    <td>gestion_fichier.php</td>
    <td>Page prévue pour ajouter ou consulter des fichiers partagés.</td>
  </tr>
  <tr>
    <td>Wiki</td>
    <td>wiki.php</td>
    <td>Page actuelle contenant la documentation et l’explication des fichiers utilisés.</td>
  </tr>
  <tr>
    <td>Déconnexion</td>
    <td>portail_deconnexion.php</td>
    <td>Termine la session utilisateur et redirige vers la page de connexion.</td>
  </tr>
  <tr>
    <td>Documentation Apache</td>
    <td><a href='https://httpd.apache.org/' target='_blank'>httpd.apache.org</a></td>
    <td>Doc officielle d’Apache HTTP Server, pour configurer et comprendre le serveur web.</td>
  </tr>
  <tr>
    <td>PHP Documentation</td>
    <td><a href='https://www.php.net/manual/fr/' target='_blank'>php.net</a></td>
    <td>Référence officielle PHP pour fonctions, syntaxe et bonnes pratiques.</td>
  </tr>
  <tr>
    <td>Bootstrap Docs</td>
    <td><a href='https://getbootstrap.com/docs/5.3/' target='_blank'>getbootstrap.com</a></td>
    <td>Docs Bootstrap 5.3, utilisée pour styliser et structurer l’interface.</td>
  </tr>
  <tr>
    <td>W3Schools</td>
    <td><a href='https://www.w3schools.com/' target='_blank'>w3schools.com</a></td>
    <td>Site d’apprentissage web (HTML, CSS, JS, PHP), idéal pour bases et références rapides.</td>
  </tr>
  <tr>
    <td>MDN Web Docs</td>
    <td><a href='https://developer.mozilla.org/fr/' target='_blank'>developer.mozilla.org</a></td>
    <td>Documentation complète HTML, CSS, JS et technologies web.</td>
  </tr>
  <tr>
    <td>Stack Overflow</td>
    <td><a href='https://stackoverflow.com' target='_blank'>stackoverflow.com</a></td>
    <td>Site d’entraide pour développeurs, utile pour résoudre des problèmes et trouver des exemples.</td>
  </tr>
  <tr>
    <td>Documentation MySQL</td>
    <td><a href='https://dev.mysql.com/doc/' target='_blank'>dev.mysql.com/doc/</a></td>
    <td>Doc officielle MySQL : syntaxe SQL et administration des bases.</td>
  </tr>
  <tr>
    <td>phpMyAdmin</td>
    <td><a href='https://www.phpmyadmin.net/' target='_blank'>phpmyadmin.net</a></td>
    <td>Interface web pour gérer facilement une base MySQL.</td>
  </tr>
  <tr>
    <td>CyberChef</td>
    <td><a href='https://gchq.github.io/CyberChef/' target='_blank'>gchq.github.io/CyberChef/</a></td>
    <td>Outil en ligne pour encoder, décoder, hasher et déboguer des chaînes.</td>
  </tr>
  <tr>
    <td>Regex101</td>
    <td><a href='https://regex101.com/' target='_blank'>regex101.com</a></td>
    <td>Test et débogage interactif d’expressions régulières (RegEx).</td>
  </tr>
  <tr>
    <td>Draw.io</td>
    <td><a href='https://app.diagrams.net/' target='_blank'>app.diagrams.net</a></td>
    <td>Outil de création de diagrammes UML, BDD, architecture.</td>
  </tr>
</tbody>

    </table>
  </div>
  <h3 class='mt-5'>Identifiants de test (mdp nécessaires)</h3>
  <div class='table-responsive'>
    <table class='table table-bordered table-hover'>
      <thead class='table-dark'>
        <tr>
          <th>Nom</th>
          <th>Identifiant</th>
          <th>Mot de passe</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>David GATEL</td>
          <td>dagatel</td>
          <td>bonjour</td>
        </tr>
        <tr>
          <td>François-Régis Menguy</td>
          <td>frmenguy</td>
          <td>bonjour</td>
        </tr>
        <tr>
          <td>Aloïs Guitton</td>
          <td>alguitton</td>
          <td>bonjour</td>
        </tr>
        <tr>
          <td>user user</td>
          <td>ususer</td>
          <td>bonjour</td>
        </tr>
        <tr>
          <td>admin admin</td>
          <td>adadmin</td>
          <td>bonjour</td>
        </tr>
      </tbody>
    </table>
  </div>
</section>
</section>

<footer class='bg-dark text-white text-center py-3'>
  <div class='container'>
    <p>&copy; ". date('Y') ." Intranet. Tous droits réservés.</p>
  </div>
</footer>
</body>
</html>";
?>
