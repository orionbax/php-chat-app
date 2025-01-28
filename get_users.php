<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    if ($search) {
        $stmt = $db->prepare("SELECT id, username FROM users WHERE username LIKE ? AND id != ?");
        $stmt->execute(['%' . $search . '%', $_SESSION['user_id']]);
    } else {
        $stmt = $db->prepare("SELECT id, username FROM users WHERE id != ?");
        $stmt->execute([$_SESSION['user_id']]);
    }
    
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($users);
} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error']);
} 