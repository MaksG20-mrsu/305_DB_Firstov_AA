<?php
$pdo = new PDO("sqlite:" . __DIR__ . '/university.db');
$year = (int)date('Y');

$gStmt = $pdo->prepare("SELECT number FROM groups WHERE graduation_year <= ?");
$gStmt->execute([$year]);
$groupList = $gStmt->fetchAll(PDO::FETCH_COLUMN);

$filter = $_GET['group'] ?? '';

$sql = "SELECT g.number, g.major, s.full_name, s.gender, s.birth_date, s.student_id_card
        FROM students s JOIN groups g ON s.group_id = g.id
        WHERE g.graduation_year <= :year";
$params = ['year' => $year];

if ($filter && in_array($filter, $groupList)) {
    $sql .= " AND g.number = :g";
    $params['g'] = $filter;
}
$sql .= " ORDER BY g.number, s.full_name";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>University Students</title>
    <style>
        table { border-collapse: collapse; width: 100%; font-family: sans-serif; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h1>Student List</h1>
    <form method="GET">
        Filter by Group:
        <select name="group" onchange="this.form.submit()">
            <option value="">-- All Groups --</option>
            <?php foreach($groupList as $g): ?>
                <option value="<?= $g ?>" <?= $filter == $g ? 'selected' : '' ?>><?= $g ?></option>
            <?php endforeach; ?>
        </select>
    </form>

    <br>
    <table>
        <thead>
            <tr>
                <th>Group</th><th>Major</th><th>Full Name</th><th>Gender</th><th>Birth Date</th><th>Card No</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($students as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['number']) ?></td>
                <td><?= htmlspecialchars($s['major']) ?></td>
                <td><?= htmlspecialchars($s['full_name']) ?></td>
                <td><?= htmlspecialchars($s['gender']) ?></td>
                <td><?= htmlspecialchars($s['birth_date']) ?></td>
                <td><?= htmlspecialchars($s['student_id_card']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>