<?php
require_once '../config.php';
require_once '../functions.php';

if (!isLoggedIn()) {
    header('Location: /bibliotheque/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $borrow_id = $_POST['borrow_id'];
    
    try {
        $borrow_stmt = $pdo->prepare("SELECT * FROM borrowed_books WHERE id = ? AND user_id = ?");
        $borrow_stmt->execute([$borrow_id, $user_id]);
        $borrow = $borrow_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$borrow) {
            $_SESSION['message'] = 'Emprunt non trouvé.';
            $_SESSION['message_type'] = 'error';
            header('Location: /bibliotheque/dashboard.php');
            exit();
        }
        $penalty = 0;
        $return_date = date('Y-m-d');
        $due_date = $borrow['due_date'];
        
        if (strtotime($return_date) > strtotime($due_date)) {
            $days_late = floor((strtotime($return_date) - strtotime($due_date)) / (60 * 60 * 24));
            $penalty = $days_late * 0.50;
        }
        $return_stmt = $pdo->prepare("UPDATE borrowed_books SET return_date = ?, status = 'returned', penalty = ? WHERE id = ?");
        $return_stmt->execute([$return_date, $penalty, $borrow_id]);
        $update_stmt = $pdo->prepare("UPDATE books SET available_copies = available_copies + 1 WHERE id = ?");
        $update_stmt->execute([$borrow['book_id']]);
        
        $message = 'Livre retourné avec succès.';
        if ($penalty > 0) {
            $message .= ' Pénalité de retard: ' . number_format($penalty, 2) . ' DT';
        }
        
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = 'success';
        header('Location: /bibliotheque/dashboard.php?section=borrowed');
        exit();
        
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Erreur lors du retour: ' . $e->getMessage();
        $_SESSION['message_type'] = 'error';
        header('Location: /bibliotheque/dashboard.php');
        exit();
    }
} else {
    header('Location: /bibliotheque/dashboard.php');
    exit();
}
?>