<?php
session_start();
require 'backend/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $review_id = $_POST['review_id'];

    if ($action === 'like') {
        // Handle Like
        $stmt = $conn->prepare("INSERT INTO likes (user_id, review_id) VALUES (?, ?) 
                                ON DUPLICATE KEY UPDATE liked_at = CURRENT_TIMESTAMP");
        $stmt->execute([$user_id, $review_id]);
        echo json_encode(['success' => true]);
    } elseif ($action === 'save') {
        // Handle Save
        $stmt = $conn->prepare("INSERT INTO saves (user_id, review_id) VALUES (?, ?) 
                                ON DUPLICATE KEY UPDATE saved_at = CURRENT_TIMESTAMP");
        $stmt->execute([$user_id, $review_id]);
        echo json_encode(['success' => true]);
    } elseif ($action === 'reply') {
        // Handle Reply
        $reply_text = trim($_POST['reply_text']);
        if (!empty($reply_text)) {
            $stmt = $conn->prepare("INSERT INTO replies (user_id, review_id, reply_text) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $review_id, $reply_text]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Reply cannot be empty']);
        }
    } else {
        echo json_encode(['error' => 'Invalid action']);
    }
}
?>
