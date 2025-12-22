<?php
$pdo = new PDO('sqlite:../data/clinic.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_GET['id'];
$doctor_id = $_GET['doctor_id'];

$stmt = $pdo->prepare("DELETE FROM performed_services WHERE id = ?");
$stmt->execute([$id]);

header("Location: services.php?doctor_id=$doctor_id");
exit;
