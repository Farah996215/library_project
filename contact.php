<?php
$page_title = "Contact";
include 'includes/header.php';

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (empty($name)) {
        $errors[] = "Le nom est obligatoire";
    }
    
    if (empty($email)) {
        $errors[] = "L'email est obligatoire";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Veuillez entrer une adresse email valide";
    }
    
    if (empty($subject)) {
        $errors[] = "Le sujet est obligatoire";
    }
    
    if (empty($message)) {
        $errors[] = "Le message est obligatoire";
    } elseif (strlen($message) < 10) {
        $errors[] = "Le message doit contenir au moins 10 caractères";
    }

    if (empty($errors)) {
        $success = true;
    }
}
?>

<section class="contact">
    <div class="container">
        <div class="contact-hero">
            <h1>Contactez-nous</h1>
            <p>Nous sommes là pour répondre à vos questions et recevoir vos suggestions.</p>
        </div>

        <div class="contact-content">
            <div class="contact-info">
                <h2>Nos coordonnées</h2>
                
                <div class="contact-method">
                    <h3>📧 Email</h3>
                    <p>contact@bibliotheque.com</p>
                    <p>Nous répondons sous 24 heures</p>
                </div>
                
                <div class="contact-method">
                    <h3>📞 Téléphone</h3>
                    <p>+216 21 23 17 65</p>
                    <p>Lun-Ven: 9h-18h</p>
                </div>
                
                <div class="contact-method">
                    <h3>📍 Adresse</h3>
                    <p>
                        Mornag<br>
                        2090 Tunisie,Ben Arous
                    </p>
                </div>
                
                <div class="contact-method">
                    <h3>🕒 Horaires d'ouverture</h3>
                    <p>
                        Lundi - Vendredi: 9h00 - 19h00<br>
                        Samedi: 10h00 - 15h00<br>
                        Dimanche: Fermé
                    </p>
                </div>
            </div>

            <div class="contact-form-container">
                <h2>Envoyez-nous un message</h2>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <h3>✅ Message envoyé avec succès !</h3>
                        <p>Merci de nous avoir contactés. Nous avons bien reçu votre message et nous vous répondrons dans les plus brefs délais.</p>
                        <div class="success-actions">
                            <a href="contact.php" class="btn">Envoyer un autre message</a>
                            <a href="index.php" class="btn btn-secondary">Retour à l'accueil</a>
                        </div>
                    </div>
                <?php else: ?>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-error">
                            <h3>❌ Veuillez corriger les erreurs suivantes :</h3>
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="contact-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Votre nom *</label>
                                <input type="text" id="name" name="name" class="form-control" 
                                       value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Votre email *</label>
                                <input type="email" id="email" name="email" class="form-control" 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Sujet *</label>
                            <select id="subject" name="subject" class="form-control" required>
                                <option value="">Choisir un sujet</option>
                                <option value="question" <?php echo ($subject ?? '') == 'question' ? 'selected' : ''; ?>>Question générale</option>
                                <option value="technical" <?php echo ($subject ?? '') == 'technical' ? 'selected' : ''; ?>>Problème technique</option>
                                <option value="suggestion" <?php echo ($subject ?? '') == 'suggestion' ? 'selected' : ''; ?>>Suggestion</option>
                                <option value="partnership" <?php echo ($subject ?? '') == 'partnership' ? 'selected' : ''; ?>>Partenariat</option>
                                <option value="other" <?php echo ($subject ?? '') == 'other' ? 'selected' : ''; ?>>Autre</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Votre message *</label>
                            <textarea id="message" name="message" class="form-control" rows="6" required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn">Envoyer le message</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="map-section">
            <h2>Nous trouver</h2>
            <div class="map-placeholder">
                <div class="map-content">
                    <h3>Bibliothèque Moderne</h3>
                    <p>Mornag, 2090 Tunisie,Ben Arous</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.alert {
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.alert-success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
    text-align: center;
}

.alert-error {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.success-actions {
    margin-top: 20px;
}

.btn-secondary {
    background: #6c757d;
    margin-left: 10px;
}

.btn-secondary:hover {
    background: #545b62;
}

.alert h3 {
    margin-top: 0;
    margin-bottom: 10px;
}

.alert ul {
    margin-bottom: 0;
    padding-left: 20px;
}
</style>

<?php include 'includes/footer.php'; ?>