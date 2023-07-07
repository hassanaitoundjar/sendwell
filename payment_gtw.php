<?php
// PayPal payment processing code
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve form data
    $name = test_input($_POST["name"]);
    $email = test_input($_POST["email"]);
    $area_code = test_input($_POST["area_code"]);
    $whatsapp_number = test_input($_POST["whatsapp_number"]);
    $adult = test_input($_POST["adult"]);
    $channels = $_POST["channels"];
    $product_id = test_input($_POST["product_id"]);

    // Validate and process the payment

    // Initialize PayPal payment variables
    $paypal_url = "https://www.paypal.com/cgi-bin/webscr";
    $paypal_email = "your_paypal_email@example.com"; // Replace with your PayPal email

    // Set the PayPal return URL
    $return_url = "http://example.com/return_url.php"; // Replace with your return URL

    // Prepare the PayPal form
    echo '<form action="' . $paypal_url . '" method="post">';
    echo '<input type="hidden" name="business" value="' . $paypal_email . '">';
    echo '<input type="hidden" name="cmd" value="_xclick">';
    echo '<input type="hidden" name="item_name" value="' . htmlspecialchars($product['name'], ENT_QUOTES) . '">';
    echo '<input type="hidden" name="item_number" value="' . $product_id . '">';
    echo '<input type="hidden" name="amount" value="' . $product['price'] . '">';
    echo '<input type="hidden" name="currency_code" value="' . $product['currency'] . '">';
    echo '<input type="hidden" name="return" value="' . $return_url . '">';
    echo '<input type="hidden" name="cancel_return" value="' . $return_url . '">';

    // Add additional form fields
    echo '<input type="hidden" name="custom" value="' . urlencode(serialize($_POST)) . '">';

    // Display the PayPal button
    echo '<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online">';
    echo '</form>';
}
?>