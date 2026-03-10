<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Your database connection code below...
$name = "localhost";
$user = "root";
$pass = "";
$dbname = "cricstrome";

$conn = new mysqli($name, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
