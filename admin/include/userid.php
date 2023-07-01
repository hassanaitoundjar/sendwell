<?php
include ('../admin/db.php');
// Initialize the session
session_start();
// Get user information
$user_id = $_SESSION['user_id'];

$stmt = mysqli_prepare($link, "SELECT username, email FROM users WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $username = $row['username'];
    $email = $row['email'];
}

?>