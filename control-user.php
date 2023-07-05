<?php
// Include your database connection and helper functions
include ('./admin/db.php');
session_start();

// Check if the user is logged in and is an admin, otherwise redirect to the login page
if (!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['admin']) && $_SESSION['admin'] === true)) {
    header("Location: control-user.php");
    exit;
}

// Reactivate user account
if (isset($_GET['action']) && $_GET['action'] === 'reactivate' && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Update the user's status to active
    $stmt = mysqli_prepare($link, "UPDATE users SET is_blocked = 1 WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    $_SESSION['status'] = "User account reactivated successfully.";
    header("Location: control-user.php");
    exit;
}

// Cancel user account
if (isset($_GET['action']) && $_GET['action'] === 'cancel' && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Update the user's status to canceled
    $stmt = mysqli_prepare($link, "UPDATE users SET is_blocked = 0 WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    $_SESSION['status'] = "User account canceled successfully.";
    header("Location: control-user.php");
    exit;
}

// Delete user account
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Delete the user account from the database
    $stmt = mysqli_prepare($link, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    $_SESSION['status'] = "User account deleted successfully.";
    header("Location: control-user.php");
    exit;
}

// Fetch all users
$stmt = mysqli_prepare($link, "SELECT id, username, email, is_blocked FROM users");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Page</title>
</head>

<body>
    <h1>Admin Page</h1>

    <?php if (isset($_SESSION['status'])) : ?>
    <p><?php echo $_SESSION['status']; ?></p>
    <?php unset($_SESSION['status']); ?>
    <?php endif; ?>

    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach ($users as $user) : ?>
        <tr>
            <td><?php echo $user['id']; ?></td>
            <td><?php echo $user['username']; ?></td>
            <td><?php echo $user['email']; ?></td>
            <td><?php echo $user['is_blocked'] ? 'Active' : 'Inactive'; ?></td>
            <td>
                <?php if ($user['is_blocked']) : ?>
                <a href="control-user.php?action=cancel&user_id=<?php echo $user['id']; ?>">Cancel</a>
                <?php else : ?>
                <a href="control-user.php?action=reactivate&user_id=<?php echo $user['id']; ?>">Reactivate</a>
                <?php endif; ?>
                <a href="control-user.php?action=delete&user_id=<?php echo $user['id']; ?>">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>