<?php

session_start();
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

<?php
// Récupération des éventuels messages passés en GET (après redirection)
$success = isset($_GET['success']) && $_GET['success'] === '1';
$errors  = isset($_GET['errors'])  ? json_decode(urldecode($_GET['errors']), true) : [];
$old     = isset($_GET['old'])     ? json_decode(urldecode($_GET['old']), true) : [];

header("Content-Security-Policy: default-src 'self'; script-src 'self' cdn.jsdelivr.net; style-src 'self' cdn.jsdelivr.net 'unsafe-inline';");
header("X-Frame-Options: SAMEORIGIN");  
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Formulaire de commande</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet" 
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
        crossorigin="anonymous">
  
  <style>
    .form-section {
      background: #f8f9fa;
      padding: 2.5rem;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
  </style>
</head>
<body class="bg-light">

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">

      <h2 class="text-center mb-4">Formulaire de commande</h2>

      <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong>Commande enregistrée avec succès !</strong><br>
          Nous vous contacterons très bientôt.
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <strong>Des erreurs ont été détectées :</strong>
          <ul class="mb-0">
            <?php foreach ($errors as $err): ?>
              <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form class="form-section" action="traitment.php" method="post">
        <div class="row g-4">

          <!-- Colonne 1 -->
          <div class="col-md-6">
            <div class="mb-3">
              <label for="prenom" class="form-label">Prénom</label>
              <input type="text" class="form-control" id="prenom" name="prenom" required 
                     value="<?= htmlspecialchars($old['prenom'] ?? '') ?>" pattern="[A-Za-zÀ-ÿ\s'-]+">
            </div>

            <div class="mb-3">
              <label for="name" class="form-label">Nom</label>
              <input placeholder="entrez votre nom" type="text" class="form-control" id="name" name="name" required 
                     value="<?= htmlspecialchars($old['name'] ?? '') ?>" pattern="[A-Za-zÀ-ÿ\s'-]+">
            </div>

            <div class="mb-3">
              <label for="sexe" class="form-label">Sexe</label>
              <select class="form-select" id="sexe" name="sexe" required>
                <option value="">Sélectionnez...</option>
                <option value="homme"   <?= ($old['sexe'] ?? '') === 'homme'   ? 'selected' : '' ?>>Homme</option>
                <option value="femme"   <?= ($old['sexe'] ?? '') === 'femme'   ? 'selected' : '' ?>>Femme</option>
                <option value="autre"   <?= ($old['sexe'] ?? '') === 'autre'   ? 'selected' : '' ?>>Autre</option>
              </select>
            </div>

            <div class="mb-3">
              <label for="age" class="form-label">Âge</label>
              <input type="number" class="form-control" id="age" name="age" min="18" required 
                     value="<?= htmlspecialchars($old['age'] ?? '') ?>">
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" required 
                     value="<?= htmlspecialchars($old['email'] ?? '') ?>">
            </div>
          </div>

          <!-- Colonne 2 -->
          <div class="col-md-6">
            <div class="mb-3">
              <label for="marque" class="form-label">Marque souhaitée</label>
              <input type="text" class="form-control" id="marque" name="marque" required 
                     value="<?= htmlspecialchars($old['marque'] ?? '') ?>">
            </div>

            <div class="mb-3">
              <label for="adresse" class="form-label">Adresse de livraison</label>
              <input type="text" class="form-control" id="adresse" name="adresse" required 
                     value="<?= htmlspecialchars($old['adresse'] ?? '') ?>">
            </div>

            <div class="mb-3">
              <label for="date" class="form-label">Date de livraison souhaitée</label>
              <input type="date" class="form-control" id="date" name="date" required 
                     value="<?= htmlspecialchars($old['date'] ?? '') ?>">
            </div>

            <div class="mb-3">
              <label for="message" class="form-label">Commentaires</label>
              <textarea class="form-control" id="message" name="message" rows="5"><?= htmlspecialchars($old['message'] ?? '') ?></textarea>
            </div>
          </div>

        </div>

        <div class="text-center mt-5">
          <button type="submit" class="btn btn-success btn-lg px-5">Valider </button> 
          <br>
          <button type="submit" class="btn btn btn-lg px-10"><a href="commandes.php">Suivre ma commande</a>  </button>
                    <button type="submit" class="btn btn btn-lg px-5"><a href="login_admin.php">Espace admin</a>  </button> 
 
        </div>
      </form>

    </div>
  
   
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    // On ajoute 1 jour → la date minimale devient demain
    today.setDate(today.getDate() + 1);
    
    // Format YYYY-MM-DD attendu par input type="date"
    const minDate = today.toISOString().split('T')[0];
    
    document.getElementById('date').setAttribute('min', minDate);
    
    // Optionnel : message plus clair si l'utilisateur tente une date invalide
    document.getElementById('date').addEventListener('invalid', function(e) {
        if (this.validity.rangeUnderflow) {
            this.setCustomValidity('La date de livraison doit être à partir de demain.');
        }
    });
    
    document.getElementById('date').addEventListener('input', function() {
        this.setCustomValidity(''); // reset message d'erreur quand corrigé
    });
});
</script>
</body>
</html>