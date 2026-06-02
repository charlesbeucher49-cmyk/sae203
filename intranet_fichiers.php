<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: intranet_login.php');
    exit();
}

$groupes_user = $_SESSION['groupes'] ?? [];
// Map 'salariés' to 'salaries' to match folder name safely
$groupes_user = array_map(function($g) { return ($g === 'salariés') ? 'salaries' : $g; }, $groupes_user);

$all_folders = ['admin', 'direction', 'managers', 'salaries', 'perso'];

$allowed_folders = [];
// Les admin et la direction ont accès à tous les dossiers
if (in_array('admin', $groupes_user) || in_array('direction', $groupes_user)) {
    $allowed_folders = $all_folders;
} else {
    // Les autres n'ont accès qu'à leurs propres groupes
    $allowed_folders = array_intersect($all_folders, $groupes_user);
}

// Vérification du dossier actuel
$current_folder = $_GET['folder'] ?? ($allowed_folders[0] ?? null);
if (!in_array($current_folder, $allowed_folders) && $current_folder !== null) {
    die("<div class='alert alert-danger m-4'>Accès refusé à ce dossier.</div>");
}

$base_dir = __DIR__ . "/uploads/";
$message = "";

// Traitement de l'Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fichier'])) {
    if ($current_folder) {
        $file = $_FILES['fichier'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext === 'txt' || $ext === 'csv') {
            $dest = $base_dir . $current_folder . '/' . basename($file['name']);
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $message = "<div class='alert alert-success'>Fichier uploadé avec succès.</div>";
            } else {
                $message = "<div class='alert alert-danger'>Erreur lors de l'upload.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Seuls les fichiers .txt et .csv sont autorisés.</div>";
        }
    }
}

// Traitement de la suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file'])) {
    if ($current_folder) {
        $file_to_delete = basename($_POST['delete_file']);
        $path_to_delete = $base_dir . $current_folder . '/' . $file_to_delete;
        if (file_exists($path_to_delete)) {
            unlink($path_to_delete);
            $message = "<div class='alert alert-success'>Fichier supprimé avec succès.</div>";
        }
    }
}

// Traitement du téléchargement
if (isset($_GET['download']) && $current_folder) {
    $file_to_download = basename($_GET['download']);
    $path_to_download = $base_dir . $current_folder . '/' . $file_to_download;
    if (file_exists($path_to_download)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($path_to_download).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path_to_download));
        readfile($path_to_download);
        exit;
    }
}

// Liste des fichiers du dossier
$files = [];
if ($current_folder && is_dir($base_dir . $current_folder)) {
    $files = array_diff(scandir($base_dir . $current_folder), ['.', '..']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionnaire de Fichiers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style_intranet.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="accueil_intranet.php">Intranet Entreprise</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="accueil_intranet.php">Accueil</a></li>
        <li class="nav-item"><a class="nav-link active text-primary" href="intranet_fichiers.php">Fichiers partagés</a></li>
      </ul>
      <span class="navbar-text me-3">Connecté : <strong><?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?></strong></span>
      <a href="intranet_logout.php" class="btn btn-outline-danger btn-sm">Se déconnecter</a>
    </div>
  </div>
</nav>

<div class="container">
    <div class="row">
        <!-- Colonne de gauche : Dossiers -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    Mes Dossiers
                </div>
                <div class="list-group list-group-flush">
                    <?php if (empty($allowed_folders)): ?>
                        <div class="list-group-item text-muted">Aucun dossier accessible</div>
                    <?php endif; ?>
                    <?php foreach ($allowed_folders as $folder): ?>
                        <a href="?folder=<?= urlencode($folder) ?>" class="list-group-item list-group-item-action <?= ($current_folder === $folder) ? 'active bg-primary border-primary text-dark fw-bold' : '' ?>">
                            📁 <?= ucfirst($folder) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Colonne de droite : Fichiers -->
        <div class="col-md-9">
            <h2 class="mb-4 text-primary">Dossier : <?= htmlspecialchars(ucfirst($current_folder ?? 'Aucun')) ?></h2>
            <?= $message ?>

            <?php if ($current_folder): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form action="?folder=<?= urlencode($current_folder) ?>" method="POST" enctype="multipart/form-data" class="d-flex align-items-center">
                            <input class="form-control me-3" type="file" name="fichier" accept=".txt,.csv" required>
                            <button type="submit" class="btn btn-primary text-nowrap">⬆️ Uploader (.txt, .csv)</button>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-3">Nom du fichier</th>
                                    <th>Taille</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($files)): ?>
                                    <tr><td colspan="3" class="text-center py-4 text-muted">Ce dossier est vide.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($files as $file): ?>
                                        <?php 
                                        $fpath = $base_dir . $current_folder . '/' . $file;
                                        $fsize = file_exists($fpath) ? round(filesize($fpath) / 1024, 2) . ' Ko' : '0 Ko';
                                        ?>
                                        <tr>
                                            <td class="ps-3">📄 <?= htmlspecialchars($file) ?></td>
                                            <td><?= $fsize ?></td>
                                            <td class="text-end pe-3">
                                                <a href="?folder=<?= urlencode($current_folder) ?>&download=<?= urlencode($file) ?>" class="btn btn-sm btn-success">⬇️ Télécharger</a>
                                                
                                                <form method="POST" action="?folder=<?= urlencode($current_folder) ?>" style="display:inline;" onsubmit="return confirm('Voulez-vous vraiment supprimer ce fichier ?');">
                                                    <input type="hidden" name="delete_file" value="<?= htmlspecialchars($file) ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">❌ Supprimer</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">Sélectionnez un dossier pour voir les fichiers.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
