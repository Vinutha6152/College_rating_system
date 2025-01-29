<?php
require 'db.php'; // Include the database connection

session_start(); // Start the session to handle user login

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture form data
    $college_id = $_POST['college_id'];
    $placement_rating = $_POST['placement_rating'];
    $academics_rating = $_POST['academics_rating'];
    $sports_rating = $_POST['sports_rating'];
    $cafeteria_rating = $_POST['cafeteria_rating'];
    $dance_club_rating = $_POST['dance_club_rating'];

    // Calculate the overall rating
    $overall_rating = round(
        ($placement_rating + $academics_rating + $sports_rating + $cafeteria_rating + $dance_club_rating) / 5, 2
    );

    // Insert the data into the ratings table
    $stmt = $conn->prepare("INSERT INTO ratings (user_id, college_id, placement_rating, academics_rating, sports_rating, cafeteria_rating, dance_club_rating, overall_rating) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([ 
        $_SESSION['user_id'], 
        $college_id, 
        $placement_rating, 
        $academics_rating, 
        $sports_rating, 
        $cafeteria_rating, 
        $dance_club_rating, 
        $overall_rating
    ]);

    // Redirect to dashboard after successful form submission
    header('Location: ../dashboard.php'); // Make sure 'dashboard.php' exists and is correctly referenced
    exit(); // Terminate further script execution
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Rating Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', Arial, sans-serif;
        }

        body {
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
            padding: 20px;
        }

        .form-container {
            background: #fff;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }

        .form-container h2 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .input-box {
            position: relative;
            margin-bottom: 20px;
        }

        .input-box input {
            width: 100%;
            padding: 15px;
            font-size: 1rem;
            border: 2px solid #3498db;
            border-radius: 8px;
            outline: none;
            transition: 0.3s;
        }

        .input-box input:focus {
            border-color: #2980b9;
            box-shadow: 0 0 5px rgba(41, 128, 185, 0.4);
        }

        .input-box label {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            font-size: 1rem;
            color: #7f8c8d;
            pointer-events: none;
            transition: 0.3s;
        }

        .input-box input:focus ~ label,
        .input-box input:valid ~ label {
            top: -10px;
            font-size: 0.85rem;
            color: #3498db;
        }

        .input-box select {
            width: 100%;
            padding: 15px;
            font-size: 1rem;
            border: 2px solid #3498db;
            border-radius: 8px;
            outline: none;
            transition: 0.3s;
        }

        .input-box select:focus {
            border-color: #2980b9;
            box-shadow: 0 0 5px rgba(41, 128, 185, 0.4);
        }

        .btn {
            display: block;
            width: 100%;
            padding: 15px;
            font-size: 1.2rem;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: 600;
        }

        .btn:hover {
            background-color: #2980b9;
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .rating {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .rating input[type="radio"] {
            display: none;
        }

        .rating label {
            font-size: 1.5rem;
            color: #2c3e50;
            cursor: pointer;
            transition: 0.3s;
        }

        .rating label:hover,
        .rating input[type="radio"]:checked + label {
            color: #2980b9;
        }

        .rating input[type="radio"]:checked + label {
            font-weight: bold;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 1rem;
            background-color: #e74c3c;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            transition: 0.3s;
        }

        .back-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Rate Your College</h2>
        <form action="rating.php" method="POST">
            <div class="input-box">
                <label for="college_id">Select College</label>
                <select name="college_id" required>
                    <option value="1">College A</option>
                    <option value="2">College B</option>
                    <option value="3">College C</option>
                    <!-- Add more college options here -->
                </select>
            </div>

            <div class="rating">
                <label>Placement:</label>
                <div>
                    <input type="radio" id="placement_1" name="placement_rating" value="1"><label for="placement_1">1</label>
                    <input type="radio" id="placement_2" name="placement_rating" value="2"><label for="placement_2">2</label>
                    <input type="radio" id="placement_3" name="placement_rating" value="3"><label for="placement_3">3</label>
                    <input type="radio" id="placement_4" name="placement_rating" value="4"><label for="placement_4">4</label>
                    <input type="radio" id="placement_5" name="placement_rating" value="5"><label for="placement_5">5</label>
                </div>
            </div>

            <div class="rating">
                <label>Academics:</label>
                <div>
                    <input type="radio" id="academics_1" name="academics_rating" value="1"><label for="academics_1">1</label>
                    <input type="radio" id="academics_2" name="academics_rating" value="2"><label for="academics_2">2</label>
                    <input type="radio" id="academics_3" name="academics_rating" value="3"><label for="academics_3">3</label>
                    <input type="radio" id="academics_4" name="academics_rating" value="4"><label for="academics_4">4</label>
                    <input type="radio" id="academics_5" name="academics_rating" value="5"><label for="academics_5">5</label>
                </div>
            </div>

            <div class="rating">
                <label>Sports:</label>
                <div>
                    <input type="radio" id="sports_1" name="sports_rating" value="1"><label for="sports_1">1</label>
                    <input type="radio" id="sports_2" name="sports_rating" value="2"><label for="sports_2">2</label>
                    <input type="radio" id="sports_3" name="sports_rating" value="3"><label for="sports_3">3</label>
                    <input type="radio" id="sports_4" name="sports_rating" value="4"><label for="sports_4">4</label>
                    <input type="radio" id="sports_5" name="sports_rating" value="5"><label for="sports_5">5</label>
                </div>
            </div>

            <div class="rating">
                <label>Cafeteria:</label>
                <div>
                    <input type="radio" id="cafeteria_1" name="cafeteria_rating" value="1"><label for="cafeteria_1">1</label>
                    <input type="radio" id="cafeteria_2" name="cafeteria_rating" value="2"><label for="cafeteria_2">2</label>
                    <input type="radio" id="cafeteria_3" name="cafeteria_rating" value="3"><label for="cafeteria_3">3</label>
                    <input type="radio" id="cafeteria_4" name="cafeteria_rating" value="4"><label for="cafeteria_4">4</label>
                    <input type="radio" id="cafeteria_5" name="cafeteria_rating" value="5"><label for="cafeteria_5">5</label>
                </div>
            </div>

            <div class="rating">
                <label>Dance Club:</label>
                <div>
                    <input type="radio" id="dance_club_1" name="dance_club_rating" value="1"><label for="dance_club_1">1</label>
                    <input type="radio" id="dance_club_2" name="dance_club_rating" value="2"><label for="dance_club_2">2</label>
                    <input type="radio" id="dance_club_3" name="dance_club_rating" value="3"><label for="dance_club_3">3</label>
                    <input type="radio" id="dance_club_4" name="dance_club_rating" value="4"><label for="dance_club_4">4</label>
                    <input type="radio" id="dance_club_5" name="dance_club_rating" value="5"><label for="dance_club_5">5</label>
                </div>
            </div>

            <button type="submit" class="btn">Submit Rating</button>
        </form>
        <a href="../dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>

</body>
</html>
