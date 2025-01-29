<?php
function getRatingIdForCollege($conn, $college_id) {
    $stmt = $conn->prepare("SELECT id FROM ratings WHERE college_id = ? LIMIT 1");
    $stmt->execute([$college_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['id'] ?? null;  // Return the rating ID or null if not found
}

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.html');
    exit();
}

require 'backend/db.php'; // Database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Check Ratings</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            transition: margin-left 0.3s ease;
        }
        .like-btn {
    font-size: 24px;
    cursor: pointer; /* Add this line */
}
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            width: 250px;
            height: 100%;
            background-color: #34495E;
            color: white;
            padding: 20px;
            transition: transform 0.3s ease;
            transform: translateX(0);
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 20px;
            display: block;
            padding: 10px 15px;
            border-radius: 5px;
            font-family:Papyrus;
        }

        .sidebar ul li a:hover {
            background-color: #1abc9c;
        }

        .sidebar.collapsed {
            transform: translateX(-300px);
        }

        /* Sidebar Toggle Button */
        .toggle-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: #34495E;
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

        /* Content Styles */
        .content {
            margin-left: 270px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .content.collapsed {
            margin-left: 20px;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: #fff;
            margin-left:20px;
            font-family:Garamond;
            color:#005A9C;
            font-size:20px;
        }

        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle; 
        }

        table th {
            background-color: #34495E;
            color: white; 
        }

        table td.review {
            text-align: left;
            white-space: pre-wrap;
            word-wrap: break-word;
            max-width: 300px;
            color:#DE3163;
        }
       
        h1, h2 {
            color: #34495E;
            font-family:Papyrus;
        }

        h1 {
            margin-bottom: 20px;
           text-align:center;  
         }

        h2 {
            margin-top: 40px;
            margin-left:20px;
        }

        .highlight {
            background-color: #ffeb3b;
        }

        .search-bar {
            margin-bottom: 20px;

        }

        .search-bar input[type="text"] {
            width: 300px;
            padding: 10px;
            font-size: 16px;
            border-radius:5px;
            margin-left:20px;
        }

        .search-bar button {
            padding: 10px 15px;
            font-size: 16px;
            background-color: #34495E;
            color: white;
            border: none;
            cursor: pointer;
            border-radius:5px;
        }

        .search-bar button:hover {
            background-color: #2c3e50;
        }

        /* Message Styles */
        .no-results-message {
            color: red;
            font-size: 18px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Sidebar Toggle Button -->
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>

    <!-- Sidebar -->
    <div class="sidebar">
        <ul><br><br><br>
            <li><a href="colleges.php">Rate a College</a></li>
            <li><a href="backend/logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>...Navigate College Choices with Ratings That Reflect Your Priorities!</h1>

        <!-- Search Bar -->
        <div class="search-bar">
            <form method="GET">
                <input type="text" name="search" placeholder="Search for a college..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <?php
        $resultsFound = false;
        $user_id = $_SESSION['user_id'];
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $searchPattern = "%$search%";

        // Fetch user's ratings
        $stmt = $conn->prepare("SELECT colleges.name AS college_name, ratings.*, reviews.review_text
                                FROM ratings
                                JOIN colleges ON ratings.college_id = colleges.id
                                LEFT JOIN reviews ON ratings.id = reviews.rating_id
                                WHERE ratings.user_id = ? AND (colleges.name LIKE ? OR ? = '')");
        $stmt->execute([$user_id, $searchPattern, $search]);
        $resultsFound = $stmt->rowCount() > 0;

        if (!$resultsFound && $search) {
            echo "<div class='no-results-message'>No results found for '$search'.</div>";
        }
        ?>

        <!-- User's Ratings -->
        <h2>Your Ratings</h2>
        <table>
            <tr>
                <th>College Name</th>
                <th>Placement</th>
                <th>Academics</th>
                <th>Sports</th>
                <th>Cafeteria</th>
                <th>Dance Club</th>
                <th>College Fest</th>
                <th>Faculty Expertise</th>
                <th>College Campus</th>
                <th>Technical Clubs</th>
                <th>Practical Labs</th>
                <th>Overall</th>
                <th>Review</th>
                <th>Likes</th>
                
            </tr>
            <?php
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rating_id = $row['id']; // Get rating ID
    $likes_count = getLikesCount($conn, $rating_id); // Fetch likes count
    $liked_by_user = hasUserLiked($conn, $_SESSION['user_id'], $rating_id); // Check if user has liked

    // Set heart icon based on whether the user has liked
    $heart_icon = $liked_by_user ? "❤️" : "🤍";
                $highlight = ($search && stripos($row['college_name'], $search) !== false) ? 'highlight' : '';
                $overall_rating = number_format(((
                    $row['placement_rating'] + 
                    $row['academics_rating'] + 
                    $row['sports_rating'] + 
                    $row['cafeteria_rating'] + 
                    $row['dance_club_rating'] +
                    $row['college_fest_rating'] +
                    $row['faculty_expertise_rating'] +
                    $row['college_campus_rating'] +
                    $row['technical_clubs_rating'] +
                    $row['practical_labs_rating']
                ) / 11), 1);
                echo "<tr class='{$highlight}'>
                        <td>{$row['college_name']}</td>
                        <td>{$row['placement_rating']}</td>
                        <td>{$row['academics_rating']}</td>
                        <td>{$row['sports_rating']}</td>
                        <td>{$row['cafeteria_rating']}</td>
                        <td>{$row['dance_club_rating']}</td>
                        <td>{$row['college_fest_rating']}</td>
                        <td>{$row['faculty_expertise_rating']}</td>
                        <td>{$row['college_campus_rating']}</td>
                        <td>{$row['technical_clubs_rating']}</td>
                        <td>{$row['practical_labs_rating']}</td>
                        <td>{$overall_rating}</td>
                        <td class='review'>{$row['review_text']}</td>
                         <td>
                <span class='like-btn' data-rating='{$rating_id}'>$heart_icon</span>
                <sub class='like-count'>$likes_count</sub>
            </td>
                      </tr>";
            }
            ?>
        </table>

        <!-- Other Users' Ratings -->
        <h2>Other User's Ratings</h2>
        <table>
            <tr>
                <th>College Name</th>
                <th>Placement</th>
                <th>Academics</th>
                <th>Sports</th>
                <th>Cafeteria</th>
                <th>Dance Club</th>
                <th>College Fest</th>
                <th>Faculty Expertise</th>
                <th>College Campus</th>
                <th>Technical Clubs</th>
                <th>Practical Labs</th>
                <th>Overall</th>
                <th>Review</th>
                <th>Likes</th>
            </tr>
            <?php
            $stmt = $conn->prepare("SELECT colleges.name AS college_name, ratings.*, reviews.review_text
                                    FROM ratings
                                    JOIN colleges ON ratings.college_id = colleges.id
                                    LEFT JOIN reviews ON ratings.id = reviews.rating_id
                                    WHERE ratings.user_id != ?");
            $stmt->execute([$user_id]);

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rating_id = $row['id']; // Get rating ID
                $likes_count = getLikesCount($conn, $rating_id); // Fetch likes count
                $liked_by_user = hasUserLiked($conn, $_SESSION['user_id'], $rating_id); // Check if user has liked
            
                // Set heart icon based on whether the user has liked
                $heart_icon = $liked_by_user ? "❤️" : "🤍";
                $overall_rating = number_format(((
                    $row['placement_rating'] + 
                    $row['academics_rating'] + 
                    $row['sports_rating'] + 
                    $row['cafeteria_rating'] + 
                    $row['dance_club_rating'] +
                    $row['college_fest_rating'] +
                    $row['faculty_expertise_rating'] +
                    $row['college_campus_rating'] +
                    $row['technical_clubs_rating'] +
                    $row['practical_labs_rating']
                ) / 11), 1);
                echo "<tr>
                        <td>{$row['college_name']}</td>
                        <td>{$row['placement_rating']}</td>
                        <td>{$row['academics_rating']}</td>
                        <td>{$row['sports_rating']}</td>
                        <td>{$row['cafeteria_rating']}</td>
                        <td>{$row['dance_club_rating']}</td>
                        <td>{$row['college_fest_rating']}</td>
                        <td>{$row['faculty_expertise_rating']}</td>
                        <td>{$row['college_campus_rating']}</td>
                        <td>{$row['technical_clubs_rating']}</td>
                        <td>{$row['practical_labs_rating']}</td>
                        <td>{$overall_rating}</td>
                        <td class='review'>{$row['review_text']}</td>
                         <td>
                <span class='like-btn' data-rating='{$rating_id}'>$heart_icon</span>
                <sub class='like-count'>$likes_count</sub>
            </td>
                      </tr>";
            }
            ?>
            
        </table>
          <!-- Average Ratings -->
          <h2>Average Ratings</h2>
<table>
    <tr>
        <th>College Name</th>
        <th>Average Placement</th>
        <th>Average Academics</th>
        <th>Average Sports</th>
        <th>Average Cafeteria</th>
        <th>Average Dance Club</th>
        <th>Average College Fest</th>
        <th>Average Faculty Expertise</th>
        <th>Average College Campus</th>
        <th>Average Technical Clubs</th>
        <th>Average Practical Labs</th>
        <th>Average Overall</th>
        <th>Likes</th>
    </tr>
    <?php
    // Query to fetch average ratings
    $stmt = $conn->query("SELECT colleges.name AS college_name,
                                 colleges.id AS college_id,   -- Add college_id to use for like check
                                 AVG(ratings.placement_rating) AS avg_placement,
                                 AVG(ratings.academics_rating) AS avg_academics,
                                 AVG(ratings.sports_rating) AS avg_sports,
                                 AVG(ratings.cafeteria_rating) AS avg_cafeteria,
                                 AVG(ratings.dance_club_rating) AS avg_dance_club,
                                 AVG(ratings.college_fest_rating) AS avg_college_fest,
                                 AVG(ratings.faculty_expertise_rating) AS avg_faculty_expertise,
                                 AVG(ratings.college_campus_rating) AS avg_college_campus,
                                 AVG(ratings.technical_clubs_rating) AS avg_technical_clubs,
                                 AVG(ratings.practical_labs_rating) AS avg_practical_labs,
                                 AVG((ratings.placement_rating + ratings.academics_rating + ratings.sports_rating + ratings.cafeteria_rating + ratings.dance_club_rating + ratings.college_fest_rating + ratings.faculty_expertise_rating + ratings.college_campus_rating + ratings.technical_clubs_rating + ratings.access_to_library_rating + ratings.practical_labs_rating) / 11) AS avg_overall
                          FROM ratings
                          JOIN colleges ON ratings.college_id = colleges.id
                          GROUP BY colleges.id");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $college_id = $row['college_id'];  // Get college_id for the current college
        $rating_id = getRatingIdForCollege($conn, $college_id);  // Fetch the rating ID associated with this college
        $likes_count = getLikesCount($conn, $rating_id);  // Fetch likes count
        $liked_by_user = hasUserLiked($conn, $_SESSION['user_id'], $rating_id);  // Check if the current user has liked
        
        // Set heart icon based on whether the user has liked
        $heart_icon = $liked_by_user ? "❤️" : "🤍";
        
        echo "<tr>
                <td>{$row['college_name']}</td>
                <td>" . number_format($row['avg_placement'], 1) . "</td>
                <td>" . number_format($row['avg_academics'], 1) . "</td>
                <td>" . number_format($row['avg_sports'], 1) . "</td>
                <td>" . number_format($row['avg_cafeteria'], 1) . "</td>
                <td>" . number_format($row['avg_dance_club'], 1) . "</td>
                <td>" . number_format($row['avg_college_fest'], 1) . "</td>
                <td>" . number_format($row['avg_faculty_expertise'], 1) . "</td>
                <td>" . number_format($row['avg_college_campus'], 1) . "</td>
                <td>" . number_format($row['avg_technical_clubs'], 1) . "</td>
                <td>" . number_format($row['avg_practical_labs'], 1) . "</td>
                <td>" . number_format($row['avg_overall'], 1) . "</td>
                <td>
                    <span class='like-btn' data-rating='{$rating_id}'>$heart_icon</span>
                    <sub class='like-count'>$likes_count</sub>
                </td>
              </tr>";
    }
    ?>
</table>

    </div>

    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.content').classList.toggle('collapsed');
        }
        document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".like-btn").forEach(button => {
        button.addEventListener("click", function() {
            let ratingId = this.dataset.rating;
            let likeCountElement = this.nextElementSibling;
            
            fetch("backend/like.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `rating_id=${ratingId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Toggle the heart icon
                    this.textContent = data.liked ? "❤️" : "🤍";
                    // Update the like count
                    likeCountElement.textContent = data.likes;
                }
            });
        });
    });
});

    </script>
</body>
</html>
