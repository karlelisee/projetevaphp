<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Déconnexion
if (isset($_GET['logout'])) {
    session_destroy(); // Détruire la session
    header("Location: login.php"); // Redirection vers la page de connexion
    exit;
}
require 'db.php';

$stmt = $pdo->query("SELECT * FROM files WHERE deleted = 1");
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corbeille</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-primary">Corbeille</h1>
            <a href="index.php" class="btn btn-secondary">Retour à l'accueil</a>
        </div>

        <?php if (count($files) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Nom du fichier</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($files as $file): ?>
                            <tr>
                                <td><?= htmlspecialchars($file['file_name']); ?></td>
                                <td>
                                    <a href="restore.php?id=<?= $file['id']; ?>" class="btn btn-success btn-sm me-2">Restaurer</a>
                                    <a href="permanent_delete.php?id=<?= $file['id']; ?>" class="btn btn-danger btn-sm">Supprimer définitivement</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                Aucun fichier dans la corbeille.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
