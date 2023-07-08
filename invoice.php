<?php
include ('./admin/db.php');

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

<!--Your HTML code here... -->

<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_xclick">
    <input type="hidden" name="business" value="sb-qf4aa26227839@business.example.com">
    <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>">
    <input type="hidden" name="item_number" value="<?php echo $product_id; ?>">
    <input type="hidden" name="amount" value="<?php echo $product['price']; ?>">
    <input type="hidden" name="currency_code" value="USD">
    <input type="hidden" name="return" value="https://iptvsmartersproo.com/invoice.php">
    <input type="hidden" name="cancel_return" value="https://iptvsmartersproo.com/cancel.php">
    <input type="hidden" name="notify_url" value="https://iptvsmartersproo.com/ipn_listener.php">
    <input type="image" src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-large.png" border="0"
        name="submit" alt="PayPal - The safer, easier way to pay online!">
</form>

<!--Your HTML code here... -->

<?php include 'payment_gtw.php'; ?>
</body>

</html>