<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get events the user is registered for
$registered_events = [];
$stmt = $conn->prepare("
    SELECT e.* 
    FROM events e
    JOIN event_registrations er ON e.id = er.event_id
    WHERE er.user_id = ?
    ORDER BY er.registration_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $registered_events[] = $row;
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events - Event Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header class="main-header">
            <h1>My Events</h1>
            <nav>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="events.php">Events</a></li>
                    <li><a href="my-events.php" class="active">My Events</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>
        
        <div class="content">
            <div class="my-events-section">
                <h2>Events You're Registered For</h2>
                
                <?php if (empty($registered_events)): ?>
                    <div class="error-message">
                        You haven't registered for any events yet. <a href="events.php">Browse events</a> to find something you're interested in!
                    </div>
                <?php else: ?>
                    <div class="events-grid">
                        <?php foreach ($registered_events as $event): ?>
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
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html>

