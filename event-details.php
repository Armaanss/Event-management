<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$event_id = isset($_GET['id']) ? $_GET['id'] : 0;
$success = false;
$error = '';

// Get event details
$event = null;
if ($event_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();
    } else {
        $error = "Event not found.";
    }
    $stmt->close();
} else {
    $error = "Invalid event ID.";
}

// Handle event registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_event'])) {
    // Check if user is already registered for this event
    $stmt = $conn->prepare("SELECT * FROM event_registrations WHERE user_id = ? AND event_id = ?");
    $stmt->bind_param("ii", $user_id, $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $error = "You are already registered for this event.";
    } else {
        // Register user for the event
        $stmt = $conn->prepare("INSERT INTO event_registrations (user_id, event_id, registration_date) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $user_id, $event_id);
        
        if ($stmt->execute()) {
            $success = true;
        } else {
            $error = "Error registering for the event: " . $stmt->error;
        }
    }
    $stmt->close();
}

// Check if user is registered for this event
$is_registered = false;
if ($event_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM event_registrations WHERE user_id = ? AND event_id = ?");
    $stmt->bind_param("ii", $user_id, $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $is_registered = true;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details - Event Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header class="main-header">
            <h1>Event Details</h1>
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
            <?php if ($success): ?>
                <div class="success-message">
                    You have successfully registered for this event!
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($event): ?>
                <div class="event-detail">
                    <div class="event-detail-header">
                        <div class="event-detail-image">
                            <img src="uploads/event<?php echo $event['id']; ?>.jpg" alt="<?php echo htmlspecialchars($event['event_name']); ?>">
                        </div>
                        
                        <div class="event-detail-info">
                            <h2><?php echo htmlspecialchars($event['event_name']); ?></h2>
                            
                            <div class="event-detail-meta">
                                <div class="event-detail-meta-item">
                                    <span>Date:</span>
                                    <span><?php echo date('F j, Y', strtotime($event['created_at'])); ?></span>
                                </div>
                                <div class="event-detail-meta-item">
                                    <span>Location:</span>
                                    <span>Event Hall</span>
                                </div>
                                <div class="event-detail-meta-item">
                                    <span>Organizer:</span>
                                    <span>Event Management System</span>
                                </div>
                            </div>
                            
                            <div class="event-detail-description">
                                <h3>Description</h3>
                                <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                            </div>
                            
                            <div class="event-detail-actions">
                                <?php if ($is_registered): ?>
                                    <button class="btn btn-secondary" disabled>Already Registered</button>
                                <?php else: ?>
                                    <form method="POST">
                                        <input type="hidden" name="register_event" value="1">
                                        <button type="submit" class="btn">Register for Event</button>
                                    </form>
                                <?php endif; ?>
                                <a href="events.php" class="btn btn-secondary">Back to Events</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="error-message">
                    Event not found. <a href="events.php">Go back to events</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html>

