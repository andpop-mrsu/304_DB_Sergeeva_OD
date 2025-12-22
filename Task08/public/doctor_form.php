<?php
$pdo = new PDO('sqlite:../data/clinic.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_GET['id'] ?? null;

// список специальностей
$specialties = $pdo->query("SELECT * FROM specialties")->fetchAll(PDO::FETCH_ASSOC);

// если редактирование — загружаем врача
$doctor = null;
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = ?");
    $stmt->execute([$id]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
}

// сохранение формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $specialty_id = $_POST['specialty_id'];
    $salary = $_POST['salary_percentage'];

    if ($id) {
        $stmt = $pdo->prepare("
            UPDATE doctors 
            SET name = ?, specialty_id = ?, salary_percentage = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $specialty_id, $salary, $id]);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO doctors (name, specialty_id, salary_percentage)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$name, $specialty_id, $salary]);
    }

    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Врач</title>
</head>
<body>

<h2><?= $id ? 'Редактировать врача' : 'Добавить врача' ?></h2>

<form method="post">
    ФИО:<br>
    <input type="text" name="name" required value="<?= htmlspecialchars($doctor['name'] ?? '') ?>"><br><br>

    Специальность:<br>
    <select name="specialty_id" required>
        <?php foreach ($specialties as $s): ?>
            <option value="<?= $s['id'] ?>"
                <?= isset($doctor) && $doctor['specialty_id'] == $s['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($s['name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    Процент зарплаты:<br>
    <input type="number" step="0.1" name="salary_percentage" required
           value="<?= $doctor['salary_percentage'] ?? '' ?>"><br><br>

    <button type="submit">Сохранить</button>
</form>

<br>
<a href="index.php">Назад</a>

</body>
</html>
