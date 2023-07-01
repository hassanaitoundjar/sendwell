<?php
include ('../admin/db.php');

// Function to sanitize input data
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Retrieve product information based on the product ID in the URL
if (isset($_GET["product"])) {
    $product_id = test_input($_GET["product"]);

    // Prepare the SQL statement with a parameterized query to prevent SQL injection attacks
    $stmt = mysqli_prepare($link, "SELECT * FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $product = mysqli_fetch_assoc($result);
    } else {
        header("Location: products.php");
        exit();
    }
} else {
    header("Location: products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container">
        <h2>Checkout</h2>
        <h4>Product: <?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?></h4>
        <h4>Price: <?php echo htmlspecialchars($product['currency'] . " " . $product['price'], ENT_QUOTES); ?></h4>
        <form method="POST" action="invoice.php">
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name"
                    required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email"
                    required>
            </div>
            <label for="whatsapp_number">Whatsapp Number:</label>
            <div class="input-group">
                <select class="custom-select" id="area_code" name="area_code" required>
                    <option value="">Area code</option>
                    <option value="+1">+1 (United States)</option>
                    <option value="+44">+44 (United Kingdom)</option>
                    <!-- Add more area codes as needed -->
                </select>
                <input type="number" class="form-control" id="whatsapp_number" name="whatsapp_number"
                    placeholder="Enter your whatsapp number" required pattern="\d{7,15}"
                    title="Please enter a valid phone number with 7-15 digits">
            </div>

            <div class="form-group">
                <label for="adult">Select option:</label>
                <select class="custom-select" id="adult" name="adult" required>
                    <option value="">adult;;;</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </div>
            <div class="form-group">
                <label>Select channels:</label>
                <div>
                    <input type="checkbox" id="channel_maroc" name="channels[]" value="maroc" required>
                    <label for="channel_maroc">Maroc</label>
                </div>
                <div>
                    <input type="checkbox" id="channel_usa" name="channels[]" value="usa">
                    <label for="channel_usa">USA</label>
                </div>
                <div>
                    <input type="checkbox" id="channel_france" name="channels[]" value="france" required>
                    <label for="channel_france">france</label>
                </div>
                <div>
                    <input type="checkbox" id="channel_usa" name="channels[]" value="usa">
                    <label for="channel_usa">USA</label>
                </div>
                <!-- Add more channels as needed -->
            </div>

            <button type="submit" class="btn btn-danger mt-1 ">Place Order</button>
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
        </form>

        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="business" value="stephencox@sky.com">
            <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>">
            <input type="hidden" name="item_number" value="<?php echo $product_id; ?>">
            <input type="hidden" name="amount" value="<?php echo $product['price']; ?>">
            <input type="hidden" name="currency_code" value="USD">
            <input type="hidden" name="return" value="http://yourwebsite.com/thankyou.php">
            <input type="hidden" name="cancel_return" value="http://yourwebsite.com/cancel.php">
            <input type="hidden" name="notify_url" value="http://yourwebsite.com/ipn_listener.php">
            <input type="image" src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-large.png"
                border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
        </form>

        <form action="./payment_process.php" method="POST">

            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <input type="hidden" name="product_name"
                value="<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>">
            <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
            <input type="hidden" name="product_currency" value="<?php echo $product['currency']; ?>">

        </form>


    </div>

    <?php include 'payment_gtw.php'; ?>
</body>

</html>