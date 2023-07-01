<?php
// This script updates the database connection information.

// Function to update the database connection file.
function get_current_db_config() {
    include 'db.php';

    return [
        'host' => $servername,
        'username' => $username,
        'password' => $password,
        'database' => $dbname
    ];
}

// Function to update the database connection file.
function update_db_config($host, $username, $password, $database) {
    $config_file = 'db_config.php';

    $config_template = "<?php
    \$db_host = '{$host}';
    \$db_username = '{$username}';
    \$db_password = '{$password}';
    \$db_database = '{$database}';
    ?>";

if (file_put_contents($config_file, $config_template) !== false) {
return true;
} else {
return false;
}
}

// Check if the form is submitted and update the database connection information.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$host = $_POST['host'];
$username = $_POST['username'];
$password = $_POST['password'];
$database = $_POST['database'];

if (update_db_config($host, $username, $password, $database)) {
echo "Database connection information updated successfully!";
} else {
echo "Failed to update database connection information.";
}
}

// Get the current database connection information.
$current_config = get_current_db_config();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Update Database Connection Information</title>
</head>

<body>
    <h1>Update Database Connection Information</h1>
    <form action="" method="POST">
        <label for="host">Host:</label>
        <input type="text" id="host" name="host" value="<?php echo htmlspecialchars($current_config['host']); ?>"
            required><br>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username"
            value="<?php echo htmlspecialchars($current_config['username']); ?>" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password"
            value="<?php echo htmlspecialchars($current_config['password']); ?>" required><br>

        <label for="database">Database:</label>
        <input type="text" id="database" name="database"
            value="<?php echo htmlspecialchars($current_config['database']); ?>" required><br>

        <input type="submit" value="Update">
        <p>Current PHP Version: <span><?php echo phpversion(); ?></span></p>
    </form>
</body>

</html>