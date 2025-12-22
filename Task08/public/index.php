<?php
$pdo = new PDO('sqlite:../data/clinic.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "
    SELECT doctors.id, doctors.name, specialties.name AS specialty
    FROM doctors
    JOIN specialties ON doctors.specialty_id = specialties.id
    ORDER BY doctors.name
";
$doctors = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Врачи</title>
</head>
<body>

<h2>Список врачей</h2>

<table border="1" cellpadding="5">
    <tr>
        <th>ФИО</th>
        <th>Специальность</th>
        <th>Действия</th>
    </tr>

    <?php foreach ($doctors as $doctor): ?>
        <tr>
            <td><?= htmlspecialchars($doctor['name']) ?></td>
            <td><?= htmlspecialchars($doctor['specialty']) ?></td>
            <td>
                <a href="doctor_form.php?id=<?= $doctor['id'] ?>">Редактировать</a> |
                <a href="doctor_delete.php?id=<?= $doctor['id'] ?>">Удалить</a> |
                <a href="schedule.php?doctor_id=<?= $doctor['id'] ?>">График</a> |
                <a href="services.php?doctor_id=<?= $doctor['id'] ?>">Услуги</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<br>
<a href="doctor_form.php">Добавить врача</a>

</body>
</html>
