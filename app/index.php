<?php
// index.php
// Gestionnaire de fichiers avec inclusion de la connexion DB
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Inclusion de la connexion à la BDD
require_once 'db.php'; // db.php définit et instancie $pdo

// Redirection si non connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// --- Upload de fichiers ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $fileError   = $_FILES['file']['error'];
    $originalName= $_FILES['file']['name'];
    $fileName    = !empty($_POST['new_name'])
        ? $_POST['new_name'] . '.' . pathinfo($originalName, PATHINFO_EXTENSION)
        : basename($originalName);

    $fileTmpPath = $_FILES['file']['tmp_name'];
    $fileExt     = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $uploadDir   = 'uploads/';
    $destination = $uploadDir . $fileName;
    $allowedExts = ['jpg','jpeg','png','pdf','csv'];

    if ($fileError === UPLOAD_ERR_OK) {
        if (in_array($fileExt, $allowedExts)) {
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            // Vérifier existence
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM files WHERE file_name = ?");
            $stmt->execute([$fileName]);
            if ($stmt->fetchColumn() > 0) {
                $message = ['type'=>'danger','text'=>'Le fichier existe déjà.'];
            } else {
                if (move_uploaded_file($fileTmpPath, $destination)) {
                    $stmt = $pdo->prepare(
                        "INSERT INTO files (file_name, file_path, deleted, is_favorite, owner_id) VALUES (?, ?, 0, 0, ?)"
                    );
                    $stmt->execute([$fileName, $destination, $_SESSION['user_id']]);
                    $message = ['type'=>'success','text'=>'Fichier téléchargé avec succès !'];
                } else {
                    $message = ['type'=>'danger','text'=>'Erreur lors du déplacement du fichier.'];
                }
            }
        } else {
            $message = ['type'=>'danger','text'=>'Extension non autorisée.'];
        }
    } else {
        $message = ['type'=>'danger','text'=>"Erreur upload : $fileError"];    
    }
}

// --- Recherche ou sélection de tous ---
$searchTerm = '';
if (!empty($_POST['search'])) {
    $searchTerm = $_POST['search'];
    $stmt = $pdo->prepare("SELECT * FROM files WHERE owner_id = ? AND file_name LIKE ? AND deleted = 0");
    $stmt->execute([$_SESSION['user_id'], "%$searchTerm%"]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM files WHERE owner_id = ? AND deleted = 0");
    $stmt->execute([$_SESSION['user_id']]);
}
$files = $stmt->fetchAll();

// --- Actions (corbeille, favoris, suppression) ---
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("UPDATE files SET deleted=1 WHERE id=? AND owner_id=?")
        ->execute([$id, $_SESSION['user_id']]);
    header("Location: trash.php"); exit;
}
if (isset($_GET['favorite'])) {
    $id = (int)$_GET['favorite'];
    $stmt = $pdo->prepare("SELECT is_favorite FROM files WHERE id=? AND owner_id=?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $fav = $stmt->fetchColumn();
    $pdo->prepare("UPDATE files SET is_favorite=? WHERE id=? AND owner_id=?")
        ->execute([$fav?0:1, $id, $_SESSION['user_id']]);
    header("Location: index.php"); exit;
}
if (isset($_GET['delete_permanently'])) {
    $id = (int)$_GET['delete_permanently'];
    $stmt = $pdo->prepare("SELECT file_path FROM files WHERE id=? AND owner_id=?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $path = $stmt->fetchColumn();
    if ($path && file_exists($path)) unlink($path);
    $pdo->prepare("DELETE FROM files WHERE id=? AND owner_id=?")->execute([$id, $_SESSION['user_id']]);
    header("Location: trash.php"); exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Gestionnaire de fichiers</title>
    <style>
        /* Styles d'aperçu et liste */
        #file-preview { width:150px; height:150px; border:1px solid #ccc; display:flex; align-items:center; justify-content:center; overflow:hidden; background:#f8f9fa; margin-top:10px; }
        #file-preview img, .file-thumb img { max-width:100%; max-height:100%; }
        .file-thumb { width:50px; height:50px; border:1px solid #ccc; display:flex; align-items:center; justify-content:center; background:#f8f9fa; margin-right:10px; }
        .file-item { display:flex; align-items:center; margin-bottom:10px; }
    </style>
</head>
<body class="bg-light">
<div class="container mt-4">
    <h2 class="text-center mb-4">Gestionnaire de fichiers</h2>
    <?php if (isset($message)): ?>
        <div class="alert alert-<?php echo htmlspecialchars($message['type']); ?>">
            <?php echo htmlspecialchars($message['text']); ?>
        </div>
    <?php endif; ?>
    <div class="d-flex justify-content-between mb-4">
        <span>Bienvenue, <?php echo htmlspecialchars(
            // récupérer username si nécessaire
            $_SESSION['username'] ?? 'Utilisateur'
        ); ?></span>
        <a href="?logout=1" class="btn btn-danger">Déconnexion</a>

    </div>

    <!-- Recherche -->
    <form method="POST" class="d-flex mb-4">
        <input type="text" name="search" class="form-control me-2" placeholder="Rechercher..." value="<?php echo htmlspecialchars($searchTerm); ?>">
        <button class="btn btn-primary">Rechercher</button>
    </form>

    <!-- Upload -->
    <div class="card p-4 mb-4 shadow-sm">
        <h4>Télécharger un fichier</h4>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="file" id="file" class="form-control" required>
            <div id="file-preview">Aucun aperçu</div>
            <input type="text" name="new_name" class="form-control mt-2" placeholder="Nom du fichier (optionnel)">
            <button class="btn btn-success mt-2">Télécharger</button>
        </form>
    </div>

    <!-- Liste fichiers -->
    <div class="card p-4 shadow-sm">
        <h4>Fichiers</h4>
        <?php foreach ($files as $file): ?>
            <div class="file-item">
                <div class="file-thumb">
                    <?php $mime = mime_content_type($file['file_path']);
                    if (str_starts_with($mime,'image/')) echo "<img src='{$file['file_path']}' alt='IMG'>";
                    elseif ($mime==='application/pdf') echo '<span>PDF</span>';
                    else echo '<span>Fichier</span>';
                    ?>
                </div>
                <div class="flex-grow-1">
                    <?php echo htmlspecialchars($file['file_name']); ?>
                    <div class="mt-1">
                        <a href="<?php echo htmlspecialchars($file['file_path']); ?>" class="btn btn-sm btn-outline-primary">Voir</a>
                        <a href="?delete=<?php echo $file['id']; ?>" class="btn btn-sm btn-outline-warning">Corbeille</a>
                        <a href="?favorite=<?php echo $file['id']; ?>" class="btn btn-sm btn-outline-<?php echo $file['is_favorite']?'danger':'success'; ?>">
                            <?php echo $file['is_favorite']?'Retirer favori':'Ajouter favori'; ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.getElementById('file').addEventListener('change', e => {
    const f = e.target.files[0], p = document.getElementById('file-preview');
    if (f && f.type.startsWith('image/')) {
        const r = new FileReader();
        r.onload = ev => p.innerHTML = `<img src='${ev.target.result}'>`;
        r.readAsDataURL(f);
    } else p.innerHTML = '<span>Aucun aperçu</span>';
});
</script>
</body>
</html>
