<?php
// fetch_allocation.php

// Database connection parameters
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "exam"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve registration number and internal number from POST request
    $reg_no = intval($_POST['reg_no']);
    $internal_no = intval($_POST['internal_no']); // Assuming you have an internal number field

    // Prepare SQL statement to fetch the seat allocation details
    $sql = "SELECT class_no, seat_no, dept FROM seats WHERE Roll_no = ? AND internal_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $reg_no, $internal_no);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any results were returned
    if ($result->num_rows > 0) {
        // Output the seat allocation details
        $row = $result->fetch_assoc();
        echo "<h2>Seat Allocation Details</h2>";
        echo "<p>Registration No: " . htmlspecialchars($reg_no) . "</p>";
        echo "<p>Internal No: " . htmlspecialchars($internal_no) . "</p>";
        echo "<p>Class No: " . htmlspecialchars($row['class_no']) . "</p>";
        echo "<p>Seat No: " . htmlspecialchars($row['seat_no']) . "</p>";
        echo "<p>Department: " . htmlspecialchars($row['dept']) . "</p>";
    } else {
        echo "<p>No allocation found for Registration No: " . htmlspecialchars($reg_no) . " and Internal No: " . htmlspecialchars($internal_no) . "</p>";
    }
}

// Close the database connection
$conn->close();
?>
