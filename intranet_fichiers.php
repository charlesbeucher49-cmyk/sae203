<?php
require_once 'intranet_fonctions.php';
verifierConnexion();

$groupes_user = $_SESSION['groupes'] ?? [];
$groupes_user = array_map(function($g) { return ($g === 'salariés') ? 'salaries' : $g; }, $groupes_user);

$all_folders = ['admin', 'direction', 'managers', 'salaries', 'perso'];

$allowed_folders = [];
if (in_array('admin', $groupes_user) || in_array('direction', $groupes_user)) {
    $allowed_folders = $all_folders;
} else {
    $allowed_folders = array_intersect($all_folders, $groupes_user);
}

$current_folder = $_GET['folder'] ?? ($allowed_folders[0] ?? null);
if (!in_array($current_folder, $allowed_folders) && $current_folder !== null) {
    die("<div class='alert alert-danger m-4'>Accès refusé à ce dossier.</div>");
}

$base_dir = __DIR__ . "/uploads/";
$message = "";

// Traitement POST global
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Suppression
    if (isset($_POST['delete_file'])) {
        if ($current_folder) {
            $file_to_delete = basename($_POST['delete_file']);
            $path_to_delete = $base_dir . $current_folder . '/' . $file_to_delete;
            if (file_exists($path_to_delete)) {
                unlink($path_to_delete);
                $message = "<div class='alert alert-success alert-dismissible fade show'><strong>Succès !</strong> Fichier supprimé avec succès. <button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
            }
        }
    }
    // Upload
    elseif (isset($_FILES['fichier']) && $_FILES['fichier']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($current_folder) {
            $file = $_FILES['fichier'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $message = "<div class='alert alert-danger'>Erreur lors de l'upload (code {$file['error']}). Vérifiez la configuration PHP.</div>";
            } else {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if ($ext === 'txt' || $ext === 'csv') {
                    $dest = $base_dir . $current_folder . '/' . basename($file['name']);
                    if (!is_dir($base_dir . $current_folder)) {
                        mkdir($base_dir . $current_folder, 0775, true);
                    }
                    if (move_uploaded_file($file['tmp_name'], $dest)) {
                        $message = "<div class='alert alert-success alert-dismissible fade show'><strong>Succès !</strong> Fichier '{$file['name']}' uploadé avec succès. <button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
                    } else {
                        $message = "<div class='alert alert-danger'>Erreur : impossible de déplacer le fichier.</div>";
                    }
                } else {
                    $message = "<div class='alert alert-danger'>Format refusé : seuls les fichiers <strong>.txt</strong> et <strong>.csv</strong> sont autorisés.</div>";
                }
            }
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

$page_title = 'Fichiers Partagés — Intranet TechRevive';
$active_page = 'fichiers';
require_once 'intranet_header.php';
?>

<div class="container mt-4 fade-in-up">
    <div class="row">
        <!-- Colonne de gauche : Dossiers -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-header" style="background:var(--tr-navy);color:#fff;border-bottom:3px solid var(--tr-green);">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:6px;"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                    Mes Dossiers
                </div>
                <div class="list-group list-group-flush">
                    <?php if (empty($allowed_folders)): ?>
                        <div class="list-group-item text-muted">Aucun dossier accessible</div>
                    <?php endif; ?>
                    <?php foreach ($allowed_folders as $folder): ?>
                        <a href="?folder=<?= urlencode($folder) ?>" class="list-group-item list-group-item-action <?= ($current_folder === $folder) ? 'active' : '' ?>">
                            📁 <?= ucfirst($folder) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Colonne de droite : Fichiers -->
        <div class="col-md-9">
            <h2 class="mb-4 fw-bold" style="color:#1B2A4A;">
                📂 Dossier : <?= htmlspecialchars(ucfirst($current_folder ?? 'Aucun')) ?>
            </h2>
            <?= $message ?>

            <?php if ($current_folder): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <form action="?folder=<?= urlencode($current_folder) ?>" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-3">
                            <input class="form-control" type="file" name="fichier" accept=".txt,.csv" required>
                            <button type="submit" class="btn btn-success text-nowrap">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:4px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                Uploader
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card">
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
                                                <a href="?folder=<?= urlencode($current_folder) ?>&download=<?= urlencode($file) ?>" class="btn btn-sm btn-success">Télécharger</a>
                                                <form method="POST" action="?folder=<?= urlencode($current_folder) ?>" style="display:inline;" onsubmit="return confirm('Supprimer ce fichier ?');">
                                                    <input type="hidden" name="delete_file" value="<?= htmlspecialchars($file) ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
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

<?php require_once 'intranet_footer.php'; ?>
