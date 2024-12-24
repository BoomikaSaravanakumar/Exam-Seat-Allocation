<?php
$servername = "localhost"; // Change as needed
$username = "root"; // Change as needed
$password = ""; // Change as needed
$dbname = "exam"; // Change as needed

$conn =mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " .mysqli_error($conn));
}
else{
    echo "connected";
}

?>