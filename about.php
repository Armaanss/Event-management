<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - Event Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header class="main-header">
            <h1>About Us</h1>
            <nav>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="events.php">Events</a></li>
                    <li><a href="my-events.php">My Events</a></li>
                    <li><a href="about.php" class="active">About</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>
        
        <div class="content">
            <div class="about-section">
                <h2>About Event Management System</h2>
                
                <p>Welcome to the Event Management System, a platform designed to connect people with events they love. Our mission is to make event discovery, registration, and management as seamless as possible.</p>
                
                <p>Whether you're looking to attend a music concert, art exhibition, tech conference, or any other type of event, our platform provides a comprehensive solution for all your event needs.</p>
                
                <h3>Our Features</h3>
                <ul>
                    <li>Easy event discovery and registration</li>
                    <li>Personalized event recommendations</li>
                    <li>Secure user authentication</li>
                    <li>Profile management</li>
                    <li>Event creation and management</li>
                </ul>
                
                <h3>Our Team</h3>
                <div class="team-grid">
                    <div class="team-member">
                        <img src="uploads/team1.jpg" alt="Team Member 1">
                        <h3>John Doe</h3>
                        <p>Founder & CEO</p>
                    </div>
                    <div class="team-member">
                        <img src="uploads/team2.jpg" alt="Team Member 2">
                        <h3>Jane Smith</h3>
                        <p>CTO</p>
                    </div>
                    <div class="team-member">
                        <img src="uploads/team3.jpg" alt="Team Member 3">
                        <h3>Michael Johnson</h3>
                        <p>Lead Developer</p>
                    </div>
                    <div class="team-member">
                        <img src="uploads/team4.jpg" alt="Team Member 4">
                        <h3>Emily Brown</h3>
                        <p>UI/UX Designer</p>
                    </div>
                </div>
                
                <h3>Contact Us</h3>
                <p>If you have any questions, feedback, or concerns, please don't hesitate to contact us:</p>
                <p>Email: info@eventmanagementsystem.com</p>
                <p>Phone: +1 (123) 456-7890</p>
                <p>Address: 123 Event Street, City, Country</p>
            </div>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html>

