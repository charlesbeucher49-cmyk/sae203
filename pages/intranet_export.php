<?php
require_once '../includes/intranet_fonctions.php';
verifierConnexion();
verifierDroits(['admin', 'direction', 'managers']);

// Traitement des exports
if (isset($_GET['export'])) {
    $type_export = $_GET['export'];
    $entite = $_GET['entite'] ?? '';

    // Chargement des données (factorisé — une seule fois)
    $data = [];
    if ($entite === 'clients') {
        $data = lireJSON('../data/intranet_data-clients.json') ?? [];
    } elseif ($entite === 'employes') {
        $data = lireJSON('../data/intranet_data-employes.json') ?? [];
    } elseif ($entite === 'partenaires') {
        $raw = lireJSON('../data/intranet_data-partenaires.json') ?? [];
        $data = $raw['fournisseurs'] ?? [];
    }

    // Export selon le format demandé
    if (!empty($data)) {
        $nom = ucfirst($entite);
        if ($type_export === 'csv') {
            exportCSV($data, $nom);
        } elseif ($type_export === 'pdf') {
            exportPDF($data, $nom);
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
            // Aplatir les arrays imbriquées (ex: type_produits) en string lisible
            $flat = array_map(function($v) {
                return is_array($v) ? implode(', ', $v) : $v;
            }, $row);
            fputcsv($output, $flat);
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
            <img src="<?= 'data:image/png;base64,' . base64_encode(file_get_contents('../images/logo.png')) ?>" alt="Logo">
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

<div class="container mt-4">
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
                                <path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM3.517 14.841a1.13 1.13 0 0 0 .401.823c.13.108.289.192.478.252.19.061.411.091.665.091.338 0 .624-.053.859-.158.236-.105.416-.252.539-.44.125-.189.187-.408.187-.656 0-.224-.045-.41-.134-.56a1.001 1.001 0 0 0-.375-.357 2.027 2.027 0 0 0-.566-.21l-.621-.144a.97.97 0 0 1-.404-.176.37.37 0 0 1-.144-.299c0-.156.062-.284.185-.384.125-.101.296-.152.512-.152.143 0 .266.023.37.068a.624.624 0 0 1 .246.181.56.56 0 0 1 .12.258h.75a1.092 1.092 0 0 0-.2-.566 1.21 1.21 0 0 0-.5-.41 1.813 1.813 0 0 0-.78-.152c-.293 0-.551.05-.776.15-.225.099-.4.24-.527.421-.127.182-.19.395-.19.639 0 .201.04.376.122.524.082.149.2.27.352.367.152.095.332.167.539.213l.618.144c.207.049.361.111.463.186a.366.366 0 0 1 .152.298c0 .156-.06.29-.18.4-.119.11-.296.165-.533.165-.258 0-.46-.058-.603-.174a.559.559 0 0 1-.211-.38H3.517Zm4.425-2.096v3.21H6.602v-3.21h1.34Zm2.493 3.21h-1.341l-1.38-3.21h1.411l.732 1.93h.03l.718-1.93h1.413l-1.583 3.21Z"/>
                            </svg>
                            CSV
                        </a>
                        <a href="?export=pdf&entite=clients" class="btn btn-primary">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right:8px;">
                                <path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM1.6 11.85H0v3.999h.791v-1.342h.803c.287 0 .531-.057.732-.173.203-.117.358-.275.463-.474a1.42 1.42 0 0 0 .161-.677c0-.25-.053-.476-.158-.677a1.176 1.176 0 0 0-.46-.477c-.2-.12-.443-.179-.732-.179Zm.545 1.333a.795.795 0 0 1-.085.38.574.574 0 0 1-.238.241.794.794 0 0 1-.375.082H.788V12.48h.66c.218 0 .389.06.512.181.123.122.185.296.185.522Zm1.217-1.333v3.999h1.46c.401 0 .734-.08.998-.237a1.45 1.45 0 0 0 .595-.689c.13-.3.196-.662.196-1.084 0-.42-.065-.778-.196-1.075a1.426 1.426 0 0 0-.589-.68c-.264-.156-.599-.234-1.005-.234H3.362Zm.791.645h.563c.249 0 .45.05.603.154a.89.89 0 0 1 .315.434c.068.188.104.42.104.697 0 .288-.035.52-.104.698a.891.891 0 0 1-.315.434c-.153.104-.354.155-.603.155h-.563v-2.572Zm3.734 3.354h1.033v-1.11h.9A1.05 1.05 0 0 0 10.64 14.5a1 1 0 0 0 .285-.347c.07-.123.105-.268.105-.436v-.026c0-.188-.046-.34-.139-.454a.784.784 0 0 0-.395-.275.9.9 0 0 0 .426-.263c.12-.13.18-.302.18-.518v-.025c0-.168-.035-.313-.105-.436a.996.996 0 0 0-.285-.346 1.05 1.05 0 0 0-.422-.218 1.832 1.832 0 0 0-.52-.072H7.888v3.999Zm.791-2.091v-1.266h.423c.21 0 .363.045.461.135.097.09.146.216.146.38v.025c0 .17-.049.301-.146.39-.098.087-.251.131-.461.131h-.423Zm0 1.474v-1.285h.455c.22 0 .381.047.484.14.103.093.154.225.154.394v.025c0 .152-.05.277-.152.375-.102.097-.26.146-.475.146h-.466Z"/>
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
                                <path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM3.517 14.841a1.13 1.13 0 0 0 .401.823c.13.108.289.192.478.252.19.061.411.091.665.091.338 0 .624-.053.859-.158.236-.105.416-.252.539-.44.125-.189.187-.408.187-.656 0-.224-.045-.41-.134-.56a1.001 1.001 0 0 0-.375-.357 2.027 2.027 0 0 0-.566-.21l-.621-.144a.97.97 0 0 1-.404-.176.37.37 0 0 1-.144-.299c0-.156.062-.284.185-.384.125-.101.296-.152.512-.152.143 0 .266.023.37.068a.624.624 0 0 1 .246.181.56.56 0 0 1 .12.258h.75a1.092 1.092 0 0 0-.2-.566 1.21 1.21 0 0 0-.5-.41 1.813 1.813 0 0 0-.78-.152c-.293 0-.551.05-.776.15-.225.099-.4.24-.527.421-.127.182-.19.395-.19.639 0 .201.04.376.122.524.082.149.2.27.352.367.152.095.332.167.539.213l.618.144c.207.049.361.111.463.186a.366.366 0 0 1 .152.298c0 .156-.06.29-.18.4-.119.11-.296.165-.533.165-.258 0-.46-.058-.603-.174a.559.559 0 0 1-.211-.38H3.517Zm4.425-2.096v3.21H6.602v-3.21h1.34Zm2.493 3.21h-1.341l-1.38-3.21h1.411l.732 1.93h.03l.718-1.93h1.413l-1.583 3.21Z"/>
                            </svg>
                            CSV
                        </a>
                        <a href="?export=pdf&entite=employes" class="btn btn-primary">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right:8px;">
                                <path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM1.6 11.85H0v3.999h.791v-1.342h.803c.287 0 .531-.057.732-.173.203-.117.358-.275.463-.474a1.42 1.42 0 0 0 .161-.677c0-.25-.053-.476-.158-.677a1.176 1.176 0 0 0-.46-.477c-.2-.12-.443-.179-.732-.179Zm.545 1.333a.795.795 0 0 1-.085.38.574.574 0 0 1-.238.241.794.794 0 0 1-.375.082H.788V12.48h.66c.218 0 .389.06.512.181.123.122.185.296.185.522Zm1.217-1.333v3.999h1.46c.401 0 .734-.08.998-.237a1.45 1.45 0 0 0 .595-.689c.13-.3.196-.662.196-1.084 0-.42-.065-.778-.196-1.075a1.426 1.426 0 0 0-.589-.68c-.264-.156-.599-.234-1.005-.234H3.362Zm.791.645h.563c.249 0 .45.05.603.154a.89.89 0 0 1 .315.434c.068.188.104.42.104.697 0 .288-.035.52-.104.698a.891.891 0 0 1-.315.434c-.153.104-.354.155-.603.155h-.563v-2.572Zm3.734 3.354h1.033v-1.11h.9A1.05 1.05 0 0 0 10.64 14.5a1 1 0 0 0 .285-.347c.07-.123.105-.268.105-.436v-.026c0-.188-.046-.34-.139-.454a.784.784 0 0 0-.395-.275.9.9 0 0 0 .426-.263c.12-.13.18-.302.18-.518v-.025c0-.168-.035-.313-.105-.436a.996.996 0 0 0-.285-.346 1.05 1.05 0 0 0-.422-.218 1.832 1.832 0 0 0-.52-.072H7.888v3.999Zm.791-2.091v-1.266h.423c.21 0 .363.045.461.135.097.09.146.216.146.38v.025c0 .17-.049.301-.146.39-.098.087-.251.131-.461.131h-.423Zm0 1.474v-1.285h.455c.22 0 .381.047.484.14.103.093.154.225.154.394v.025c0 .152-.05.277-.152.375-.102.097-.26.146-.475.146h-.466Z"/>
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
                                <path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM3.517 14.841a1.13 1.13 0 0 0 .401.823c.13.108.289.192.478.252.19.061.411.091.665.091.338 0 .624-.053.859-.158.236-.105.416-.252.539-.44.125-.189.187-.408.187-.656 0-.224-.045-.41-.134-.56a1.001 1.001 0 0 0-.375-.357 2.027 2.027 0 0 0-.566-.21l-.621-.144a.97.97 0 0 1-.404-.176.37.37 0 0 1-.144-.299c0-.156.062-.284.185-.384.125-.101.296-.152.512-.152.143 0 .266.023.37.068a.624.624 0 0 1 .246.181.56.56 0 0 1 .12.258h.75a1.092 1.092 0 0 0-.2-.566 1.21 1.21 0 0 0-.5-.41 1.813 1.813 0 0 0-.78-.152c-.293 0-.551.05-.776.15-.225.099-.4.24-.527.421-.127.182-.19.395-.19.639 0 .201.04.376.122.524.082.149.2.27.352.367.152.095.332.167.539.213l.618.144c.207.049.361.111.463.186a.366.366 0 0 1 .152.298c0 .156-.06.29-.18.4-.119.11-.296.165-.533.165-.258 0-.46-.058-.603-.174a.559.559 0 0 1-.211-.38H3.517Zm4.425-2.096v3.21H6.602v-3.21h1.34Zm2.493 3.21h-1.341l-1.38-3.21h1.411l.732 1.93h.03l.718-1.93h1.413l-1.583 3.21Z"/>
                            </svg>
                            CSV
                        </a>
                        <a href="?export=pdf&entite=partenaires" class="btn btn-primary">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right:8px;">
                                <path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM1.6 11.85H0v3.999h.791v-1.342h.803c.287 0 .531-.057.732-.173.203-.117.358-.275.463-.474a1.42 1.42 0 0 0 .161-.677c0-.25-.053-.476-.158-.677a1.176 1.176 0 0 0-.46-.477c-.2-.12-.443-.179-.732-.179Zm.545 1.333a.795.795 0 0 1-.085.38.574.574 0 0 1-.238.241.794.794 0 0 1-.375.082H.788V12.48h.66c.218 0 .389.06.512.181.123.122.185.296.185.522Zm1.217-1.333v3.999h1.46c.401 0 .734-.08.998-.237a1.45 1.45 0 0 0 .595-.689c.13-.3.196-.662.196-1.084 0-.42-.065-.778-.196-1.075a1.426 1.426 0 0 0-.589-.68c-.264-.156-.599-.234-1.005-.234H3.362Zm.791.645h.563c.249 0 .45.05.603.154a.89.89 0 0 1 .315.434c.068.188.104.42.104.697 0 .288-.035.52-.104.698a.891.891 0 0 1-.315.434c-.153.104-.354.155-.603.155h-.563v-2.572Zm3.734 3.354h1.033v-1.11h.9A1.05 1.05 0 0 0 10.64 14.5a1 1 0 0 0 .285-.347c.07-.123.105-.268.105-.436v-.026c0-.188-.046-.34-.139-.454a.784.784 0 0 0-.395-.275.9.9 0 0 0 .426-.263c.12-.13.18-.302.18-.518v-.025c0-.168-.035-.313-.105-.436a.996.996 0 0 0-.285-.346 1.05 1.05 0 0 0-.422-.218 1.832 1.832 0 0 0-.52-.072H7.888v3.999Zm.791-2.091v-1.266h.423c.21 0 .363.045.461.135.097.09.146.216.146.38v.025c0 .17-.049.301-.146.39-.098.087-.251.131-.461.131h-.423Zm0 1.474v-1.285h.455c.22 0 .381.047.484.14.103.093.154.225.154.394v.025c0 .152-.05.277-.152.375-.102.097-.26.146-.475.146h-.466Z"/>
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
