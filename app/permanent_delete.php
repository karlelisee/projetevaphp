<?php
require 'db.php';

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM files WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $file = $stmt->fetch();

    if ($file && file_exists($file['file_path'])) {
        unlink($file['file_path']);
    }

    $stmt = $pdo->prepare("DELETE FROM files WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}

header("Location: trash.php");
exit;
?>
