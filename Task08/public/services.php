<?php
$pdo = new PDO('sqlite:../data/clinic.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$doctor_id = $_GET['doctor_id'];

$stmt = $pdo->prepare("
    SELECT ps.id, s.name, ps.performed_date, ps.actual_price
    FROM performed_services ps
    JOIN services s ON ps.service_id = s.id
    WHERE ps.doctor_id = ?
    ORDER BY ps.performed_date DESC
");
$stmt->execute([$doctor_id]);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оказанные услуги</title>
</head>
<body>

<h2>Оказанные услуги</h2>

<table border="1" cellpadding="5">
    <tr>
        <th>Услуга</th>
        <th>Дата</th>
        <th>Цена</th>
        <th>Действия</th>
    </tr>

    <?php foreach ($services as $row): ?>
        <tr>
            <td><?= $row['name'] ?></td>
            <td><?= $row['performed_date'] ?></td>
            <td><?= $row['actual_price'] ?></td>
            <td>
                <a href="service_form.php?id=<?= $row['id'] ?>&doctor_id=<?= $doctor_id ?>">Редактировать</a> |
                <a href="service_delete.php?id=<?= $row['id'] ?>&doctor_id=<?= $doctor_id ?>">Удалить</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<br>
<a href="service_form.php?doctor_id=<?= $doctor_id ?>">Добавить услугу</a><br><br>
<a href="index.php">Назад</a>

</body>
</html>
