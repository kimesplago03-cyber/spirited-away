<?php
$server     = "localhost";
$username   = "root";
$password   = "";
$dbname     = "dbspirited";
$tablelog   = "register";
$tablelogin = "adminlogin";

$conn = mysqli_connect($server, $username, $password, $dbname);
$connection = $conn; // keep $connection so other pages don't break

if (!$conn) {
    die("Cannot connect to database: " . mysqli_connect_error());
}
?>
