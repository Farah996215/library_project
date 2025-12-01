<?php
$page_title = "Connexion";
include 'includes/header.php';
?>
<div class="auth-container">
    <div class="auth-form fade-in">
        <h2>Connexion</h2>
        <form action="php/process/login-process.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn" style="width: 100%;">Se connecter</button>
        </form>
        <p style="text-align: center; margin-top: 24px;">
            Pas encore de compte ? <a href="register.php">S'inscrire</a>
        </p>
    </div>
</div>
<?php include 'includes/footer.php'; ?>