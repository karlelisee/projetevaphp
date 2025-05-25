<?php
$host   = 'db';
$dbname = 'file_manager'; // <- c'est cette base qui doit contenir "users"
$user   = 'user';
$pass   = 'userpass';

try {
    // 1) Connexion initiale (optionnel ici)
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

    // 2) ✅ Connexion à la bonne base
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );

} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}
