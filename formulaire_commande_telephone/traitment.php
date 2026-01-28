<?php
session_start();
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $errors[] = "Token CSRF invalide.";
}
unset($_SESSION['csrf_token']); 

if (!isset($_SESSION['submit_count'])) $_SESSION['submit_count'] = 0;
if ($_SESSION['submit_count'] > 5) { 
    $errors[] = "Trop de soumissions. Réessayez plus tard.";
} else {
    $_SESSION['submit_count']++;
}
$errors = [];
$old = $_POST; 

// Nettoyage basique
$prenom  = trim($_POST['prenom']  ?? '');
$nom     = trim($_POST['name']    ?? '');
$sexe    = trim($_POST['sexe']    ?? '');
$age     = trim($_POST['age']     ?? '');
$email   = trim($_POST['email']   ?? '');
$marque  = trim($_POST['marque']  ?? '');
$adresse = trim($_POST['adresse'] ?? '');
$date    = trim($_POST['date']    ?? '');
$message = trim($_POST['message'] ?? '');


// Validations
if (empty($prenom))    $errors[] = "Le prénom est obligatoire.";
if (empty($nom))       $errors[] = "Le nom est obligatoire.";
if (!preg_match('/^[A-Za-zÀ-ÿ\s\'-]+$/u', subject: $prenom)) {
    $errors[] = "Le prénom ne doit contenir que des lettres, espaces, tirets ou apostrophes.";
}

if (!preg_match('/^[A-Za-zÀ-ÿ\s\'-]+$/u', $nom)) {
    $errors[] = "Le nom ne doit contenir que des lettres, espaces, tirets ou apostrophes.";
}
if (empty($sexe) || !in_array($sexe, ['homme','femme','autre'])) 
                       $errors[] = "Veuillez sélectionner un sexe valide.";
if (empty($age) || !is_numeric($age) || (int)$age < 18) 
                       $errors[] = "L'âge doit être un nombre ≥ 18 ans.";
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) 
                       $errors[] = "L'email n'est pas valide.";
if (empty($marque))    $errors[] = "La marque est obligatoire.";
if (empty($adresse))   $errors[] = "L'adresse de livraison est obligatoire.";
if (empty($date))      $errors[] = "La date de livraison est obligatoire.";
elseif (!empty($date)) {
    $dateLivraison = new DateTime($date);
    $aujourdhui    = new DateTime('today');           
    $demain        = (clone $aujourdhui)->modify('+1 day'); 

    if ($dateLivraison < $demain) {
        $errors[] = "La date de livraison doit être à partir de demain.";
    }
}

if (empty($errors)) {
    
    //  Enregistrement en base de données (décommente et configure)
    
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=commandes;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM commandes WHERE email = ?");
    $stmt->execute([$email]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $errors[] = "Cet email est déjà utilisé pour une autre commande.";
    }      
    
    if (!empty($errors)) {
        $redirect_url = "index.php?" 
                      . "errors=" . urlencode(json_encode($errors)) 
                      . "&old="   . urlencode(json_encode($old));
        header("Location: $redirect_url");
        exit;
    }
    // Génération d'un mot de passe temporaire
    $motDePasseClair = $prenom ."2@25";   

// Hashage sécurisé 
$motDePasseHashe = password_hash($motDePasseClair, PASSWORD_DEFAULT);
    $sql = "INSERT INTO commandes 
                (prenom, nom, sexe, age, email, marque, adresse, date_livraison, commentaire, created_at, password)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$prenom, $nom, $sexe, (int)$age, $email, $marque, $adresse, $date, $message, $motDePasseHashe]);

        // Redirection avec succès
        header("Location: index.php?success=1");
        exit;
    } 
    catch (PDOException $e) {
        $errors[] = "Erreur lors de l'enregistrement : " . $e->getMessage();
    }
    
}

$redirect_url = "index.php?" 
              . "errors=" . urlencode(json_encode($errors)) 
              . "&old="   . urlencode(json_encode($old));

header("Location: $redirect_url");
exit;