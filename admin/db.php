<?php
// Set up database connection parameters
$servername = "localhost";
$username = "u775625162_tagramt";
$password = "147Tagramt@";
$dbname = "paywell";
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "paywell";


// Create a connection to the database
$link = mysqli_connect($servername, $username, $password, $dbname);

if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}

// // Prepare a SQL statement to execute
// $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username=? AND password=?");

// // Bind the parameters to the SQL statement
// mysqli_stmt_bind_param($stmt, "ss", $username, $password);

// // Sanitize user input to prevent SQL injection attacks
// $username = mysqli_real_escape_string($conn, $_POST['username']);
// $password = mysqli_real_escape_string($conn, $_POST['password']);

// // Execute the SQL statement
// mysqli_stmt_execute($stmt);

// // Get the result set
// $result = mysqli_stmt_get_result($stmt);

// // Fetch the results as an associative array
// $row = mysqli_fetch_assoc($result);

// // Close the database connection
// mysqli_close($conn);
?>