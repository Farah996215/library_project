<?php
require_once '../config.php';
require_once '../functions.php';

error_log("=== DÉBUT LOGIN PROCESS ===");
error_log("POST data: " . print_r($_POST, true));
error_log("Session ID: " . session_id());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    
    error_log("Tentative de connexion pour: " . $email);
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            
            error_log("Connexion réussie pour: " . $email);
            error_log("Redirection vers dashboard.php");
        
            $redirect_url = "http://" . $_SERVER['HTTP_HOST'] . "/bibliotheque/dashboard.php";
            error_log("URL de redirection: " . $redirect_url);
            
            header('Location: ' . $redirect_url);
            exit();
            
        } else {
            error_log("Échec connexion - mot de passe incorrect");
            $_SESSION['message'] = 'Email ou mot de passe incorrect.';
            $_SESSION['message_type'] = 'error';
            header('Location: /bibliotheque/login.php');
            exit();
        }
    } catch (PDOException $e) {
        error_log("Erreur DB: " . $e->getMessage());
        $_SESSION['message'] = 'Erreur de connexion: ' . $e->getMessage();
        $_SESSION['message_type'] = 'error';
        header('Location: /bibliotheque/login.php');
        exit();
    }
} else {
    error_log("Méthode non POST");
    header('Location: /bibliotheque/login.php');
    exit();
}
?>