<?php
session_start();

// Assuming the user is logged in and their ID is stored in the session
$user_id = $_SESSION['user_id'];

// Check if the session has a user ID
if (!isset($user_id)) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

// Database connection details (Modify with your own details)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "financetracker";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get the total credited amount for the logged-in user
$sql = "SELECT SUM(amount) AS total_credited FROM transactions WHERE transaction_type = 'credit' AND user_id = ?"; // Use single quotes around 'credit'
$stmt = $conn->prepare($sql);

// Check if prepare was successful
if (!$stmt) {
    echo json_encode(['error' => 'SQL prepare statement failed: ' . $conn->error]);
    exit();
}

// Bind parameters and execute
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the total amount
$total_credited = 0; // Initialize correctly
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_credited = $row['total_credited'];
}

$stmt->close();
$conn->close();

// Return the result as JSON
echo json_encode(['total_credited' => $total_credited]); // Correct variable name
?>