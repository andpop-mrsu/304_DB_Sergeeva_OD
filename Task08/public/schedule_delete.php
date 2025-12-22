<?php
$pdo = new PDO('sqlite:../data/clinic.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$doctor_id = $_GET['doctor_id'];

$stmt = $pdo->prepare("
    DELETE FROM appointments
    WHERE doctor_id = ?
");
$stmt->execute([$doctor_id]);

header("Location: schedule.php?doctor_id=$doctor_id");
exit;
