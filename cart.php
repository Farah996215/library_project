<?php
$page_title = "Panier";
include 'includes/header.php';

require_once 'php/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$cart_stmt = $pdo->prepare("SELECT c.*, b.title, b.author, b.price, b.cover_image, b.available_copies 
                          FROM cart c 
                          JOIN books b ON c.book_id = b.id 
                          WHERE c.user_id = ?");
$cart_stmt->execute([$user_id]);
$cart_items = $cart_stmt->fetchAll(PDO::FETCH_ASSOC);
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<section class="cart">
    <div class="container">
        <h1 class="section-title">Votre panier</h1>
        
        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <h2>Votre panier est vide</h2>
                <p>Découvrez notre catalogue et ajoutez des livres à votre panier.</p>
                <a href="books.php" class="btn">Explorer le catalogue</a>
            </div>
        <?php else: ?>
            <div class="cart-content">
                <div class="cart-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <img src="images/<?php echo $item['cover_image'] ?: 'default.jpg'; ?>" 
                                 alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                 class="cart-item-image">
                            
                            <div class="cart-item-details">
                                <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                                <p class="author"><?php echo htmlspecialchars($item['author']); ?></p>
                                <div class="availability <?php echo $item['available_copies'] > 0 ? 'available' : 'unavailable'; ?>">
                                    <?php echo $item['available_copies'] > 0 ? 'En stock' : 'Rupture de stock'; ?>
                                </div>
                            </div>
                            
                            <div class="cart-item-price">
                                <span class="price"><?php echo number_format($item['price'], 2); ?> DT</span>
                            </div>
                            
                            <div class="cart-item-quantity">
                                <form action="php/process/cart-process.php" method="POST" class="quantity-form">
                                    <input type="hidden" name="book_id" value="<?php echo $item['book_id']; ?>">
                                    <input type="hidden" name="action" value="update">
                                    <button type="submit" name="change" value="-1" <?php echo $item['quantity'] <= 1 ? 'disabled' : ''; ?>>-</button>
                                    <span class="quantity"><?php echo $item['quantity']; ?></span>
                                    <button type="submit" name="change" value="1">+</button>
                                </form>
                            </div>
                            
                            <div class="cart-item-total">
                                <span class="total"><?php echo number_format($item['price'] * $item['quantity'], 2); ?> DT</span>
                            </div>
                            
                            <div class="cart-item-actions">
                                <form action="php/process/cart-process.php" method="POST">
                                    <input type="hidden" name="book_id" value="<?php echo $item['book_id']; ?>">
                                    <input type="hidden" name="action" value="remove">
                                    <button type="submit" class="btn-remove">×</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="cart-summary">
                    <h3>Résumé de la commande</h3>
                    <div class="summary-line">
                        <span>Sous-total:</span>
                        <span><?php echo number_format($total, 2); ?> DT</span>
                    </div>
                    <div class="summary-line">
                        <span>Frais de livraison:</span>
                        <span>Gratuit</span>
                    </div>
                    <div class="summary-line total">
                        <span>Total:</span>
                        <span><?php echo number_format($total, 2); ?> DT</span>
                    </div>
                    
                    <div class="cart-actions">
                        <a href="books.php" class="btn btn-outline">Continuer les achats</a>
                        <a href="checkout.php" class="btn">Procéder au paiement</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>