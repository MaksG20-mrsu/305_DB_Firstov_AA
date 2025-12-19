<?php
$pdo = new PDO("sqlite:" . __DIR__ . '/university.db');
$currentYear = (int)date('Y');

$stmt = $pdo->prepare("SELECT number FROM groups WHERE graduation_year <= ? ORDER BY number");
$stmt->execute([$currentYear]);
$groupList = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "Active groups: " . implode(", ", $groupList) . "\n";
echo "Enter group number (or press Enter for all): ";
$input = trim(fgets(STDIN));

if ($input !== "" && !in_array($input, $groupList)) {
    die("Invalid group number!\n");
}

$sql = "SELECT g.number, g.major, s.full_name, s.gender, s.birth_date, s.student_id_card
        FROM students s JOIN groups g ON s.group_id = g.id
        WHERE g.graduation_year <= :year";
if ($input) $sql .= " AND g.number = :group";
$sql .= " ORDER BY g.number, s.full_name";

$stmt = $pdo->prepare($sql);
$params = ['year' => $currentYear];
if ($input) $params['group'] = $input;
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$mask = "| %-5s | %-15.15s | %-30.30s | %-2s | %-10s | %-8s |\n";
$line = "+-------+-----------------+--------------------------------+----+------------+----------+\n";

echo $line;
printf($mask, 'Group', 'Major', 'Full Name', 'G', 'Birthday', 'ID Card');
echo $line;
foreach ($data as $r) {
    printf($mask, $r['number'], $r['major'], $r['full_name'], $r['gender'], $r['birth_date'], $r['student_id_card']);
}
echo $line;