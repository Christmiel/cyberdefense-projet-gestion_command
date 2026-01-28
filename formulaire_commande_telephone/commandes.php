<?php
// suivi.php

header("Content-Security-Policy: default-src 'self'; script-src 'self' cdn.jsdelivr.net; style-src 'self' cdn.jsdelivr.net 'unsafe-inline';");
header("X-Frame-Options: SAMEORIGIN");  // Ou DENY
$email = '';
$commandes = [];
$erreur = '';
$trouve = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $erreur = "Veuillez entrer votre adresse email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "L'adresse email n'est pas valide.";
    } else {
        try {
            $pdo = new PDO(
                "mysql:host=localhost;dbname=commandes;charset=utf8",
                "root",
                "",
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]
            );

            $stmt = $pdo->prepare("
                SELECT id, prenom, nom, marque, adresse, date_livraison, statut, created_at 
                FROM commandes 
                WHERE email = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$email]);
            $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $trouve = !empty($commandes);

            if (!$trouve) {
                $erreur = "Aucune commande trouvée pour cet email.";
            }

        } catch (PDOException $e) {
            $erreur = "Erreur technique. Veuillez réessayer plus tard.";
             //En production : error_log($e);
             var_dump($e); // À supprimer en production
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Suivi de commande</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .form-section { background: #f8f9fa; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .status-en-attente    { background: #ffc107; color: black; }
    .status-traitée { background: #0d6efd; color: white; }
    .status-livrée      { background: #198754; color: white; }
    .status-annulee       { background: #dc3545; color: white; }
  </style>
</head>
<body class="bg-light">

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">

      <h2 class="text-center mb-4">Suivi de vos commandes</h2>

      <?php if ($erreur): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
      <?php endif; ?>

      <div class="form-section mb-5">
        <form method="post" action="">
          <div class="mb-3">
            <label for="email" class="form-label fw-bold">Votre adresse email</label>
            <input type="email" class="form-control form-control-lg" id="email" name="email" 
                   value="<?= htmlspecialchars($email) ?>" required 
                   placeholder="exemple@domaine.com">
          </div>
          <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg px-5">Voir mes commandes</button>
          </div>
        </form>
      </div>

      <?php if ($trouve): ?>
        <h4 class="text-center mb-4">Vos commandes (<?= count($commandes) ?>)</h4>

        <div class="table-responsive">
          <table class="table table-hover table-bordered align-middle">
            <thead class="table-dark">
              <tr>
                <th>Date</th>
                <th>Prénom Nom</th>
                <th>Marque / Produit</th>
                <th>Adresse</th>
                <th>Date livraison souhaitée</th>
                <th>Statut</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($commandes as $cmd): ?>
                <tr>
                  <td><?= date('d/m/Y H:i', strtotime($cmd['created_at'])) ?></td>
                  <td><?= htmlspecialchars($cmd['prenom'] . ' ' . $cmd['nom']) ?></td>
                  <td><?= htmlspecialchars($cmd['marque'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars(substr($cmd['adresse'], 0, 40)) ?>...</td>
                  <td><?= $cmd['date_livraison'] ? date('d/m/Y', strtotime($cmd['date_livraison'])) : '—' ?></td>
                  <td>
                    <span class="badge rounded-pill px-3 py-2 status-<?= strtolower(str_replace(' ', '-', $cmd['statut'])) ?>">
                      <?= htmlspecialchars($cmd['statut']) ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="alert alert-info mt-4 small text-center">
          Pour toute question, contactez-nous via l'email utilisé pour la commande.
        </div>

      <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): 
        ?>
        
        <div class="alert alert-warning text-center">
          Aucune commande enregistrée avec cet email pour le moment.
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>