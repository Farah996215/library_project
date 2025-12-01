<?php
require_once 'config.php';
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
function redirectWithMessage($url, $type, $message) {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
    header("Location: $url");
    exit();
}
function displayMessage() {
    if (isset($_SESSION['message'])) {
        $type = $_SESSION['message_type'];
        $message = $_SESSION['message'];

        echo "<div class='message $type'>$message</div>";

        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
}
function calculatePenalty($due_date) {
    $today = new DateTime();
    $due = new DateTime($due_date);

    if ($today > $due) {
        
        $interval = $today->diff($due);
        $days_overdue = $interval->days;
        return $days_overdue * PENALTY_PER_DAY;
    }

    return 0;
}
function getPopularBooks($limit = 5) {
    global $pdo;

    $sql = "SELECT b.*, COUNT(bb.id) as borrow_count 
            FROM books b 
            LEFT JOIN borrowed_books bb ON b.id = bb.book_id 
            GROUP BY b.id 
            ORDER BY borrow_count DESC 
            LIMIT :limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>