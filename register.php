<?php
session_start();
include 'db_connect.php';

$errors = [];
$success = false;

// If form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $event_interest = $_POST['event_interest'];
    
    // Validate first name
    if (empty($first_name)) {
        $errors['first_name'] = "First name is required";
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $first_name)) {
        $errors['first_name'] = "Only letters and white space allowed";
    }
    
    // Validate last name
    if (empty($last_name)) {
        $errors['last_name'] = "Last name is required";
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $last_name)) {
        $errors['last_name'] = "Only letters and white space allowed";
    }
    
    // Validate contact
    if (empty($contact)) {
        $errors['contact'] = "Contact number is required";
    } elseif (!preg_match("/^[0-9]{10}$/", $contact)) {
        $errors['contact'] = "Contact must be 10 digits";
    }
    
    // Validate email
    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors['email'] = "Email already exists";
        }
        $stmt->close();
    }
    
    // Validate password
    if (empty($password)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters";
    }
    
    // Validate confirm password
    if (empty($confirm_password)) {
        $errors['confirm_password'] = "Please confirm your password";
    } elseif ($password != $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match";
    }
    
    // Validate event interest
    if (empty($event_interest)) {
        $errors['event_interest'] = "Please select an event";
    }
    
    // If no errors, insert user into database
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, contact, email, password, event_interest) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $first_name, $last_name, $contact, $email, $hashed_password, $event_interest);
        
        if ($stmt->execute()) {
            $success = true;
            // Redirect to login page after 3 seconds
            header("refresh:3;url=login.php");
        } else {
            $errors['db'] = "Error: " . $stmt->error;
        }
        
        $stmt->close();
    }
}

// Get events from database
$events = [];
$result = $conn->query("SELECT * FROM events");
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
    <title>Register - Event Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h1>Register</h1>
            
            <?php if ($success): ?>
                <div class="success-message">
                    Registration successful! Redirecting to login page...
                </div>
            <?php endif; ?>
            
            <?php if (isset($errors['db'])): ?>
                <div class="error-message">
                    <?php echo $errors['db']; ?>
                </div>
            <?php endif; ?>
            
            <form id="registrationForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>">
                    <?php if (isset($errors['first_name'])): ?>
                        <span class="error"><?php echo $errors['first_name']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>">
                    <?php if (isset($errors['last_name'])): ?>
                        <span class="error"><?php echo $errors['last_name']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="contact">Contact Number</label>
                    <input type="text" id="contact" name="contact" value="<?php echo isset($_POST['contact']) ? htmlspecialchars($_POST['contact']) : ''; ?>">
                    <?php if (isset($errors['contact'])): ?>
                        <span class="error"><?php echo $errors['contact']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <?php if (isset($errors['email'])): ?>
                        <span class="error"><?php echo $errors['email']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password">
                    <?php if (isset($errors['password'])): ?>
                        <span class="error"><?php echo $errors['password']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password">
                    <?php if (isset($errors['confirm_password'])): ?>
                        <span class="error"><?php echo $errors['confirm_password']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="event_interest">Event Interest</label>
                    <select id="event_interest" name="event_interest">
                        <option value="">Select an event</option>
                        <?php foreach ($events as $event): ?>
                            <option value="<?php echo htmlspecialchars($event['event_name']); ?>" <?php echo (isset($_POST['event_interest']) && $_POST['event_interest'] == $event['event_name']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($event['event_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['event_interest'])): ?>
                        <span class="error"><?php echo $errors['event_interest']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <button type="submit">Register</button>
                </div>
                
                <div class="form-footer">
                    Already have an account? <a href="login.php">Login</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html>

