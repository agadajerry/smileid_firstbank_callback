<?php

$servername = "localhost";
$username = "admin";
$password = "root";
$dbname = "first_customer";




$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}





