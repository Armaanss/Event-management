<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get all events
$events = [];
$result = $conn->query("SELECT * FROM events ORDER BY created_at DESC");
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - Event Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header class="main-header">
            <h1>Events</h1>
            <nav>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="events.php" class="active">Events</a></li>
                    <li><a href="my-events.php">My Events</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>
        
        <div class="content">
            <div class="events-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>All Events</h2>
                    <a href="create-event.php" class="btn btn-small">Create New Event</a>
                </div>
                
                <div class="events-grid">
                    <?php foreach ($events as $event): ?>
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
            </div>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html>

