<?php
$page_title = "Accueil";
include 'includes/header.php';
?>
<section class="hero">
    <div class="container">
        <div class="hero-content fade-in">
            <h1>Bienvenue dans votre bibliothèque moderne</h1>
            <p>Découvrez des milliers de livres, empruntez ou achetez en toute simplicité</p>
            <div class="hero-actions">
                <a href="books.php" class="btn">Explorer le catalogue</a>
                <a href="about.php" class="btn btn-secondary">En savoir plus</a>
            </div>
        </div>
    </div>
</section>
<section class="featured-books">
    <div class="container">
        <h2 class="section-title">Livres en vedette</h2>
        <div class="books-grid">
            <?php
            $popular_books = getPopularBooks(6);
            foreach ($popular_books as $book):
            ?>
            <div class="book-card fade-in">
                <img src="images/<?php echo $book['cover_image'] ?: 'default.jpg'; ?>" 
                     alt="<?php echo htmlspecialchars($book['title']); ?>" 
                     class="book-cover">
                <div class="book-info">
                    <h3 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p class="book-author"><?php echo htmlspecialchars($book['author']); ?></p>
                    <div class="book-price"><?php echo number_format($book['price'], 2); ?> DT</div>
                    <a href="book-details.php?id=<?php echo $book['id']; ?>" class="btn">Voir détails</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<section class="stats">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card fade-in">
                <div class="stat-number">1000+</div>
                <div class="stat-label">Livres disponibles</div>
            </div>
            <div class="stat-card fade-in">
                <div class="stat-number">2,000+</div>
                <div class="stat-label">Membres actifs</div>
            </div>
            <div class="stat-card fade-in">
                <div class="stat-number">5,000+</div>
                <div class="stat-label">Emprunts annuels</div>
            </div>
            <div class="stat-card fade-in">
                <div class="stat-number">99%</div>
                <div class="stat-label">Satisfaction des membres</div>
            </div>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
