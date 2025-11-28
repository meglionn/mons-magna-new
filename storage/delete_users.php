<?php
require __DIR__ . '/../vendor/autoload.php';

$host='127.0.0.1';
$db='mons_magna';
$user='root';
$pass='';
$charset='utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0;');
    $pdo->exec('TRUNCATE TABLE `users`;');
    $pdo->exec('SET FOREIGN_KEY_CHECKS=1;');
    $stmt = $pdo->query('SELECT COUNT(*) FROM `users`');
    $count = (int)$stmt->fetchColumn();
    echo "users_remaining: $count\n";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
