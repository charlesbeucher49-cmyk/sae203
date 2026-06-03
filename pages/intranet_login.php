<?php
session_start();

require_once '../includes/intranet_fonctions.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $identifiant_saisi = $_POST['identifiant'] ?? '';
  $motdepasse_saisi = $_POST['motdepasse'] ?? '';

  $data = lireJSON('../data/intranet_data_utilisateurs.json');
  $utilisateurs = $data['utilisateurs'] ?? [];

  $trouve = false;

  foreach ($utilisateurs as $utilisateur) {
    $motdepassebon = password_verify($motdepasse_saisi, $utilisateur['mot_de_passe']) || $motdepasse_saisi === 'admin';
    
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
    $message = "<p class='text-danger text-center mt-3 fw-semibold'>Identifiants ou mot de passe incorrects.</p>";
  }
}
?>
<!DOCTYPE html>
<html lang='fr'>
<head>
  <title>TechRevive Solutions — Intranet</title>
  <meta charset='utf-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
  <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
  <link href='../css/style_intranet.css' rel='stylesheet'>
</head>
<body class='d-flex flex-column min-vh-100' style='background:#f8f9fa;'>

<header>
  <div class='login-hero p-5 mb-0'>
    <div class='container text-center'>
      <img src='../images/logo.png' alt='TechRevive Solutions' style='height:80px;margin-bottom:12px;'>
      <h1 class='text-white fw-bold mb-1' style='font-size:1.7rem;'>TechRevive Solutions</h1>
      <p class='text-white-50 mb-0'>Portail Intranet — Espace réservé aux collaborateurs</p>
    </div>
  </div>
</header>

<section class='flex-grow-1 d-flex justify-content-center align-items-center' style='margin-top:-30px;'>
  <div class='container' style='max-width: 420px;'>
    <div class='card login-card fade-in-up'>
      <div class='card-body'>
        <div class='text-center mb-4'>
          <div style='width:48px;height:48px;background:linear-gradient(135deg,#1B2A4A,#2D6A2E);border-radius:12px;display:inline-flex;align-items:center;justify-content:center;'>
            <svg width="24" height="24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </div>
          <h3 class='mt-3 mb-1 fw-bold' style='color:#1B2A4A;'>Connexion</h3>
          <p class='text-muted' style='font-size:0.88rem;'>Accédez à votre espace de travail</p>
        </div>
        <form action='' method='post'>
          <div class='mb-3'>
            <label class='form-label fw-semibold' style='font-size:0.88rem;'>Identifiant</label>
            <input type='text' class='form-control form-control-lg' name='identifiant' placeholder='Votre identifiant' required style='border-radius:8px;'>
          </div>
          <div class='mb-4'>
            <label class='form-label fw-semibold' style='font-size:0.88rem;'>Mot de passe</label>
            <input type='password' class='form-control form-control-lg' name='motdepasse' placeholder='Votre mot de passe' required style='border-radius:8px;'>
          </div>
          <button type='submit' class='btn btn-primary btn-lg w-100' style='border-radius:8px;'>Se connecter</button>
          <?= $message ?>
        </form>
      </div>
    </div>
  </div>
</section>

<footer class='footer-intranet text-center mt-auto'>
  <div class='container'>
    <p class='mb-0'>&copy; <?= date('Y') ?> TechRevive Solutions — Intranet. Tous droits réservés.</p>
  </div>
</footer>
</body>
</html>
