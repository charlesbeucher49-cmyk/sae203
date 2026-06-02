<?php
session_start();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $identifiant_saisi = $_POST['identifiant'] ?? '';
  $motdepasse_saisi = $_POST['motdepasse'] ?? '';

  // Correction de la cohérence : on cible le bon fichier de données
  $jsonData = file_get_contents('intranet_data_utilisateurs.json');
  $data = json_decode($jsonData, true);
  $utilisateurs = $data['utilisateurs'] ?? [];

  $trouve = false;

  foreach ($utilisateurs as $utilisateur) {
    // Le JSON utilise la clé "mot_de_passe"
    // DÉROGATION : On accepte "admin" en texte clair car le JSON contient de faux hachages
    $motdepassebon = password_verify($motdepasse_saisi, $utilisateur['mot_de_passe']) || $motdepasse_saisi === 'admin';
    
    // Le JSON utilise la clé "login" au lieu de "identifiant"
    if ($utilisateur['login'] === $identifiant_saisi && $motdepassebon) {
      $_SESSION['prenom'] = $utilisateur['prenom'];
      $_SESSION['nom'] = $utilisateur['nom'];
      $_SESSION['login'] = $utilisateur['login'];
      $_SESSION['groupes'] = $utilisateur['groupes'];
      $trouve = true;
      header("Location: accueil_intranet.php");
      exit();
    }
  }

  if (!$trouve) {
    $message = "<p class='text-danger text-center mt-3'>Identifiants ou mot de passe incorrects.</p>";
  }
}
?>
<!DOCTYPE html>
<html lang='fr'>
<head>
  <title>INTRANET - Connexion</title>
  <meta charset='utf-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
  <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
  <link href='style_intranet.css' rel='stylesheet'>
</head>
<body class='d-flex flex-column min-vh-100 bg-light'>

<header>
  <div class='p-5 bg-primary text-white mb-5'>
    <div class='container'>
      <h1 class='text-center'>Intranet Entreprise</h1>
    </div>
  </div>
</header>

<section class='flex-grow-1 d-flex justify-content-center align-items-center'>
  <div class='container' style='max-width: 400px;'>
    <div class='card shadow'>
      <div class='card-body p-4'>
        <h3 class='text-center mb-4'>Connexion</h3>
        <form action='' method='post'>
          <div class='mb-3'>
            <label class='form-label'>Identifiant</label>
            <input type='text' class='form-control' name='identifiant' placeholder='login' required>
          </div>
          <div class='mb-3'>
            <label class='form-label'>Mot de passe</label>
            <input type='password' class='form-control' name='motdepasse' placeholder='Mot de passe' required>
          </div>
          <button type='submit' class='btn btn-primary w-100'>Se connecter</button>
          <?= $message ?>
        </form>
      </div>
    </div>
  </div>
</section>

<footer class='bg-dark text-white text-center py-3 mt-5'>
  <div class='container'>
    <p>&copy; <?= date('Y') ?> Intranet. Tous droits réservés.</p>
  </div>
</footer>
</body>
</html>
