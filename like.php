<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$rating_id = $_POST['rating_id'] ?? 0;

// Check if the user already liked the rating
$stmt = $conn->prepare("SELECT * FROM rating_likes WHERE user_id = ? AND rating_id = ?");
$stmt->execute([$user_id, $rating_id]);
$liked = $stmt->rowCount() > 0;

if ($liked) {
    // Unlike the rating
    $conn->prepare("DELETE FROM rating_likes WHERE user_id = ? AND rating_id = ?")->execute([$user_id, $rating_id]);
} else {
    // Like the rating
    $conn->prepare("INSERT INTO rating_likes (user_id, rating_id) VALUES (?, ?)")->execute([$user_id, $rating_id]);
}

// Get updated likes count
$stmt = $conn->prepare("SELECT COUNT(*) AS likes FROM rating_likes WHERE rating_id = ?");
$stmt->execute([$rating_id]);
$likes = $stmt->fetch(PDO::FETCH_ASSOC)['likes'];

echo json_encode(["success" => true, "liked" => !$liked, "likes" => $likes]);
?>



