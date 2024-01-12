<?php
require_once __DIR__ . "/../../config.php";

global $sessionHandler;

if ($sessionHandler->isLogged()) {
    $performedOrders = getUserOrders($_SESSION['userId']);
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
<!--        <link rel="stylesheet" type="text/css" href="./../../css/orders.css">-->
        <link rel="stylesheet" type="text/css" href="./../../css/bootstrap.min.css">
        <title>Book Selling - Orders</title>
    </head>
    <body>

    <?php
    include "./../layout/header.php";

    if ($sessionHandler->isLogged()) {
    ?>

    <div class="container bg-secondary mt-4 p-4">

        <h1 class="text-white">Your Orders</h1>

        <?php
        if ($performedOrders) {
        ?>
        <table class="table table-light table-striped mt-4">
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Time</th>
                    <th>Amount</th>
                    <th>Quantity</th>
                    <th>Payment Method</th>
                </tr>
            </thead>
            <tbody>
            <?php
                //FARE GRAFICA X QUESTO
                $ordersByTime = array();

                while ($order = $performedOrders->fetch_assoc()) {
                    $time = $order['time'];
                    $title = htmlspecialchars($order['title']);

                    // Check if the time is already in the array
                    if (array_key_exists($time, $ordersByTime)) {
                        // If yes, add the title to the existing array
                        $ordersByTime[$time][] = $title;
                    } else {
                        // If no, create a new array with the title
                        $ordersByTime[$time] = array($title);
                    }
                }
                // Display the titles in an array for each time
                foreach ($ordersByTime as $time => $titles) {
                    echo '<p>Orders at ' . htmlspecialchars($time) . ': ' . implode(', ', $titles) . '</p>';
                }
                while ($order = $performedOrders->fetch_assoc()) {
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['title']); ?></td>
                    <td><?php echo htmlspecialchars($order['time']); ?></td>
                    <td><?php echo htmlspecialchars($order['amount']); ?></td>
                    <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                </tr>

            </tbody>
            <?php } ?>
        </table>
    <?php
        }
        else {
    ?>
        <p>No orders found.</p>
    <?php
        }}
        else {
    ?>
        <div class='error-message'>No user logged</div>
    <?php
        }
    ?>

    </div>
    </body>
</html>
