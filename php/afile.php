<?php
require('fpdf.php'); // Include FPDF library

// Database connection parameters
$servername = "localhost"; // Your database server
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
    // Retrieve form data
    $start_roll_no = intval($_POST['start_roll_no']);
    $end_roll_no = intval($_POST['end_roll_no']);
    $internal_no = intval($_POST['internal_no']);
    $num_classes = intval($_POST['num_classes']);
    $class_no = $_POST['class_no'];
    $seats_per_class = intval($_POST['seats_per_class']);
    $department = $_POST['department'];

    // Calculate number of students
    $total_students = $end_roll_no - $start_roll_no + 1;

    // Check if total students can fit in the given classes
    if ($total_students > $num_classes * $seats_per_class) {
        die("Not enough seats available for the number of students.");
    }

    // Create instance of FPDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    
    // Title
    $pdf->Cell(0, 10, "Seat Allotment for $department", 0, 1, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(30, 10, "Roll No", 1);
    $pdf->Cell(30, 10, "Seat No", 1);
    $pdf->Cell(30, 10, "Class No", 1);
    $pdf->Ln();

    // Seat allotment logic
    $seat_no = 1;
    for ($roll_no = $start_roll_no; $roll_no <= $end_roll_no; $roll_no++) {
        // Add to PDF
        $pdf->Cell(30, 10, $roll_no, 1);
        $pdf->Cell(30, 10, $seat_no, 1);
        $pdf->Cell(30, 10, $class_no, 1);
        $pdf->Ln();
        
        // Insert into the database for retrieval later
        $sql = "INSERT INTO seats (roll_no, class_no, seat_no, internal_no, dept) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiis", $roll_no, $class_no, $seat_no, $internal_no, $department); // Adjust according to your table schema
        $stmt->execute();

        $seat_no++;
        if ($seat_no > $seats_per_class) {
            $seat_no = 1; // Reset seat number for the next class
            $class_no++;   // Move to the next class
        }
    }

    // Output the PDF
    $pdf->Output('D', 'Seat_Allotment.pdf'); // Download PDF
}

// Close the database connection
$conn->close();
?>
