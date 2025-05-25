<?php
require 'db.php';

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("UPDATE files SET deleted = 1 WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}

header("Location: index.php");
exit;
?>
