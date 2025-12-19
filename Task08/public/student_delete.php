<?php
$pdo = new PDO("sqlite:" . __DIR__ . "/../data/university.db");
$pdo->prepare("DELETE FROM students WHERE id = ?")->execute([$_GET['id']]);
header("Location: index.php");