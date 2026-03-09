<?php
// config/database.php

$db_host = 'localhost';  // Yehi rahega
$db_user = 'zabrinxyz_xion';       // XAMPP mein default
$db_pass = '+Bww!#L#hwsDM*.R';           // XAMPP mein blank
$db_name = 'zabrinxyz_suitecrm7';    // Tumhara database name

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Optional - for debugging
// echo "Database connected successfully!";
?>