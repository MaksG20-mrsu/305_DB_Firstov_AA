<?php
$pdo = new PDO("sqlite:" . __DIR__ . "/../data/university.db");
$id = $_GET['id'] ?? null;
$st = ['full_name'=>'', 'group_id'=>'', 'gender'=>'М', 'birth_date'=>'2005-01-01', 'card_id'=>''];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $st = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [$_POST['group_id'], $_POST['full_name'], $_POST['gender'], $_POST['birth_date'], $_POST['card_id']];
    if ($id) {
        $data[] = $id;
        $pdo->prepare("UPDATE students SET group_id=?, full_name=?, gender=?, birth_date=?, card_id=? WHERE id=?")->execute($data);
    } else {
        $pdo->prepare("INSERT INTO students (group_id, full_name, gender, birth_date, card_id) VALUES (?,?,?,?,?)")->execute($data);
    }
    header("Location: index.php"); exit;
}
$groups = $pdo->query("SELECT * FROM groups ORDER BY number")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru"><head><meta charset="UTF-8"><title>Карточка</title></head>
<body>
    <h3><?= $id ? 'Редактировать' : 'Добавить' ?> студента</h3>
    <form method="POST">
        ФИО: <input type="text" name="full_name" value="<?= htmlspecialchars($st['full_name']) ?>" required><br><br>
        Группа: <select name="group_id">
            <?php foreach($groups as $g): ?>
                <option value="<?= $g['id'] ?>" <?= $st['group_id']==$g['id']?'selected':'' ?>><?= $g['number'] ?> (<?= $g['major'] ?>)</option>
            <?php endforeach; ?>
        </select><br><br>
        Пол: <input type="radio" name="gender" value="М" <?= $st['gender']=='М'?'checked':'' ?>> М
             <input type="radio" name="gender" value="Ж" <?= $st['gender']=='Ж'?'checked':'' ?>> Ж<br><br>
        Дата рождения: <input type="date" name="birth_date" value="<?= $st['birth_date'] ?>"><br><br>
        № зачетки: <input type="text" name="card_id" value="<?= $st['card_id'] ?>"><br><br>
        <button type="submit">Сохранить</button> <a href="index.php">Отмена</a>
    </form>
</body></html>