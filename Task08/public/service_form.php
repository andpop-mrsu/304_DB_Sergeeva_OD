<?php
$pdo = new PDO('sqlite:../data/clinic.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_GET['id'] ?? null;
$doctor_id = $_GET['doctor_id'];

$services = $pdo->query("SELECT * FROM services")->fetchAll(PDO::FETCH_ASSOC);

$record = null;
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM performed_services WHERE id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = $_POST['service_id'];
    $date = $_POST['date'];
    $price = $_POST['price'];
    $time = $_POST['time'];
    $duration = $_POST['duration'];

    if ($id) {
        $stmt = $pdo->prepare("
            UPDATE performed_services
            SET service_id=?, performed_date=?, actual_price=?, performed_time=?, actual_duration_minutes=?
            WHERE id=?
        ");
        $stmt->execute([$service_id, $date, $price, $time, $duration, $id]);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO performed_services (doctor_id, service_id, performed_date, performed_time, actual_duration_minutes, actual_price)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$doctor_id, $service_id, $date, $time, $duration, $price]);
    }

    header("Location: services.php?doctor_id=$doctor_id");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Услуга</title>
</head>
<body>

<h2>Услуга</h2>

<form method="post">
    Услуга:<br>
    <select name="service_id">
        <?php foreach ($services as $s): ?>
            <option value="<?= $s['id'] ?>"
                <?= isset($record) && $record['service_id'] == $s['id'] ? 'selected' : '' ?>>
                <?= $s['name'] ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    Дата:<br>
    <input type="date" name="date" value="<?= $record['performed_date'] ?? '' ?>"><br><br>

    Время:<br>
    <input type="time" name="time" value="<?= $record['performed_time'] ?? '' ?>"><br><br>

    Длительность (мин):<br>
    <input type="number" name="duration" value="<?= $record['actual_duration_minutes'] ?? '' ?>"><br><br>

    Цена:<br>
    <input type="number" step="0.01" name="price" value="<?= $record['actual_price'] ?? '' ?>"><br><br>

    <button type="submit">Сохранить</button>
</form>

<br>
<a href="services.php?doctor_id=<?= $doctor_id ?>">Назад</a>

</body>
</html>
