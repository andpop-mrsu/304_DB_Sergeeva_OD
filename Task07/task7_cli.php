<?php

function connectDatabase(): PDO {
    $pdo = new PDO('sqlite:' . __DIR__ . '/clinic.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');
    return $pdo;
}

function selectAllDoctors(PDO $pdo): array {
    $query = "SELECT id, name FROM doctors ORDER BY name";
    $stmt = $pdo->prepare($query);
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

    if ($doctorId !== null) {
        $query .= " WHERE d.id = :doctor_id";
    }

    $query .= " ORDER BY d.name, ps.performed_date";

    $stmt = $pdo->prepare($query);

    if ($doctorId !== null) {
        $stmt->bindParam(':doctor_id', $doctorId, PDO::PARAM_INT);
    }

    $stmt->execute();
    return $stmt->fetchAll();
}

function displayDoctorsList(array $doctors): void {
    echo "\n╔════════════════════════════╗\n";
    echo "║      Врачи клиники        ║\n";
    echo "╠══════╦═════════════════════╣\n";
    echo "║ ID   ║ Имя                 ║\n";
    echo "╠══════╬═════════════════════╣\n";
    foreach ($doctors as $d) {
        printf("║ %-4s ║ %-19s ║\n", $d['id'], mb_substr($d['name'],0,19));
    }
    echo "╚══════╩═════════════════════╝\n\n";
}

function getDoctorId(array $validIds): ?int {
    while (true) {
        $input = readline("Введите ID врача (Enter для всех): ");
        if (trim($input) === '') return null;
        if (!ctype_digit($input)) continue;
        $id = (int)$input;
        if (!in_array($id, $validIds, true)) continue;
        return $id;
    }
}

function renderServicesTable(array $services): void {
    if (empty($services)) {
        echo "Данные не найдены.\n";
        return;
    }

    echo "╔══════╦════════════════════════╦════════════╦══════════════════════╦══════════╗\n";
    echo "║ ID   ║ Врач                   ║ Дата       ║ Услуга               ║ Цена     ║\n";
    echo "╠══════╬════════════════════════╬════════════╬══════════════════════╬══════════╣\n";

    foreach ($services as $s) {
        printf(
            "║ %-4s ║ %-22s ║ %-10s ║ %-20s ║ %8.2f ║\n",
            $s['doctor_id'],
            mb_substr($s['doctor_name'],0,22),
            $s['work_date'],
            mb_substr($s['service_name'],0,20),
            $s['service_price']
        );
    }

    echo "╚══════╩════════════════════════╩════════════╩══════════════════════╩══════════╝\n";
}

function main(): void {
    $pdo = connectDatabase();
    $doctors = selectAllDoctors($pdo);

    if (empty($doctors)) {
        echo "Врачи не найдены.\n";
        return;
    }

    displayDoctorsList($doctors);
    $doctorId = getDoctorId(array_column($doctors, 'id'));
    $services = selectPerformedServices($pdo, $doctorId);
    renderServicesTable($services);
}

main();
