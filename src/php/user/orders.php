<?php
require_once __DIR__ . "./../../config.php";
require_once __DIR__ . "./../util/dbInteraction.php";

if (isLogged()) {
    $logged = true;
    $performedOrders = getUserOrders($_SESSION['userId']);
} else {
    $logged = false;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="./../../css/orders.css">
    <title>Book Selling - Orders</title>
</head>
<body>

<?php
include "./../layout/header.php";
if ($logged) {
    echo "<b>Ciao:" . $_SESSION['name'] . "</b><br>";

    // Display orders table
    echo '<h1>Your Orders</h1>';
    if ($performedOrders) {
        echo '<table>';
        echo '<tr>';
        echo '<th>Book</th>';
        echo '<th>Amount</th>';
        echo '<th>Status</th>';
        echo '<th>Payment Method</th>';
        echo '</tr>';
        while ($order = $performedOrders->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $order['title'] . '</td>';
            echo '<td>' . $order['amount'] . '</td>';
            echo '<td>' . $order['status'] . '</td>';
            echo '<td>' . $order['payment_method'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<p>No orders found.</p>';
    }
} else {
    echo "<div class='error-message'>No user logged</div>";
}
?>

</body>
</html>
