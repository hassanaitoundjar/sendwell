<?php
require_once('coinbase-commerce-php/autoload.php');
use CoinbaseCommerce\ApiClient;
use CoinbaseCommerce\Resources\Charge;

// Set up Coinbase Commerce API credentials
$apiKey = '056cfc8b-6080-40f7-9965-c4892dc6c10c';
$apiVersion = '2018-03-22';
ApiClient::init($apiKey, $apiVersion);

// Retrieve product information from the POST data
$product_id = $_POST['product_id'];
$product_name = $_POST['product_name'];
$product_price = $_POST['product_price'];
$product_currency = $_POST['product_currency'];

// Create a charge
$chargeData = [
    'name' => $product_name,
    'description' => 'Payment for '.$product_name,
    'local_price' => [
        'amount' => $product_price,
        'currency' => $product_currency
    ],
    'pricing_type' => 'fixed_price',
    'metadata' => [
        'product_id' => $product_id
    ]
];

$charge = Charge::create($chargeData);

// Redirect to Coinbase Commerce payment page
header('Location: '.$charge->hosted_url);
exit();
?>