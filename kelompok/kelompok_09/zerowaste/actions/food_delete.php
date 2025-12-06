<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donatur') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

$donatur_id = $_SESSION['user_id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: ../donatur/manage_food.php?status=error");
    exit();
}

$sql = "
    UPDATE food_stocks
    SET deleted_at = NOW(), status = 'habis'
    WHERE id = ? AND donatur_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $id, $donatur_id);
$stmt->execute();

header("Location: ../donatur/manage_food.php?status=deleted");
exit();
