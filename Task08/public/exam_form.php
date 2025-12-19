<?php
$pdo = new PDO("sqlite:" . __DIR__ . "/../data/university.db");
$id = $_GET['id'] ?? null;
$studentId = $_GET['student_id'] ?? null;

$ex = ['student_id' => $studentId, 'discipline_id' => '', 'grade' => '5', 'exam_date' => date('Y-m-d')];
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM exams WHERE id = ?");
    $stmt->execute([$id]);
    $ex = $stmt->fetch(PDO::FETCH_ASSOC);
    $studentId = $ex['student_id'];
}

$groups = $pdo->query("SELECT * FROM groups ORDER BY number")->fetchAll(PDO::FETCH_ASSOC);
$students = $pdo->query("SELECT id, full_name, group_id FROM students ORDER BY full_name")->fetchAll(PDO::FETCH_ASSOC);

$disciplines = [];
if ($studentId) {
    $st = $pdo->prepare("SELECT s.*, g.major, g.start_year FROM students s JOIN groups g ON s.group_id = g.id WHERE s.id = ?");
    $st->execute([$studentId]);
    $studentInfo = $st->fetch(PDO::FETCH_ASSOC);

    $examYear = date('Y', strtotime($ex['exam_date']));
    $course = ($examYear - $studentInfo['start_year']) + 1;
    if ($course < 1) $course = 1;

    $stmtD = $pdo->prepare("SELECT * FROM disciplines WHERE major = ? AND course = ?");
    $stmtD->execute([$studentInfo['major'], $course]);
    $disciplines = $stmtD->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = [$_POST['student_id'], $_POST['discipline_id'], $_POST['exam_date'], $_POST['grade']];
    if ($id) { $d[] = $id; $pdo->prepare("UPDATE exams SET student_id=?, discipline_id=?, exam_date=?, grade=? WHERE id=?")->execute($d); }
    else { $pdo->prepare("INSERT INTO exams (student_id, discipline_id, exam_date, grade) VALUES (?,?,?,?)")->execute($d); }
    header("Location: exams.php?student_id=".$_POST['student_id']); exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Ввод экзамена</title></head>
<body>
    <h3>Ввод результата экзамена</h3>
    <form method="POST">
        Студент:
        <select name="student_id" onchange="window.location.href='?id=<?=$id?>&student_id='+this.value">
            <option value="">-- Выберите --</option>
            <?php foreach($students as $s): ?>
                <option value="<?= $s['id'] ?>" <?= $studentId == $s['id'] ? 'selected' : '' ?>><?= $s['full_name'] ?></option>
            <?php endforeach; ?>
        </select><br><br>

        Дата: <input type="date" name="exam_date" value="<?= $ex['exam_date'] ?>" onchange="window.location.href='?id=<?=$id?>&student_id=<?=$studentId?>&date='+this.value">
        <br><br>

        Дисциплина (для <?= $course ?? '?' ?> курса):<br>
        <select name="discipline_id" required>
            <?php foreach($disciplines as $d): ?>
                <option value="<?= $d['id'] ?>" <?= $ex['discipline_id']==$d['id']?'selected':'' ?>><?= $d['name'] ?></option>
            <?php endforeach; ?>
        </select><br><br>

        Оценка: <input type="number" name="grade" min="2" max="5" value="<?= $ex['grade'] ?>"><br><br>
        <button type="submit">Сохранить</button> <a href="index.php">Отмена</a>
    </form>
</body></html>