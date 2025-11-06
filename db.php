<?php
$host = "localhost";
$user = "root";  // change if necessary
$pass = "";
$dbname = "user_portal_db"; // updated database name

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
