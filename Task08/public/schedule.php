<?php
$pdo = new PDO('sqlite:../data/clinic.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$doctor_id = $_GET['doctor_id'] ?? null;

$stmt = $pdo->prepare("
    SELECT appointment_date, appointment_time, status
    FROM appointments
    WHERE doctor_id = ?
    ORDER BY appointment_date, appointment_time
");
$stmt->execute([$doctor_id]);
$schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>График врача</title>
</head>
<body>

<h2>График работы врача</h2>

<table border="1" cellpadding="5">
    <tr>
        <th>Дата</th>
        <th>Время</th>
        <th>Статус</th>
        <th>Действия</th>
    </tr>

    <?php foreach ($schedule as $row): ?>
        <tr>
            <td><?= $row['appointment_date'] ?></td>
            <td><?= $row['appointment_time'] ?></td>
            <td><?= $row['status'] ?></td>
            <td>
                <a href="schedule_form.php?doctor_id=<?= $doctor_id ?>">Редактировать</a> |
                <a href="schedule_delete.php?doctor_id=<?= $doctor_id ?>">Удалить</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<br>
<a href="schedule_form.php?doctor_id=<?= $doctor_id ?>">Добавить запись</a><br><br>
<a href="index.php">Назад</a>

</body>
</html>
