<?php
require_once '../config.php';
require_once '../functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $address = sanitizeInput($_POST['address'] ?? '');

    if ($password !== $confirm_password) {
        $_SESSION['message'] = 'Les mots de passe ne correspondent pas.';
        $_SESSION['message_type'] = 'error';
        header('Location: /bibliotheque/register.php');
        exit();
    }
    
    if (strlen($password) < 6) {
        $_SESSION['message'] = 'Le mot de passe doit contenir au moins 6 caractères.';
        $_SESSION['message_type'] = 'error';
        header('Location: /bibliotheque/register.php');
        exit();
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $_SESSION['message'] = 'Cet email est déjà utilisé.';
            $_SESSION['message_type'] = 'error';
            header('Location: /bibliotheque/register.php');
            exit();
        }
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->fetch()) {
            $_SESSION['message'] = 'Ce nom d\'utilisateur est déjà pris.';
            $_SESSION['message_type'] = 'error';
            header('Location: /bibliotheque/register.php');
            exit();
        }
    
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, phone, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password, $phone, $address]);
        
        $_SESSION['message'] = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
        $_SESSION['message_type'] = 'success';
        
        header('Location: /bibliotheque/login.php');
        exit();
        
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Erreur lors de l\'inscription: ' . $e->getMessage();
        $_SESSION['message_type'] = 'error';
        header('Location: /bibliotheque/register.php');
        exit();
    }
} else {
    header('Location: /bibliotheque/register.php');
    exit();
}
?>