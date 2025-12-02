<?php
require_once '../config.php';
require_once '../functions.php';

if (!isLoggedIn()) {
    $_SESSION['message'] = 'Veuillez vous connecter pour effectuer un achat.';
    $_SESSION['message_type'] = 'error';
    header('Location: /bibliotheque/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    
    try {
        $pdo->beginTransaction();
        $cart_stmt = $pdo->prepare("SELECT c.*, b.title, b.price, b.available_copies 
                                  FROM cart c 
                                  JOIN books b ON c.book_id = b.id 
                                  WHERE c.user_id = ?");
        $cart_stmt->execute([$user_id]);
        $cart_items = $cart_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($cart_items)) {
            throw new Exception('Votre panier est vide.');
        }
        foreach ($cart_items as $item) {
            if ($item['available_copies'] < $item['quantity']) {
                throw new Exception('Le livre "' . $item['title'] . '" n\'est plus disponible en quantité suffisante.');
            }
        }
        $full_name = sanitizeInput($_POST['full_name']);
        $email = sanitizeInput($_POST['email']);
        $phone = sanitizeInput($_POST['phone']);
        $address = sanitizeInput($_POST['address']);
        $payment_method = sanitizeInput($_POST['payment_method']);
        $total_amount = 0;
        foreach ($cart_items as $item) {
            $total_amount += $item['price'] * $item['quantity'];
        }
        foreach ($cart_items as $item) {
            $item_total = $item['price'] * $item['quantity'];
            $purchase_stmt = $pdo->prepare("INSERT INTO purchases (user_id, book_id, quantity, unit_price, total_amount, status) VALUES (?, ?, ?, ?, ?, 'completed')");
            $purchase_stmt->execute([
                $user_id, 
                $item['book_id'], 
                $item['quantity'], 
                $item['price'], 
                $item_total
            ]);
            $update_stmt = $pdo->prepare("UPDATE books SET available_copies = available_copies - ? WHERE id = ?");
            $update_stmt->execute([$item['quantity'], $item['book_id']]);
        }
        $clear_cart = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $clear_cart->execute([$user_id]);
        $pdo->commit();
        if (!empty($phone) || !empty($address)) {
            $update_user = $pdo->prepare("UPDATE users SET phone = ?, address = ? WHERE id = ?");
            $update_user->execute([$phone, $address, $user_id]);
        }
        
        $_SESSION['message'] = 'Achat effectué avec succès ! Merci pour votre commande.';
        $_SESSION['message_type'] = 'success';
        header('Location: /bibliotheque/dashboard.php?section=purchases');
        exit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        
        $_SESSION['message'] = 'Erreur lors de l\'achat: ' . $e->getMessage();
        $_SESSION['message_type'] = 'error';
        header('Location: /bibliotheque/checkout.php');
        exit();
    }
} else {
    header('Location: /bibliotheque/cart.php');
    exit();
}
?>