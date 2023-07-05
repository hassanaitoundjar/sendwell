<?php
// Include config file
include ('../admin/db.php');
require ('../vendor/autoload.php');
include ('../mail/mailer.php');
// Check if the user is not logged in, redirect to the login page with an error message
include('./admin/include/checker-uesr.php');

session_start();


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Function to sanitize input data
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function sendAdminEmail($admin_email, $admin_name, $customer_email, $customer_name, $whatsapp_number, $transaction_id, $product, $order_id) {
    $config = include './mail/mailer.php';
    $email_config = $config['email'];
    $mail = new PHPMailer(true);

    try {
        // Configure your email settings
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = $email_config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $email_config['username'];
        $mail->Password = $email_config['password'];
        $mail->SMTPSecure = 'tls';
        $mail->Port = $email_config['port'];

        // Set email sender and recipient using the configuration values
        $mail->setFrom($email_config['admin_email'], $email_config['admin_name']);
        $mail->addAddress($email_config['admin_email'], $email_config['admin_name']);

        // Set email subject and body
        $mail->isHTML(true);
        $mail->Subject = 'Order Confirmation';

        $mail->Body = "
            <h2>Order Confirmation</h2>
            <h4>Order ID: {$order_id}</h4>
            <h4>Transaction ID: {$transaction_id}</h4>
            <h4>Customer Details</h4>
            <strong>{$customer_name}</strong><br>
            Email: {$customer_email}<br>
            WhatsApp Number: {$whatsapp_number}
            <h4>Product Details</h4>
            <table>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                </tr>
                <tr>
                    <td>{$product['name']}</td>
                    <td>{$product['currency']} {$product['price']}</td>
                </tr>
            </table>
            <h4>Total Amount:</h4>
            {$product['currency']} {$product['price']}
            <h4>Please process the order.</h4>
        ";

        // Send the email
        $mail->send();
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}

function sendInvoiceEmail($email, $name, $product, $order_id) {
    $config = include '../mail/mailer.php';
    $email_config = $config['email'];
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Configure your email settings
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = $email_config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $email_config['username'];
        $mail->Password = $email_config['password'];
        $mail->SMTPSecure = 'tls';
        $mail->Port = $email_config['port'];

        // Set email sender and recipient
        $mail->setFrom($email_config['admin_email'], $email_config['admin_name']);
        $mail->addAddress($email, $name);

        // Set email subject and body
   
        $mail->isHTML(true);
        $mail->Subject = 'Thank you for your purchase';

        $mail->Body = "
            <h2>Thank You</h2>
            <h4>Order ID: {$order_id}</h4>
            <h4>Customer Details</h4>
            <strong>{$name}</strong><br>
            Email: {$email}
            <h4>Product Details</h4>
            <table>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                </tr>
                <tr>
                    <td>{$product['name']}</td>
                    <td>{$product['currency']} {$product['price']}</td>
                </tr>
            </table>
            <h4>Total Amount:</h4>
            {$product['currency']} {$product['price']}
            <h4>Your order has been received and is being processed.</h4>
            <p>Click here to download your invoice: <a href='https://yourwebsite.com/track_email_open.php?customer_email=" . urlencode($email) . "'>Download Invoice</a></p>

        ";

        // Send the email
        $mail->send();
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}
if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['product_id'])) {
    $name = test_input($_POST['name']);
    $email = test_input($_POST['email']);
    $area_code = $_POST['area_code'];
    $whatsapp_number = test_input($area_code . $_POST['whatsapp_number']);
    $product_id = test_input($_POST['product_id']);
    $adult = $_POST['adult'];
    $channels_selected = implode(',', $_POST['channels']);
    $user_id = $_SESSION['user_id'];
    

    // Retrieve price from the 'products' table
    $stmt = mysqli_prepare($link, "SELECT * FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $product = mysqli_fetch_assoc($result);
        $price = $product['price'];
        $product_name = $product['name']; // Get the product name


        $transaction_id = uniqid('TRANS_', true);
        // Insert order details into the 'orders' table
        $order_id = sprintf('#01%08d', rand(1, 99999999));
        $stmt = mysqli_prepare($link, "INSERT INTO orders (name, email, whatsapp_number, product_id, product_name, price, currency, transaction_id, order_id, adult, channels, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssssssssssi", $name, $email, $whatsapp_number, $product_id, $product_name, $price, $product['currency'], $transaction_id, $order_id, $adult, $channels_selected, $user_id);

        $config = include '../mail/mailer.php';
        $email_config = $config['email'];
        // Use the admin email and name from the configuration file
        $admin_email = $email_config['admin_email'];
        $admin_name = $email_config['admin_name'];
        // $whatsAppNumber = 'your_customer_whatsapp_number'; // Replace with the customer's WhatsApp number

        sendInvoiceEmail($email, $name, $product, $order_id);
        sendAdminEmail($admin_email, $admin_name, $email, $name, $whatsapp_number, $transaction_id, $product, $order_id);
        // sendInvoiceWhatsApp($whatsAppNumber, $name, $product, $order_id); // Add this line
        mysqli_stmt_execute($stmt);
    } else {
        header("Location: list_product.php");
        exit();
    }
} else {
    header("Location: list_product.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-xs-6">
                <h4>Order ID:</h4>
                <p><?php echo htmlspecialchars($order_id, ENT_QUOTES); ?></p>
            </div>
            <div class="col-xs-6">
                <h4>Transaction ID:</h4>
                <p><?php echo htmlspecialchars($transaction_id, ENT_QUOTES); ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <h2 class="text-center">Invoice</h2>
                <hr>
                <h4>Customer Details</h4>
                <address>
                    <strong><?php echo htmlspecialchars($name, ENT_QUOTES); ?></strong><br>
                    Email: <?php echo htmlspecialchars($email, ENT_QUOTES); ?><br>

                </address>
                <hr>
                <h4>Product Details</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?></td>
                            <td><?php echo htmlspecialchars($product['currency'] . " " . $product['price'], ENT_QUOTES); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <h4>Total Amount:</h4>
                <p class="lead">
                    <?php echo htmlspecialchars($product['currency'] . " " . $product['price'], ENT_QUOTES); ?></p>
                <h4>Thank you for your purchase!</h4>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>

</html>