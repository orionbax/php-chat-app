<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$last_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

if (!$user_id) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid user']);
    exit;
}

try {
    $stmt = $db->prepare("
        SELECT id, sender_id, receiver_id, message, timestamp 
        FROM messages 
        WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
        AND id > ?
        ORDER BY timestamp ASC
    ");
    
    $stmt->execute([
        $_SESSION['user_id'],
        $user_id,
        $user_id,
        $_SESSION['user_id'],
        $last_id
    ]);
    
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode(['messages' => $messages]);
} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error']);
} 