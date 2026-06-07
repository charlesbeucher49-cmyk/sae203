<?php
require_once '../includes/intranet_fonctions.php';
verifierConnexion();
verifierDroits(['admin', 'direction', 'managers']);

// Traitement des exports
if (isset($_GET['export'])) {
    $type_export = $_GET['export'];
    $entite = $_GET['entite'] ?? '';

    if ($type_export === 'csv') {
        if ($entite === 'clients') {
            $clients = lireJSON('../data/intranet_data-clients.json') ?? [];
            exportCSV($clients, 'Clients');
        } elseif ($entite === 'employes') {
            $employes = lireJSON('../data/intranet_data-employes.json') ?? [];
            exportCSV($employes, 'Employes');
        } elseif ($entite === 'partenaires') {
            $data = lireJSON('../data/intranet_data-partenaires.json') ?? [];
            $partenaires = $data['fournisseurs'] ?? [];
            exportCSV($partenaires, 'Partenaires');
        }
    } elseif ($type_export === 'pdf') {
        if ($entite === 'clients') {
            $clients = lireJSON('../data/intranet_data-clients.json') ?? [];
            exportPDF($clients, 'Clients');
        } elseif ($entite === 'employes') {
            $employes = lireJSON('../data/intranet_data-employes.json') ?? [];
            exportPDF($employes, 'Employes');
        } elseif ($entite === 'partenaires') {
            $data = lireJSON('../data/intranet_data-partenaires.json') ?? [];
            $partenaires = $data['fournisseurs'] ?? [];
            exportPDF($partenaires, 'Partenaires');
        }
    }
    exit;
}

/**
 * Export en CSV
 */
function exportCSV($data, $name) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $name . '_' . date('Y-m-d_His') . '.csv');

    $output = fopen('php://output', 'w');
    
    if (!empty($data) && is_array($data[0])) {
        fputcsv($output, array_keys($data[0]));
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
    }

    fclose($output);
}

/**
 * Export en HTML (fallback simple PDF)
 */
function exportPDF($data, $name) {
    ob_start();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title><?= htmlspecialchars($name) ?></title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #1B2A4A; padding-bottom: 20px; }
            .header img { height: 60px; margin-bottom: 10px; }
            .header h1 { margin: 0; color: #1B2A4A; }
            .header p { margin: 5px 0; color: #666; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
            th { background-color: #1B2A4A; color: white; font-weight: bold; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .footer { margin-top: 40px; text-align: center; color: #666; border-top: 1px solid #ddd; padding-top: 20px; }
        </style>
    </head>
    <body>
        <div class="header">
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==" alt="Logo">
            <h1>TechRevive Solutions</h1>
            <p>Export: <?= htmlspecialchars($name) ?></p>
            <p><?= date('d/m/Y H:i:s') ?></p>
        </div>

        <table>
            <thead>
                <tr>
                    <?php if (!empty($data) && is_array($data[0])): ?>
                        <?php foreach (array_keys($data[0]) as $col): ?>
                            <th><?= htmlspecialchars($col) ?></th>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <?php foreach ($row as $cell): ?>
                            <td><?= htmlspecialchars(is_array($cell) ? json_encode($cell) : $cell) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="footer">
            <p>&copy; <?= date('Y') ?> TechRevive Solutions — Tous droits réservés</p>
        </div>
    </body>
    </html>
    <?php
    $html = ob_get_clean();

    // On affiche le HTML et on utilise html2pdf.js pour générer et télécharger le PDF automatiquement
    echo $html;
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
    window.onload = function() {
        const element = document.body;
        const opt = {
            margin:       10,
            filename:     '<?= $name ?>_<?= date('Y-m-d_His') ?>.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        // Génère le PDF et le télécharge, puis retourne à la page précédente
        html2pdf().set(opt).from(element).save().then(() => {
            setTimeout(() => { window.history.back(); }, 500);
        });
    };
    </script>
    <?php
}

$page_title = 'Exports — Intranet TechRevive';
$active_page = 'exports';
require_once '../includes/intranet_header.php';
?>

<div class="container mt-4 fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <svg width="28" height="28" fill="currentColor" viewBox="0 0 16 16" style="margin-right:12px;vertical-align:text-bottom;">
                <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5z"/>
            </svg>
            Exports Annuaires
        </h2>
        <a href="accueil_intranet.php" class="btn btn-outline-secondary">Retour</a>
    </div>

    <div class="row g-4">
        <!-- Export Clients -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width:42px;height:42px;background:rgba(27,42,74,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-right:12px;">
                            <svg width="22" height="22" fill="#1B2A4A" viewBox="0 0 16 16">
                                <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5z"/>
                            </svg>
                        </div>
                        <h5 class="card-title text-primary fw-bold mb-0">Clients</h5>
                    </div>
                    <p class="card-text text-secondary mb-4">Exportez la liste complète des clients en CSV ou PDF.</p>
                    <div class="d-grid gap-2">
                        <a href="?export=csv&entite=clients" class="btn btn-outline-primary">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right:8px;">
                                <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5z"/>
                            </svg>
                            CSV
                        </a>
                        <a href="?export=pdf&entite=clients" class="btn btn-primary">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right:8px;">
                                <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5z"/>
                            </svg>
                            PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Employés -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width:42px;height:42px;background:rgba(45,106,46,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-right:12px;">
                            <svg width="22" height="22" fill="#2D6A2E" viewBox="0 0 16 16">
                                <path d="M8 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-9.5a.5.5 0 1 1 1 0 .5.5 0 0 1-1 0zm.5 3.5a.5.5 0 1 0 0 1 .5.5 0 0 0 0-1zm-5-3.5a.5.5 0 1 1 1 0 .5.5 0 0 1-1 0zm.5 3.5a.5.5 0 1 0 0 1 .5.5 0 0 0 0-1z"/>
                            </svg>
                        </div>
                        <h5 class="card-title text-primary fw-bold mb-0">Employés</h5>
                    </div>
                    <p class="card-text text-secondary mb-4">Exportez la liste complète des employés en CSV ou PDF.</p>
                    <div class="d-grid gap-2">
                        <a href="?export=csv&entite=employes" class="btn btn-outline-primary">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right:8px;">
                                <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5z"/>
                            </svg>
                            CSV
                        </a>
                        <a href="?export=pdf&entite=employes" class="btn btn-primary">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right:8px;">
                                <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5z"/>
                            </svg>
                            PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Partenaires -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width:42px;height:42px;background:rgba(232,168,56,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-right:12px;">
                            <svg width="22" height="22" fill="#e8a838" viewBox="0 0 16 16">
                                <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.5-1a1.5 1.5 0 1 0 3 0 1.5 1.5 0 0 0-3 0zM11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                            </svg>
                        </div>
                        <h5 class="card-title text-primary fw-bold mb-0">Partenaires</h5>
                    </div>
                    <p class="card-text text-secondary mb-4">Exportez la liste complète des partenaires en CSV ou PDF.</p>
                    <div class="d-grid gap-2">
                        <a href="?export=csv&entite=partenaires" class="btn btn-outline-primary">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right:8px;">
                                <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5z"/>
                            </svg>
                            CSV
                        </a>
                        <a href="?export=pdf&entite=partenaires" class="btn btn-primary">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right:8px;">
                                <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5z"/>
                            </svg>
                            PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <div class="alert alert-info alert-dismissible fade show">
                <strong>💡 Info!</strong> Les exports sont générés en temps réel avec les données actuelles. CSV pour usage tableur, PDF pour impression.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/intranet_footer.php'; ?>
