<?php

if (isset($_GET['seen'])) {
    $_SESSION['seen'] = true;
}

$query = "SELECT * FROM orders";
$result = $link->query($query);
$customer_data = [];
$customer_count = mysqli_num_rows($result);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $customer_data[] = $row;
    }
} else {
    echo "No customer data found.";
}

$link->close();


?>