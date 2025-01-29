<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'];
    $collegeId = $_GET['collegeId'];
    $count = $_GET['count'];

    // Update action count in the database
    $stmt = $conn->prepare("INSERT INTO actions (action_type, college_id, user_id, action_count)
                            VALUES (?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE action_count = ?");
    $stmt->execute([$action, $collegeId, $_SESSION['user_id'], $count, $count]);

    // Respond to the frontend
    echo json_encode(["status" => "success"]);
}
?>
