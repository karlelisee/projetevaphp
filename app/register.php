<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validation des champs
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Tous les champs sont obligatoires.";
    } else {
        // // Connexion à la base de données
        // $pdo = new PDO('mysql:host=localhost;dbname=file_manager', 'root', '');
        
        // Vérification si l'utilisateur existe déjà
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $error = "Un compte avec cet email existe déjà.";
        } else {
            // Cryptage du mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insérer l'utilisateur dans la base de données
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword]);

            $success = "Inscription réussie. Vous pouvez vous connecter.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <title>Inscription</title>
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
            <h2 class="text-center mb-4">Inscription</h2>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger text-center">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php elseif (isset($success)): ?>
                <div class="alert alert-success text-center">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Nom d'utilisateur :</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email :</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe :</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
            </form>

            <p class="text-center mt-3">
                Déjà un compte ? <a href="login.php" class="text-decoration-none">Connectez-vous ici</a>
            </p>
        </div>
    </div>
</body>
</html>
