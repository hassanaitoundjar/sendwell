<?php
require 'send_email.php';
include ('../admin/db.php');


// Secure session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();
$user_id = $_SESSION['user_id'];

$message = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $to = filter_var($_POST['to'], FILTER_SANITIZE_EMAIL);
    $subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
    $body = filter_var($_POST['body'], FILTER_SANITIZE_STRING);

    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        $message = 'Invalid email address';
    } elseif (send_email($to, $subject, $body)) {
        $message = 'Message sent successfully';

        // Save the sent message with the user ID
        $stmt = $link->prepare("INSERT INTO sent_messages (user_id, to_email, subject, body, sent_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("isss", $user_id, $to, $subject, $body);
        $stmt->execute();
        $stmt->close();

    } else {
        $message = 'Error sending message';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy"
        content="default-src 'self'; script-src 'self' https://cdn.ckeditor.com; style-src 'self' 'unsafe-inline'; img-src 'self';">
    <title>Send Email</title>
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
</head>

<body>
    <h1>Send Email</h1>
    <?php if ($message): ?>
    <p><?= $message ?></p>
    <?php endif; ?>
    <form action="" method="POST">
        <label for="to">To:</label>
        <input type="email" name="to" id="to" required>
        <br>
        <label for="subject">Subject:</label>
        <input type="text" name="subject" id="subject" required>
        <br>
        <label for="body">Message:</label>
        <textarea name="body" id="body" rows="10" required></textarea>
        <br>
        <button type="submit">Send</button>
    </form>
    <script>
    CKEDITOR.replace('body');
    </script>
</body>

</html>