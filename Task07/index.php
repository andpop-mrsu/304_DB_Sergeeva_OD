<?php
function connectDatabase(): PDO {
    $pdo = new PDO('sqlite:' . __DIR__ . '/clinic.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');
    return $pdo;
}

function selectAllDoctors(PDO $pdo): array {
    $stmt = $pdo->prepare("SELECT id, name FROM doctors ORDER BY name");
    $stmt->execute();
    return $stmt->fetchAll();
}

function selectPerformedServices(PDO $pdo, ?int $doctorId = null): array {
    $query = "SELECT 
                d.id AS doctor_id,
                d.name AS doctor_name,
                ps.performed_date AS work_date,
                s.name AS service_name,
                ps.actual_price AS service_price
              FROM performed_services ps
              JOIN doctors d ON ps.doctor_id = d.id
              JOIN services s ON ps.service_id = s.id";

    if ($doctorId !== null):
        $query .= " WHERE d.id = :doctor_id";
    endif;

    $query .= " ORDER BY d.name, ps.performed_date";

    $stmt = $pdo->prepare($query);

    if ($doctorId !== null):
        $stmt->bindParam(':doctor_id', $doctorId, PDO::PARAM_INT);
    endif;

    $stmt->execute();
    return $stmt->fetchAll();
}

$pdo = connectDatabase();
$doctors = selectAllDoctors($pdo);

$selectedDoctorId = null;
$selectedDoctorName = 'Все врачи';

if (isset($_GET['doctor_id']) && $_GET['doctor_id'] !== ''):
    $selectedDoctorId = filter_var($_GET['doctor_id'], FILTER_VALIDATE_INT);
    if ($selectedDoctorId !== false):
        foreach ($doctors as $d):
            if ($d['id'] == $selectedDoctorId):
                $selectedDoctorName = $d['name'];
                break;
            endif;
        endforeach;
    endif;
endif;

$services = selectPerformedServices($pdo, $selectedDoctorId);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список оказанных услуг</title>
</head>
<body>
    <h1>Отчет по оказанным услугам</h1>

    <form method="GET">
        <label for="doctor_id">Выберите врача:</label>
        <select name="doctor_id" id="doctor_id">
            <option value="">Все врачи</option>
            <?php foreach ($doctors as $d): ?>
                <option value="<?= $d['id'] ?>" <?= ($selectedDoctorId == $d['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($d['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Показать</button>
    </form>

    <h2>Список оказанных услуг (<?= htmlspecialchars($selectedDoctorName) ?>)</h2>

    <?php if (empty($services)): ?>
        <p>Данные не найдены.</p>
    <?php else: ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>ID врача</th>
                    <th>ФИО</th>
                    <th>Дата</th>
                    <th>Услуга</th>
                    <th>Стоимость</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $s): ?>
                    <tr>
                        <td><?= $s['doctor_id'] ?></td>
                        <td><?= htmlspecialchars($s['doctor_name']) ?></td>
                        <td><?= $s['work_date'] ?></td>
                        <td><?= htmlspecialchars($s['service_name']) ?></td>
                        <td><?= number_format($s['service_price'], 2, '.', '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
