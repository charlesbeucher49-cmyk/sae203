//Formulaire ajout partenaire
elseif (isset($_POST['ajoutPart'])){
  echo "<br>";
  echo "<form action='scripts\ajoutPart.php' method='post'>
    <div class='container' style='max-width: 400px;'>
    <h2>Ajout d'un partenaire</h2>
      <div class='form-group mb-3'>
        <label>Nom</label>
        <input type='text' class='form-control' name='nom' placeholder='Nom' required>
      </div>
      <div class='form-group mb-3'>
        <label>Logo</label>
        <input type='text' class='form-control' name='logo' placeholder='logo' required>
      </div>
      <div class='form-group mb-3'>
        <label>Description</label>
        <input type='text' class='form-control' name='desc' placeholder='Description' required>
      </div>
      <button type='submit' class='btn btn-primary w-100'>Ajouter à l'annuaire</button>
      <p class='text-danger text-center mt-3'>Veuillez remplir tous les champs</p>
    </form>
  </div>";
}
