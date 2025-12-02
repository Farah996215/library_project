<?php
require_once '../config.php';
require_once '../functions.php';

if (!isLoggedIn()) {
    header('Location: /bibliotheque/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $book_id = $_POST['book_id'];
    $action = $_POST['action'] ?? 'add';
    
    try {
        if ($action === 'add') {
            $existing = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND book_id = ?");
            $existing->execute([$user_id, $book_id]);
            
            if (!$existing->fetch()) {
                $insert = $pdo->prepare("INSERT INTO wishlist (user_id, book_id) VALUES (?, ?)");
                $insert->execute([$user_id, $book_id]);
                $_SESSION['message'] = 'Livre ajouté à votre liste de souhaits.';
            } else {
                $_SESSION['message'] = 'Ce livre est déjà dans votre liste de souhaits.';
            }
        } elseif ($action === 'remove') {
            $delete = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND book_id = ?");
            $delete->execute([$user_id, $book_id]);
            $_SESSION['message'] = 'Livre retiré de votre liste de souhaits.';
        }
        
        $_SESSION['message_type'] = 'success';
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/bibliotheque/dashboard.php'));
        exit();
        
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Erreur: ' . $e->getMessage();
        $_SESSION['message_type'] = 'error';
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/bibliotheque/dashboard.php'));
        exit();
    }
} else {
    header('Location: /bibliotheque/books.php');
    exit();
}
?>