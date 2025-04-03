<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = false;

// Get user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle profile picture upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_picture"])) {
    // Check if file was uploaded without errors
    if ($_FILES["profile_picture"]["error"] == 0) {
        $allowed = ["jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png"];
        $filename = $_FILES["profile_picture"]["name"];
        $filetype = $_FILES["profile_picture"]["type"];
        $filesize = $_FILES["profile_picture"]["size"];
        
        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!array_key_exists($ext, $allowed)) {
            $errors['file'] = "Error: Please select a valid file format.";
        }
        
        // Verify file size - 5MB maximum
        $maxsize = 5 * 1024 * 1024;
        if ($filesize > $maxsize) {
            $errors['file'] = "Error: File size is larger than the allowed limit (5MB).";
        }
        
        // Verify MIME type of the file
        if (in_array($filetype, $allowed)) {
            // Check if file exists before uploading
            if (file_exists("uploads/" . $filename)) {
                $filename = uniqid() . "-" . $filename;
            }
            
            // Create uploads directory if it doesn't exist
            if (!file_exists("uploads")) {
                mkdir("uploads", 0777, true);
            }
            
            // Move the uploaded file to the specified directory
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], "uploads/" . $filename)) {
                // Update user profile picture in database
                $profile_picture = "uploads/" . $filename;
                $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
                $stmt->bind_param("si", $profile_picture, $user_id);
                
                if ($stmt->execute()) {
                    $success = true;
                    // Update user data
                    $user['profile_picture'] = $profile_picture;
                } else {
                    $errors['db'] = "Error: " . $stmt->error;
                }
                
                $stmt->close();
            } else {
                $errors['file'] = "Error: There was a problem uploading your file. Please try again.";
            }
        } else {
            $errors['file'] = "Error: There was a problem with your upload. Please try again.";
        }
    } else {
        $errors['file'] = "Error: " . $_FILES["profile_picture"]["error"];
    }
}

// Get featured events
$featured_events = [];
$result = $conn->query("SELECT * FROM events ORDER BY id DESC LIMIT 4");
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $featured_events[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Event Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header class="main-header">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>!</h1>
            <nav>
                <ul>
                    <li><a href="home.php" class="active">Home</a></li>
                    <li><a href="events.php">Events</a></li>
                    <li><a href="my-events.php">My Events</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>
        
        <!-- Slider Section -->
        <div class="slider-container">
            <div class="slide active" style="background-image: url('uploads/slide1.jpg');">
                <div class="slide-content">
                    <h2>Welcome to Event Management System</h2>
                    <p>Discover and join amazing events happening around you</p>
                    <a href="events.php" class="btn btn-small">Browse Events</a>
                </div>
            </div>
            <div class="slide" style="background-image: url('uploads/slide2.jpg');">
                <div class="slide-content">
                    <h2>Create Your Own Events</h2>
                    <p>Share your passion with others by hosting your own events</p>
                    <a href="create-event.php" class="btn btn-small">Create Event</a>
                </div>
            </div>
            <div class="slide" style="background-image: url('uploads/slide3.jpg');">
                <div class="slide-content">
                    <h2>Connect with Like-minded People</h2>
                    <p>Meet people who share your interests and passions</p>
                    <a href="events.php" class="btn btn-small">Find Events</a>
                </div>
            </div>
            <div class="slide" style="background-image: url('uploads/slide4.jpg');">
                <div class="slide-content">
                    <h2>Stay Updated</h2>
                    <p>Never miss an event with our notification system</p>
                    <a href="my-events.php" class="btn btn-small">My Events</a>
                </div>
            </div>
            
            <div class="slider-nav">
                <div class="slider-nav-item active" data-slide="0"></div>
                <div class="slider-nav-item" data-slide="1"></div>
                <div class="slider-nav-item" data-slide="2"></div>
                <div class="slider-nav-item" data-slide="3"></div>
            </div>
            
            <div class="slider-arrow slider-arrow-left">&lt;</div>
            <div class="slider-arrow slider-arrow-right">&gt;</div>
        </div>
        
        <div class="content">
            <div class="profile-section">
                <h2>Your Profile</h2>
                
                <?php if ($success): ?>
                    <div class="success-message">
                        Profile picture updated successfully!
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($errors)): ?>
                    <div class="error-message">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo $error; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <div class="profile-container">
                    <div class="profile-picture">
                        <?php if (!empty($user['profile_picture'])): ?>
                            <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
                        <?php else: ?>
                            <div class="no-profile-picture">
                                <span><?php echo substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="profile_picture">Update Profile Picture</label>
                                <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                            </div>
                            <button type="submit">Upload</button>
                        </form>
                    </div>
                    
                    <div class="profile-details">
                        <h3>User Information</h3>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Contact:</strong> <?php echo htmlspecialchars($user['contact']); ?></p>
                        <p><strong>Event Interest:</strong> <?php echo htmlspecialchars($user['event_interest']); ?></p>
                        <p><strong>Registered On:</strong> <?php echo date('F j, Y', strtotime($user['reg_date'])); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Featured Events Section -->
            <div class="featured-events">
                <h2>Featured Events</h2>
                <div class="events-grid">
                    <?php foreach ($featured_events as $event): ?>
                        <div class="event-card">
                            <div class="event-card-image">
                                <img src="uploads/event<?php echo $event['id']; ?>.jpg" alt="<?php echo htmlspecialchars($event['event_name']); ?>">
                            </div>
                            <div class="event-card-content">
                                <h3><?php echo htmlspecialchars($event['event_name']); ?></h3>
                                <p><?php echo htmlspecialchars(substr($event['description'], 0, 100) . '...'); ?></p>
                            </div>
                            <div class="event-card-footer">
                                <span class="date"><?php echo date('F j, Y', strtotime($event['created_at'])); ?></span>
                                <a href="event-details.php?id=<?php echo $event['id']; ?>" class="btn btn-small">View Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="events.php" class="btn btn-small">View All Events</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html>

