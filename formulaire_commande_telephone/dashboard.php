<?php
session_start();

// Protection : accès réservé aux admin et manager
if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], ['admin', 'manager'])) {
    ?>
    <h1>Accès interdit</h1>

    <?php
    header("Location: login_admin.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=commandes;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Changement de statut (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commande_id'], $_POST['nouveau_statut'])) {
    $id      = (int)$_POST['commande_id'];
    $statut  = $_POST['nouveau_statut'];

    // Vérification CSRF (à ajouter plus tard pour plus de sécurité)
    $stmt = $pdo->prepare("UPDATE commandes SET statut = ? WHERE id = ?");
    $stmt->execute([$statut, $id]);

    header("Location: dashboard.php?msg=Statut+modifié");
    exit;
}

// Suppression (POST) → UNIQUEMENT admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id']) && $_SESSION['role'] === 'admin') {
    $id = (int)$_POST['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM commandes WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: dashboard.php?msg=Commande+supprimée");
    exit;
}

// Liste des commandes
$stmt = $pdo->query("SELECT id, prenom, nom, email, marque, adresse, date_livraison, statut, created_at 
                     FROM commandes ORDER BY created_at DESC");
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Tableau de bord Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <span class="navbar-brand">Administration – <?= htmlspecialchars($_SESSION['prenom']) ?> (<?= $_SESSION['role'] ?>)</span>
    <a href="login_admin.php" class="btn btn-outline-light btn-sm">Déconnexion</a>
  </div>
</nav>

<div class="container mt-4">

  <?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
  <?php endif; ?>

  <h2>Liste des commandes (<?= count($commandes) ?>)</h2>

  <?php if (empty($commandes)): ?>
    <div class="alert alert-info">Aucune commande pour le moment.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Client</th>
            <th>Email</th>
            <th>Marque</th>
            <th>Adresse</th>
            <th>Date souhaitée</th>
            <th>Statut</th>
            <th>Date commande</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($commandes as $cmd): ?>
            <tr>
              <td><?= $cmd['id'] ?></td>
              <td><?= htmlspecialchars($cmd['prenom'] . ' ' . $cmd['nom']) ?></td>
              <td><?= htmlspecialchars($cmd['email']) ?></td>
              <td><?= htmlspecialchars($cmd['marque']) ?></td>
              <td><?= htmlspecialchars(substr($cmd['adresse'], 0, 40)) ?>...</td>
              <td><?= $cmd['date_livraison'] ?></td>
              <td>
                <form method="post" class="d-inline">
                  <input type="hidden" name="commande_id" value="<?= $cmd['id'] ?>">
                  <select name="nouveau_statut" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                    <?php
                    $statuts = ['En attente', 'Traitée', 'Livrée'];
                    foreach ($statuts as $s):
                    ?>
                      <option value="<?= $s ?>" <?= $cmd['statut'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                  </select>
                </form>
              </td>
              <td><?= date('d/m/Y H:i', strtotime($cmd['created_at'])) ?></td>
              <td>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                  <form method="post" class="d-inline" onsubmit="return confirm('Supprimer cette commande ?');">
                    <input type="hidden" name="delete_id" value="<?= $cmd['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>