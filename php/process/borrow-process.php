<?php
require_once '../config.php';
require_once '../functions.php';

if (!isLoggedIn()) {
    $_SESSION['message'] = 'Veuillez vous connecter pour emprunter un livre.';
    $_SESSION['message_type'] = 'error';
    header('Location: /bibliotheque/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $book_id = $_POST['book_id'];
    
    try {
        $book_stmt = $pdo->prepare("SELECT available_copies, title FROM books WHERE id = ?");
        $book_stmt->execute([$book_id]);
        $book = $book_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$book) {
            $_SESSION['message'] = 'Livre non trouvé.';
            $_SESSION['message_type'] = 'error';
            header('Location: /bibliotheque/books.php');
            exit();
        }
        
        if ($book['available_copies'] <= 0) {
            $_SESSION['message'] = 'Ce livre n\'est plus disponible pour l\'emprunt.';
            $_SESSION['message_type'] = 'error';
            header('Location: /bibliotheque/book-details.php?id=' . $book_id);
            exit();
        }
        $existing_borrow = $pdo->prepare("SELECT id FROM borrowed_books WHERE user_id = ? AND book_id = ? AND status = 'borrowed'");
        $existing_borrow->execute([$user_id, $book_id]);
        
        if ($existing_borrow->fetch()) {
            $_SESSION['message'] = 'Vous avez déjà emprunté ce livre.';
            $_SESSION['message_type'] = 'error';
            header('Location: /bibliotheque/book-details.php?id=' . $book_id);
            exit();
        }
        
        // Calcul des dates (emprunt pour 21 jours)
        $borrow_date = date('Y-m-d');
        $due_date = date('Y-m-d', strtotime('+21 days'));
        $borrow_stmt = $pdo->prepare("INSERT INTO borrowed_books (user_id, book_id, borrow_date, due_date, status) VALUES (?, ?, ?, ?, 'borrowed')");
        $borrow_stmt->execute([$user_id, $book_id, $borrow_date, $due_date]);
        $update_stmt = $pdo->prepare("UPDATE books SET available_copies = available_copies - 1 WHERE id = ?");
        $update_stmt->execute([$book_id]);
        
        $_SESSION['message'] = 'Livre emprunté avec succès ! Date de retour: ' . date('d/m/Y', strtotime($due_date));
        $_SESSION['message_type'] = 'success';
        header('Location: /bibliotheque/dashboard.php?section=borrowed');
        exit();
        
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Erreur lors de l\'emprunt: ' . $e->getMessage();
        $_SESSION['message_type'] = 'error';
        header('Location: /bibliotheque/book-details.php?id=' . $book_id);
        exit();
    }
} else {
    header('Location: /bibliotheque/books.php');
    exit();
}
?>
