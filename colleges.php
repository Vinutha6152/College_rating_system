<?php
session_start();
require 'backend/db.php';  // Include your database connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];  // Get the user ID from the session

// Fetch colleges from the database in alphabetical order
try {
    $stmt = $conn->prepare("SELECT DISTINCT id, name FROM colleges ORDER BY name ASC");
    $stmt->execute();
    $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $colleges = [];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get selected college ID, ratings, and review from the form
    $college_id = $_POST['college_id'];
    
    // If "Others" is selected, insert the custom college name
    if ($college_id == 'other') {
        $college_name = $_POST['other_college_name'];
        // Insert the new college into the database (or handle appropriately)
        try {
            $stmt = $conn->prepare("INSERT INTO colleges (name) VALUES (?)");
            $stmt->execute([$college_name]);
            $college_id = $conn->lastInsertId();  // Get the ID of the newly inserted college
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    
    // Ratings data
    $placement_rating = $_POST['placement_rating'];
    $academics_rating = $_POST['academics_rating'];
    $sports_rating = $_POST['sports_rating'];
    $cafeteria_rating = $_POST['cafeteria_rating'];
    $dance_club_rating = $_POST['dance_club_rating'];
    $college_fest_rating = $_POST['college_fest_rating'];
    $faculty_expertise_rating = $_POST['faculty_expertise_rating'];
    $college_campus_rating = $_POST['college_campus_rating'];
    $technical_clubs_rating = $_POST['technical_clubs_rating'];
    $practical_labs_rating = $_POST['practical_labs_rating'];
    $review = $_POST['review'];

    // Insert the rating and review into the database
    try {
        $stmt = $conn->prepare("INSERT INTO ratings (user_id, college_id, placement_rating, academics_rating, sports_rating, cafeteria_rating, dance_club_rating, college_fest_rating, faculty_expertise_rating, college_campus_rating, technical_clubs_rating, practical_labs_rating) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([ 
            $user_id, 
            $college_id, 
            $placement_rating, 
            $academics_rating, 
            $sports_rating, 
            $cafeteria_rating, 
            $dance_club_rating, 
            $college_fest_rating,
            $faculty_expertise_rating,
            $college_campus_rating,
            $technical_clubs_rating,
            $practical_labs_rating
        ]);

        // Insert the review into the reviews table
        $rating_id = $conn->lastInsertId();  // Get the last inserted rating ID
        $stmt = $conn->prepare("INSERT INTO reviews (rating_id, review_text) VALUES (?, ?)");
        $stmt->execute([$rating_id, $review]);

        // Redirect to the check ratings page after successful submission
        header("Location: colleges.php?success=true");
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate a College</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f1f2f6;
            color: #333;
            display: flex;
            margin: 0;
            overflow-x: hidden;
            transition: all 0.3s ease;
            
        }
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: #fff;
            padding: 20px;
            position: fixed;
            height: 100%;
            left: 0;
            z-index: 100;
            transition: transform 0.3s ease;
        }
        .sidebar .logout {
            color: white;
            padding:12px;
            font-style:bold;
            font-size:20px;
            font-family:Papyrus;
            text-decoration:none;
            
        }
        .sidebar .logout:hover {
            
            background-color:#16a085;
            font-style:bold;
            border-radius:6px;
        }
        .sidebar .nav-links a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: 500;
            transition: background-color 0.3s ease;
            font-family:Papyrus;
        }
        .sidebar .nav-links a:hover {
            background-color: #1abc9c;
        }
        .sidebar.hidden {
            transform: translateX(-100%);
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: 100%;
            transition: margin-left 0.3s ease;
        }
        .main-content.collapsed {
            margin-left: 0;
        }
        .container {
            background-color: #fff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
          
        }
        h1 {
            font-size: 36px;
            color: #2c3e50;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 700;
            font-family:Papyrus;
        }
        .form-group {
    margin-bottom: 10px;
    padding-bottom: 20px;  /* Add some space after the field */
    border-bottom: 2px solid #bdc3c7;  /* Light gray line between fields */
    background-color:white;
    margin-left:100px;
    line-height:30px;
    
  

}
.form-group:last-child {
   
    border-bottom: none;  /* Remove the line after the last field */
}
        .form-group label {
            font-size: 18px;
            color: #34495;
            font-style:bold;
            font-size:20px;
            font-family:Georgia;
        }
        .form-group select, .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border-radius: 6px;
            border: 1px solid #bdc3c7;
        }
        .star-rating {
    display: flex;  /* Use flexbox */
    justify-content:flex-end ;  /* Align stars to the right */
    align-items: center;  /* Vertically center the stars and the label */
    gap: 40px;  /* Space between stars */
    width: 100%;  /* Ensure the container spans the full width */
    direction: rtl; 
    margin-left:500px;
    margin-top:-32px;
}

        .star-rating input {
            display: none;
        }
        .star-rating label {
    cursor: pointer;
    font-size: 30px;
    color: #002D62;
    transition: color 0.3s ease;
   margin-right:20px;
    margin-left: 8px;  /* Space between the label text and stars */
}
        .star-rating input:checked ~ label,
        .star-rating input:checked ~ label ~ label {
            color: #f39c12;
        }
        .btn-submit {
            background-color: #1abc9c;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            width: 20%;
            margin-left:700px;
        }
        .btn-submit:hover {
            background-color: #16a085;
            transition:0.3s ;
        }
        .success-message {
            color: green;
            font-size: 18px;
            margin-top: 20px;
            text-align: center;
        }
        .toggle-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color:  #2c3e50;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
            z-index: 1000;
        }
        .toggle-btn:hover {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #16a085;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            font-size: 20px;
            z-index: 101;
            border-radius: 20%;
        }
    </style>
</head>
<body>
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    <div class="sidebar" id="sidebar">
        <div class="nav-links"><br><br><br><br>
            <a href="checkratings.php">Review Ratings</a>
        </div>
        <a href="backend/logout.php" class="logout">Logout</a>
    </div>

    <div class="main-content" id="main-content">
        <div class="container">
            <h1>Hello👋🏼<?php echo htmlspecialchars($_SESSION['full_name']); ?>... Share Your Voice - Review and Rate Your College Today</h1>
            <form action="colleges.php" method="POST">
                <div class="form-group">
                    <label for="college_id">Select College</label>
                    <select name="college_id" id="college_id" required onchange="toggleOtherCollegeInput()">
                        <option value="">Select a College</option>
                        <?php foreach ($colleges as $college): ?>
                            <option value="<?php echo htmlspecialchars($college['id']); ?>"><?php echo htmlspecialchars($college['name']); ?></option>
                        <?php endforeach; ?>
                        <option value="other">Other (Please Specify)</option>
                    </select>
                </div>

                <div class="form-group" id="other_college_name_container" style="display: none;">
                    <label for="other_college_name">Enter College Name</label>
                    <input type="text" name="other_college_name" id="other_college_name" placeholder="Enter the name of your college">
                </div>

                <?php 
                $categories = [
                    'Placement', 'Academics', 'Sports', 'Cafeteria', 'Dance Club', 'College Fest', 
                    'Faculty Expertise', 'College Campus', 'Technical Clubs', 'Practical Labs'
                ];
                ?>
                <?php foreach ($categories as $category): ?>
                    <div class="form-group">
                        <label><?php echo $category; ?> Rating</label>
                        <div class="star-rating">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" id="<?php echo strtolower(str_replace(' ', '_', $category)) . '_' . $i; ?>" name="<?php echo strtolower(str_replace(' ', '_', $category)); ?>_rating" value="<?php echo $i; ?>" required>
                                <label for="<?php echo strtolower(str_replace(' ', '_', $category)) . '_' . $i; ?>">★</label>
                            <?php endfor; ?>
                        </div>
                    </div>
                  

                <?php endforeach; ?>

                <div class="form-group">
                    <label for="review">Write a Review</label>
                    <textarea name="review" id="review" rows="5" placeholder="Share your experience about this college" required></textarea>
                </div>

                <button type="submit" class="btn-submit">Submit Rating</button>
            </form>

            <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
                <p class="success-message">Your rating and review have been submitted successfully! You will be redirected shortly.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            sidebar.classList.toggle('hidden');
            mainContent.classList.toggle('collapsed');
        }

        function toggleOtherCollegeInput() {
            const collegeSelect = document.getElementById('college_id');
            const otherCollegeContainer = document.getElementById('other_college_name_container');
            if (collegeSelect.value == 'other') {
                otherCollegeContainer.style.display = 'block';
            } else {
                otherCollegeContainer.style.display = 'none';
            }
        }

        if (window.location.search.includes('success=true')) {
    console.log("Redirecting in 2 seconds...");
    setTimeout(function() {
        window.location.href = "checkratings.php";
    }, 2000);
}
        
    </script>
</body>
</html>
