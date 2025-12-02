<?php
require_once '../config.php';
require_once '../functions.php';

if (!isLoggedIn()) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Veuillez vous connecter.']);
    } else {
        header('Location: /bibliotheque/login.php');
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $book_id = $_POST['book_id'];
    $action = $_POST['action'] ?? 'add';
    
    try {
        switch ($action) {
            case 'add':
                $existing = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND book_id = ?");
                $existing->execute([$user_id, $book_id]);
                $item = $existing->fetch(PDO::FETCH_ASSOC);
                
                if ($item) {

                    $update = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
                    $update->execute([$item['id']]);
                } else {

                    $insert = $pdo->prepare("INSERT INTO cart (user_id, book_id, quantity) VALUES (?, ?, 1)");
                    $insert->execute([$user_id, $book_id]);
                }
                break;
                
            case 'update':
                $change = $_POST['change'] ?? 0;
                $current = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND book_id = ?");
                $current->execute([$user_id, $book_id]);
                $current_item = $current->fetch(PDO::FETCH_ASSOC);
                
                $new_quantity = $current_item['quantity'] + intval($change);
                
                if ($new_quantity <= 0) {
                    $delete = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND book_id = ?");
                    $delete->execute([$user_id, $book_id]);
                } else {
                    $update = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND book_id = ?");
                    $update->execute([$new_quantity, $user_id, $book_id]);
                }
                break;
                
            case 'remove':
                $delete = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND book_id = ?");
                $delete->execute([$user_id, $book_id]);
                break;
        }
        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            header('Location: /bibliotheque/cart.php');
        }
        exit();
        
    } catch (PDOException $e) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        } else {
            $_SESSION['message'] = 'Erreur: ' . $e->getMessage();
            $_SESSION['message_type'] = 'error';
            header('Location: /bibliotheque/cart.php');
        }
        exit();
    }
} else {
    header('Location: /bibliotheque/cart.php');
    exit();
}
?>