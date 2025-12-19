<?php
$pdo = new PDO("sqlite:" . __DIR__ . "/../data/university.db");
$studentId = $_GET['student_id'];
$st = $pdo->prepare("SELECT s.*, g.number FROM students s JOIN groups g ON s.group_id = g.id WHERE s.id = ?");
$st->execute([$studentId]);
$student = $st->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT e.*, d.name FROM exams e JOIN disciplines d ON e.discipline_id = d.id WHERE e.student_id = ? ORDER BY e.exam_date ASC");
$stmt->execute([$studentId]);
$exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Экзамены</title>
<style>table { border-collapse: collapse; width: 600px; } td, th { border: 1px solid #ccc; padding: 8px; }</style></head>
<body>
    <h3>Экзамены: <?= htmlspecialchars($student['full_name']) ?> (Гр. <?= $student['number'] ?>)</h3>
    <table>
        <tr><th>Дата</th><th>Предмет</th><th>Оценка</th><th>Действия</th></tr>
        <?php foreach($exams as $e): ?>
        <tr>
            <td><?= $e['exam_date'] ?></td>
            <td><?= htmlspecialchars($e['name']) ?></td>
            <td><b><?= $e['grade'] ?></b></td>
            <td>
                <a href="exam_form.php?id=<?= $e['id'] ?>&student_id=<?= $studentId ?>">Ред.</a> |
                <a href="exam_delete.php?id=<?= $e['id'] ?>&student_id=<?= $studentId ?>" onclick="return confirm('Удалить?')">Уд.</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <br><a href="exam_form.php?student_id=<?= $studentId ?>">Добавить результат</a> | <a href="index.php">Назад</a>
</body></html>