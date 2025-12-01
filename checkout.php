<?php
$page_title = "Paiement";
include 'includes/header.php';

require_once 'php/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$cart_stmt = $pdo->prepare("SELECT c.*, b.title, b.author, b.price, b.cover_image 
                          FROM cart c 
                          JOIN books b ON c.book_id = b.id 
                          WHERE c.user_id = ?");
$cart_stmt->execute([$user_id]);
$cart_items = $cart_stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart_items)) {
    header('Location: cart.php');
    exit();
}
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
$user_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);
?>

<section class="checkout">
    <div class="container">
        <h1 class="section-title">Finaliser votre commande</h1>
        
        <div class="checkout-content">
            <div class="checkout-form">
                <form action="php/process/purchase-process.php" method="POST" id="checkout-form">
                    <div class="form-section">
                        <h3>Informations de livraison</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="full_name">Nom complet *</label>
                                <input type="text" id="full_name" name="full_name" class="form-control" 
                                       value="<?php echo htmlspecialchars($user['username']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" class="form-control" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Téléphone *</label>
                                <input type="tel" id="phone" name="phone" class="form-control" 
                                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Adresse de livraison *</label>
                            <textarea id="address" name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Méthode de paiement</h3>
                        
                        <div class="payment-methods">
                            <div class="payment-method">
                                <input type="radio" id="card" name="payment_method" value="card" checked>
                                <label for="card">Carte de crédit</label>
                            </div>
                            
                            <div class="payment-method">
                                <input type="radio" id="paypal" name="payment_method" value="paypal">
                                <label for="paypal">PayPal</label>
                            </div>
                        </div>

                        <div class="card-form" id="card-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="card_number">Numéro de carte</label>
                                    <input type="text" id="card_number" name="card_number" class="form-control" 
                                           placeholder="1234 5678 9012 3456" maxlength="19">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="expiry_date">Date d'expiration</label>
                                    <input type="text" id="expiry_date" name="expiry_date" class="form-control" 
                                           placeholder="MM/AA" maxlength="5">
                                </div>
                                
                                <div class="form-group">
                                    <label for="cvv">CVV</label>
                                    <input type="text" id="cvv" name="cvv" class="form-control" 
                                           placeholder="123" maxlength="3">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="card_holder">Nom du titulaire</label>
                                <input type="text" id="card_holder" name="card_holder" class="form-control">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-pay" style="width: 100%;">
                        Payer <?php echo number_format($total, 2); ?> DT
                    </button>
                </form>
            </div>

            <div class="order-summary">
                <h3>Résumé de la commande</h3>
                
                <div class="order-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="order-item">
                            <img src="images/<?php echo $item['cover_image'] ?: 'default.jpg'; ?>" 
                                 alt="<?php echo htmlspecialchars($item['title']); ?>">
                            <div class="item-details">
                                <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                                <p><?php echo htmlspecialchars($item['author']); ?></p>
                                <div class="item-quantity">Quantité: <?php echo $item['quantity']; ?></div>
                            </div>
                            <div class="item-price">
                                <?php echo number_format($item['price'] * $item['quantity'], 2); ?> DT
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="order-totals">
                    <div class="total-line">
                        <span>Sous-total:</span>
                        <span><?php echo number_format($total, 2); ?> DT</span>
                    </div>
                    <div class="total-line">
                        <span>Livraison:</span>
                        <span>Gratuit</span>
                    </div>
                    <div class="total-line grand-total">
                        <span>Total:</span>
                        <span><?php echo number_format($total, 2); ?> DT</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const cardForm = document.getElementById('card-form');
    
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            if (this.value === 'card') {
                cardForm.style.display = 'block';
            } else {
                cardForm.style.display = 'none';
            }
        });
    });
    const cardNumber = document.getElementById('card_number');
    if (cardNumber) {
        cardNumber.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ');
            e.target.value = formattedValue || value;
        });
    }
    const expiryDate = document.getElementById('expiry_date');
    if (expiryDate) {
        expiryDate.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\//g, '').replace(/[^0-9]/gi, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>