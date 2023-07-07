<?php
// Step 1: Get the IPN data from the PayPal POST request
$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$ipn_data = array();

foreach ($raw_post_array as $keyval) {
    $keyval = explode('=', $keyval);
    if (count($keyval) == 2) {
        $ipn_data[$keyval[0]] = urldecode($keyval[1]);
    }
}

// Step 2: Send the IPN data back to PayPal for verification
$paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr'; // Sandbox URL for testing
$ch = curl_init($paypal_url);

curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw_post_data);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

if (!($response = curl_exec($ch))) {
    // cURL error occurred
    error_log("Failed to receive IPN data from PayPal: " . curl_error($ch));
    curl_close($ch);
    exit();
}

curl_close($ch);

// Step 3: Verify the IPN response
if (strcmp($response, 'VERIFIED') === 0) {
    // IPN verification successful
    // Process the IPN data and update your database or perform any necessary actions

    // Example: Log IPN data to a file
    $log_file = 'ipn_logs.txt';
    $ipn_log = date('Y-m-d H:i:s') . " - IPN data: " . print_r($ipn_data, true) . "\n";
    file_put_contents($log_file, $ipn_log, FILE_APPEND | LOCK_EX);
} else {
    // IPN verification failed
    // Log the error for further investigation
    error_log("IPN verification failed: $response");
}
?>