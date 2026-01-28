<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=commandes;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Email et mot de passe requis.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, prenom, email, password, role 
                                   FROM commandes WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt = $pdo->prepare("SELECT id, nom, email, password, role 
                       FROM commandes 
                       WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($password, $user['password'])) {
            session_start();
                // Connexion réussie
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['prenom']  = $user['prenom'];
                $_SESSION['role']      = $user['role'];
                $_SESSION['logged_in'] = true; 

                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Identifiants incorrects.";
            }
        } catch (PDOException $e) {
            var_dump($e); // À supprimer en production
             //En production :
            error_log($e->getMessage());
            $error = "Erreur technique.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container">
  <div class="row justify-content-center mt-5">
    <div class="col-md-5">
      <div class="card shadow">
        <div class="card-body p-4">
          <h3 class="text-center mb-4">Espace Administration</h3>

          <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <form method="post">
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
              <label class="form-label">Mot de passe</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>