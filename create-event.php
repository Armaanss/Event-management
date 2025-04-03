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

// Handle event creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $event_name = trim($_POST['event_name']);
    $description = trim($_POST['description']);
    
    // Validate event name
    if (empty($event_name)) {
        $errors['event_name'] = "Event name is required";
    }
    
    // Validate description
    if (empty($description)) {
        $errors['description'] = "Description is required";
    }
    
    // If no errors, create event
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO events (event_name, description, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $event_name, $description);
        
        if ($stmt->execute()) {
            $event_id = $stmt->insert_id;
            $success = true;
            
            // Handle event image upload
            if (isset($_FILES["event_image"]) && $_FILES["event_image"]["error"] == 0) {
                $allowed = ["jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png"];
                $filename = $_FILES["event_image"]["name"];
                $filetype = $_FILES["event_image"]["type"];
                $filesize = $_FILES["event_image"]["size"];
                
                // Verify file extension
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                if (array_key_exists($ext, $allowed) && in_array($filetype, $allowed)) {
                    // Create uploads directory if it doesn't exist
                    if (!file_exists("uploads")) {
                        mkdir("uploads", 0777, true);
                    }
                    
                    // Save the file with event ID as name
                    $new_filename = "uploads/event" . $event_id . "." . $ext;
                    move_uploaded_file($_FILES["event_image"]["tmp_name"], $new_filename);
                }
            }
            
            // Redirect to event details page
            header("refresh:3;url=event-details.php?id=" . $event_id);
        } else {
            $errors['db'] = "Error: " . $stmt->error;
        }
        
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - Event Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header class="main-header">
            <h1>Create Event</h1>
            <nav>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="events.php">Events</a></li>
                    <li><a href="my-events.php">My Events</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>
        
        <div class="content">
            <div class="form-container">
                <h2>Create a New Event</h2>
                
                <?php if ($success): ?>
                    <div class="success-message">
                        Event created successfully! Redirecting to event details page...
                    </div>
                <?php endif; ?>
                
                <?php if (isset($errors['db'])): ?>
                    <div class="error-message">
                        <?php echo $errors['db']; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="event_name">Event Name</label>
                        <input type="text" id="event_name" name="event_name" value="<?php echo isset($_POST['event_name']) ? htmlspecialchars($_POST['event_name']) : ''; ?>">
                        <?php if (isset($errors['event_name'])): ?>
                            <span class="error"><?php echo $errors['event_name']; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="5"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                        <?php if (isset($errors['description'])): ?>
                            <span class="error"><?php echo $errors['description']; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="event_image">Event Image</label>
                        <input type="file" id="event_image" name="event_image" accept="image/*">
                    </div>
                    
                    <div class="form-group">
                        <button type="submit">Create Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html>

