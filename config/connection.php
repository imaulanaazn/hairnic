<?php

$servername = "mysql.railway.internal";
$username = "root";
$password = "RqVoxYzQPBBLMPsKUTxeOpAPFYZToTzb";
$dbname = "railway";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
