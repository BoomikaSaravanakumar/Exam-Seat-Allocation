<?php
session_start();

// Database connection settings
$host = 'localhost';
$dbname = 'exam';
$username = 'root'; // Replace with your MySQL username
$password = '';     // Replace with your MySQL password

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $action = $_POST['action'];

    if ($action === 'signup') {
        // Handle Signup
        // Check if the username already exists
        $stmt = $conn->prepare("SELECT username FROM login WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['message'] = "Username is already taken. Please choose a different one.";
            header("Location: http://localhost/index.html");
            exit();
        } else {
            // Register the new user as a student
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $conn->prepare("INSERT INTO login (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Signup successful. Please log in.";
            } else {
                $_SESSION['message'] = "Signup failed: " . $stmt->error;
            }
            header("Location: http://localhost/index.html");
            exit();
        }
    } elseif ($action === 'signin') {
        // Handle Signin
        // Check if the username is admin
        if ($username === 'admin' && $password === 'admin@123') {
            // Successful login for admin
            header("Location: http://localhost/admin.html");
            exit();
        } else {
            // Handle login for students
            $stmt = $conn->prepare("SELECT password FROM login WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($hashed_password);
                $stmt->fetch();

                if (password_verify($password, $hashed_password)) {
                    // Successful login; redirect to student form
                    header("Location: http://localhost/student.html");
                    exit();
                } else {
                    $_SESSION['message'] = "Incorrect password.";
                }
            } else {
                $_SESSION['message'] = "No account found with that username.";
            }
        }
        header("Location:http://localhost/student.html");
        exit();
    }
}

$conn->close();
?>
