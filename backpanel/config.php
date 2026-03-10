<?php
session_start();
$name = "localhost";
$user = "root";
$pass = "";
$dbname = "cricstrome";

$conn = new mysqli($name, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
