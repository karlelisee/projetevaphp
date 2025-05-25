<?php
session_start();
ob_start(); // en haut

require_once 'db.php';
$db = $pdo->query("SELECT DATABASE()")->fetchColumn();
echo "Base de données utilisée : " . $db;

$stmt = $pdo->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
print_r($tables);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $email = $_POST['email'];
    $password = $_POST['password'];

    // // Connexion à la base de données
    // $pdo = new PDO('mysql:host=localhost;dbname=file_manager', 'root', '');
    
    // Vérifier si l'utilisateur existe
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Connexion réussie
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");  // Rediriger vers le tableau de bord
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}
exit;
ob_end_flush(); // tout à la fin
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <title>Connexion</title>
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
            <h2 class="text-center mb-4">Connexion</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger text-center">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email :</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe :</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Se connecter</button>
            </form>

            <p class="text-center mt-3">
                Pas encore inscrit ? <a href="register.php" class="text-decoration-none">Inscrivez-vous ici</a>
            </p>
        </div>
    </div>
</body>
</html>
