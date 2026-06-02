<?php
require_once 'intranet_fonctions.php';
verifierConnexion();

$employes = lireJSON("intranet_data-employes.json");

$page_title = 'Annuaire Employés — Intranet TechRevive';
$active_page = 'annuaire_employes';
require_once 'intranet_header.php';
?>

<style>
    .employee-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
        background: #fff;
    }
    .employee-card:hover {
        box-shadow: 0 8px 28px rgba(27,42,74,0.12);
        transform: translateY(-4px);
    }
    .employee-card .card-header-bar {
        height: 6px;
        background: linear-gradient(90deg, #1B2A4A, #2D6A2E);
    }
    .employee-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1B2A4A, #2D6A2E);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 auto;
    }
    img.employee-avatar {
        object-fit: cover;
    }
</style>
<script>
    function searchEmployee() {
        let input = document.getElementById("search").value.toLowerCase();
        let cards = document.querySelectorAll(".employee-col");
        cards.forEach(card => {
            let text = card.innerText.toLowerCase();
            card.style.display = text.includes(input) ? "" : "none";
        });
    }
</script>

<div class="container mt-4 fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <h2 class="fw-bold mb-0" style="color:#1B2A4A;">Annuaire des Employés</h2>
            <?php if (in_array('admin', $groupes_user) || in_array('direction', $groupes_user) || in_array('managers', $groupes_user)): ?>
                <a href="intranet_gestion-employes.php" class="btn btn-outline-primary btn-sm">⚙️ Gérer</a>
            <?php endif; ?>
        </div>
        <span class="badge" style="background:#2D6A2E;font-size:0.9rem;padding:6px 14px;"><?= count($employes) ?> collaborateurs</span>
    </div>

    <div class="d-flex justify-content-center mb-4">
        <input type="text" id="search" class="form-control" onkeyup="searchEmployee()" placeholder="🔍 Rechercher un employé..." style="max-width:400px;">
    </div>

    <div class="row g-4">
        <?php foreach ($employes as $emp): ?>
        <div class="col-md-4 col-lg-3 employee-col">
            <div class="employee-card h-100">
                <div class="card-header-bar"></div>
                <div class="p-4 text-center">
                    <?php if (!empty($emp['photo']) && file_exists($emp['photo'])): ?>
                        <img src="<?= htmlspecialchars($emp['photo']) ?>" alt="<?= htmlspecialchars($emp['prenom'] . ' ' . $emp['nom']) ?>" class="employee-avatar mb-3">
                    <?php else: ?>
                        <div class="employee-avatar mb-3">
                            <?= strtoupper(mb_substr($emp['prenom'], 0, 1) . mb_substr($emp['nom'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <h6 class="fw-bold mb-1" style="color:#1B2A4A;"><?= htmlspecialchars($emp['prenom'] . ' ' . $emp['nom']) ?></h6>
                    <span class="badge mb-2" style="background:rgba(45,106,46,0.1);color:#2D6A2E;font-weight:600;"><?= htmlspecialchars($emp['fonction']) ?></span>
                    <p class="text-muted mt-2 mb-0" style="font-size:0.85rem;"><?= htmlspecialchars($emp['bio']) ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'intranet_footer.php'; ?>
