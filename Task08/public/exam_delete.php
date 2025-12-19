<?php
$pdo = new PDO("sqlite:" . __DIR__ . "/../data/university.db");
$pdo->prepare("DELETE FROM exams WHERE id = ?")->execute([$_GET['id']]);
header("Location: exams.php?student_id=" . $_GET['student_id']);