<?php
// db.php - Database connection

$host = 'localhost';   // Hostname (typically 'localhost' on a local server)
$dbname = 'college_rating_system';   // Database name
$username = 'root';    // MySQL username (usually 'root' for local setups)
$password = '';        // MySQL password (empty by default for local setups)


function getLikesCount($conn, $rating_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM rating_likes WHERE rating_id = ?");
    $stmt->execute([$rating_id]);
    return $stmt->fetchColumn();
}

function hasUserLiked($conn, $user_id, $rating_id) {
    $stmt = $conn->prepare("SELECT * FROM rating_likes WHERE user_id = ? AND rating_id = ?");
    $stmt->execute([$user_id, $rating_id]);
    return $stmt->rowCount() > 0;
}


// Try to establish a PDO connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception for better error handling
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Uncomment the following line for debugging purposes (remove it in production)
    // echo "Connected successfully"; 
} catch (PDOException $e) {
    // In case of an error, display the error message and stop execution
    die("Connection failed: " . $e->getMessage());
}
?>
