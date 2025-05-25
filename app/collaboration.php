<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=file_manager', 'root', ''); // Remplacez par vos paramètres
$fileId = $_GET['file_id'];
$userId = $_SESSION['user_id'];

// Vérifiez que l'utilisateur est propriétaire du fichier
$stmt = $pdo->prepare("SELECT * FROM files WHERE id = ? AND owner_id = ?");
$stmt->execute([$fileId, $userId]);
$file = $stmt->fetch();

if (!$file) {
    echo "<p style='color:red;'>Vous n'êtes pas autorisé à gérer les collaborations pour ce fichier.</p>";
    exit;
}

// Ajout de collaborateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['collaborator_email'], $_POST['permission'])) {
    $email = $_POST['collaborator_email'];
    $permission = $_POST['permission'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $collaborator = $stmt->fetch();

    if ($collaborator) {
        $collaboratorId = $collaborator['id'];
        $stmt = $pdo->prepare("INSERT INTO collaborators (file_id, owner_id, collaborator_id, permission) VALUES (?, ?, ?, ?)");
        $stmt->execute([$fileId, $userId, $collaboratorId, $permission]);
        echo "<p style='color:green;'>Collaborateur ajouté avec succès.</p>";
    } else {
        echo "<p style='color:red;'>Utilisateur introuvable.</p>";
    }
}

// Récupérer les collaborateurs existants
$stmt = $pdo->prepare("SELECT c.id, u.email, c.permission FROM collaborators c JOIN users u ON c.collaborator_id = u.id WHERE c.file_id = ?");
$stmt->execute([$fileId]);
$collaborators = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Gérer les collaborations</title>
</head>
<body>
<div class="container mt-4">
    <h2>Gérer les collaborations</h2>
    <form action="" method="POST" class="mb-4">
        <div class="mb-3">
            <label for="collaborator_email" class="form-label">Email du collaborateur :</label>
            <input type="email" name="collaborator_email" id="collaborator_email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="permission" class="form-label">Permission :</label>
            <select name="permission" id="permission" class="form-select">
                <option value="read">Lecture seule</option>
                <option value="write">Lecture et écriture</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Ajouter un collaborateur</button>
    </form>

    <h4>Collaborateurs existants :</h4>
    <ul class="list-group">
        <?php foreach ($collaborators as $collaborator): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <?php echo htmlspecialchars($collaborator['email']); ?> (<?php echo htmlspecialchars($collaborator['permission']); ?>)
                <a href="collaboration.php?remove=<?php echo $collaborator['id']; ?>&file_id=<?php echo $fileId; ?>" class="btn btn-danger btn-sm">Retirer</a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>
