function changerPage(nomPage) {
    // 1. On récupère toutes nos sections de page
    let toutesLesPages = document.querySelectorAll('.onglet-contenu');
    
    // 2. On les cache toutes en boucle
    toutesLesPages.forEach(page => {
        page.style.display = 'none';
    });
    
    // 3. On affiche uniquement la page demandée
    let pageAAfficher = document.getElementById('page-' + nomPage);
    if (pageAAfficher) {
        pageAAfficher.style.display = 'block';
    }
}
