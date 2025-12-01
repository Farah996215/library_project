<?php
$page_title = "Détails du livre";
include 'includes/header.php';

require_once 'php/functions.php';

if (!isset($_GET['id'])) {
    header('Location: books.php');
    exit();
}
$book_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT b.*, c.name as category_name FROM books b 
                      LEFT JOIN categories c ON b.category_id = c.id 
                      WHERE b.id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    header('Location: books.php');
    exit();
}
$reviews_stmt = $pdo->prepare("SELECT r.*, u.username FROM reviews r 
                              JOIN users u ON r.user_id = u.id 
                              WHERE r.book_id = ? 
                              ORDER BY r.created_at DESC");
$reviews_stmt->execute([$book_id]);
$reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);
$avg_rating_stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as review_count 
                                 FROM reviews WHERE book_id = ?");
$avg_rating_stmt->execute([$book_id]);
$rating_info = $avg_rating_stmt->fetch(PDO::FETCH_ASSOC);
$user_has_interaction = false;
if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    $interaction_stmt = $pdo->prepare("SELECT id FROM borrowed_books 
                                     WHERE user_id = ? AND book_id = ? 
                                     UNION 
                                     SELECT id FROM purchases 
                                     WHERE user_id = ? AND book_id = ?");
    $interaction_stmt->execute([$user_id, $book_id, $user_id, $book_id]);
    $user_has_interaction = $interaction_stmt->fetch() !== false;
}
?>

<section class="book-details">
    <div class="container">
        <div class="breadcrumb">
            <a href="index.php">Accueil</a> &gt;
            <a href="books.php">Catalogue</a> &gt;
            <span><?php echo htmlspecialchars($book['title']); ?></span>
        </div>
        <div class="book-details-content">
            <div class="book-image">
                <img src="images/<?php echo $book['cover_image'] ?: 'default.jpg'; ?>" 
                     alt="<?php echo htmlspecialchars($book['title']); ?>" 
                     class="book-details-image">
            </div>
            <div class="book-info-details">
                <h1><?php echo htmlspecialchars($book['title']); ?></h1>
                <p class="book-author">par <?php echo htmlspecialchars($book['author']); ?></p>               
                <div class="book-meta">
                    <span class="category"><?php echo htmlspecialchars($book['category_name']); ?></span>
                    <span class="year">Publié en <?php echo $book['published_year']; ?></span>
                    <span class="isbn">ISBN: <?php echo $book['isbn']; ?></span>
                </div>
                <div class="rating-section">
                    <div class="average-rating">
                        <div class="stars">
                            <?php
                            $avg_rating = $rating_info['avg_rating'] ?? 0;
                            for ($i = 1; $i <= 5; $i++):
                                $class = $i <= round($avg_rating) ? 'star-filled' : 'star-empty';
                            ?>
                                <span class="star <?php echo $class; ?>">★</span>
                            <?php endfor; ?>
                        </div>
                        <span class="rating-text">
                            <?php printf("%.1f", $avg_rating); ?> (<?php echo $rating_info['review_count'] ?? 0; ?> avis)
                        </span>
                    </div>
                </div>
                <div class="price-section">
                    <span class="price"><?php echo number_format($book['price'], 2); ?> DT</span>
                    <div class="availability <?php echo $book['available_copies'] > 0 ? 'available' : 'unavailable'; ?>">
                        <?php
                        if ($book['available_copies'] > 0) {
                            echo $book['available_copies'] . ' exemplaire(s) disponible(s)';
                        } else {
                            echo 'Indisponible';
                        }
                        ?>
                    </div>
                </div>
                <div class="book-description">
                    <h3>Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
                </div>
        <div class="action-buttons">
    <?php if (isLoggedIn()): ?>
        <?php if ($book['available_copies'] > 0): ?>
            <form action="php/process/borrow-process.php" method="POST" style="display: inline;">
                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                <button type="submit" class="btn">Emprunter</button>
            </form>
        <?php endif; ?>
                        
                        <form action="php/process/cart-process.php" method="POST" style="display: inline;">
                            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                            <input type="hidden" name="action" value="add">
                            <button type="submit" class="btn btn-secondary">Ajouter au panier</button>
                        </form>
                        
                        <form action="php/process/wishlist-process.php" method="POST" style="display: inline;">
                            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                            <button type="submit" class="btn btn-outline">Ajouter à la liste de souhaits</button>
                        </form>
                    <?php else: ?>
                        <p>Veuillez vous <a href="login.php">connecter</a> pour emprunter ou acheter ce livre.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="reviews-section">
            <h2>Avis des lecteurs</h2>
        
            <?php if (isLoggedIn() && $user_has_interaction): ?>
                <div class="add-review">
                    <h3>Donner votre avis</h3>
                    <form action="php/process/review-process.php" method="POST">
                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                        <div class="form-group">
                            <label>Note:</label>
                            <div class="rating-input">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>">
                                    <label for="star<?php echo $i; ?>">★</label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="comment">Commentaire:</label>
                            <textarea id="comment" name="comment" class="form-control" rows="4"></textarea>
                        </div>
                        <button type="submit" class="btn">Publier l'avis</button>
                    </form>
                </div>
            <?php endif; ?>

            <div class="reviews-list">
                <?php if (empty($reviews)): ?>
                    <p class="no-reviews">Aucun avis pour le moment.</p>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <span class="review-author"><?php echo htmlspecialchars($review['username']); ?></span>
                                <div class="review-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="star <?php echo $i <= $review['rating'] ? 'star-filled' : 'star-empty'; ?>">★</span>
                                    <?php endfor; ?>
                                </div>
                                <span class="review-date"><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></span>
                            </div>
                            <?php if (!empty($review['comment'])): ?>
                                <div class="review-comment">
                                    <p><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
