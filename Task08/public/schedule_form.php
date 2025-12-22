<?php
$pdo = new PDO('sqlite:../data/clinic.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$doctor_id = $_GET['doctor_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $time = $_POST['time'];
    $service_id = $_POST['service_id'];
    $patient_id = $_POST['patient_id'];

    $stmt = $pdo->prepare("
        INSERT INTO appointments (patient_id, doctor_id, service_id, appointment_date, appointment_time)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$patient_id, $doctor_id, $service_id, $date, $time]);

    header("Location: schedule.php?doctor_id=$doctor_id");
    exit;
}

$patients = $pdo->query("SELECT * FROM patients")->fetchAll(PDO::FETCH_ASSOC);
$services = $pdo->query("SELECT * FROM services")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>График</title>
</head>
<body>

<h2>Добавить запись в график</h2>

<form method="post">
    Дата:<br>
    <input type="date" name="date" required><br><br>

    Время:<br>
    <input type="time" name="time" required><br><br>

    Пациент:<br>
    <select name="patient_id">
        <?php foreach ($patients as $p): ?>
            <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
        <?php endforeach; ?>
    </select><br><br>

    Услуга:<br>
    <select name="service_id">
        <?php foreach ($services as $s): ?>
            <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <button type="submit">Сохранить</button>
</form>

<br>
<a href="schedule.php?doctor_id=<?= $doctor_id ?>">Назад</a>

</body>
</html>
