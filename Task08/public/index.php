<?php
$pdo = new PDO("sqlite:" . __DIR__ . "/../data/university.db");
$groupId = $_GET['group_id'] ?? '';
$groups = $pdo->query("SELECT * FROM groups ORDER BY number")->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT s.*, g.number as g_num, g.major as g_major
        FROM students s JOIN groups g ON s.group_id = g.id";
if ($groupId) $sql .= " WHERE g.id = " . (int)$groupId;
$sql .= " ORDER BY g.number, s.full_name";
$students = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление студентами</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; font-size: 13px; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: left; }
        .btn { padding: 4px 8px; text-decoration: none; border: 1px solid #333; background: #eee; color: #000; font-size: 11px; }
    </style>
</head>
<body>
    <h2>Список студентов (Полная информация)</h2>
    <form method="GET">
        Группа: <select name="group_id" onchange="this.form.submit()">
            <option value="">-- Все --</option>
            <?php foreach($groups as $g): ?>
                <option value="<?= $g['id'] ?>" <?= $groupId == $g['id'] ? 'selected' : '' ?>><?= $g['number'] ?></option>
            <?php endforeach; ?>
        </select>
    </form>
    <br>
    <table>
        <tr>
            <th>Группа</th><th>Направление</th><th>ФИО</th><th>Пол</th><th>Дата рождения</th><th>Зачетка</th><th>Действия</th>
        </tr>
        <?php foreach($students as $s): ?>
        <tr>
            <td><b><?= $s['g_num'] ?></b></td>
            <td><small><?= $s['g_major'] ?></small></td>
            <td><?= htmlspecialchars($s['full_name']) ?></td>
            <td><?= $s['gender'] ?></td>
            <td><?= $s['birth_date'] ?></td>
            <td><?= $s['card_id'] ?></td>
            <td>
                <a class="btn" href="student_form.php?id=<?= $s['id'] ?>">Редактировать</a>
                <a class="btn" href="student_delete.php?id=<?= $s['id'] ?>" onclick="return confirm('Удалить?')">Удалить</a>
                <a class="btn" href="exams.php?student_id=<?= $s['id'] ?>" style="background:#e1f5fe">Результаты экзаменов</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <br><a href="student_form.php" class="btn" style="background:#d4edda; padding:10px;">+ Добавить студента</a>
</body>
</html>