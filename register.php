<?php
$page_title = "Inscription";
include 'includes/header.php';
?>
<div class="auth-container">
    <div class="auth-form fade-in">
        <h2>Créer un compte</h2>
        <form action="php/process/register-process.php" method="POST">
            <div class="form-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" required minlength="6">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="phone">Téléphone (optionnel)</label>
                <input type="tel" id="phone" name="phone" class="form-control">
            </div>
            <div class="form-group">
                <label for="address">Adresse (optionnel)</label>
                <textarea id="address" name="address" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn" style="width: 100%;">S'inscrire</button>
        </form>
        <p style="text-align: center; margin-top: 24px;">
            Déjà un compte ? <a href="login.php">Se connecter</a>
        </p>
    </div>
</div>
<?php include 'includes/footer.php'; ?>