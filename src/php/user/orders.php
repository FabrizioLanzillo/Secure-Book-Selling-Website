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
        <link rel="stylesheet" type="text/css" href="./../../css/orders.css">
        <title>Book Selling - Orders</title>
    </head>
    <body>

    <?php
    include "./../layout/header.php";

    if ($sessionHandler->isLogged()) {
    ?>
        <h1>Your Orders</h1>
        <?php
        if ($performedOrders) {
        ?>
            <table>
                <tr>
                    <th>Book</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Payment Method</th>
                </tr>

            <?php
            while ($order = $performedOrders->fetch_assoc()) {
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['title']); ?></td>
                    <td><?php echo htmlspecialchars($order['amount']); ?></td>
                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                    <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                </tr>
            <?php
            }
            ?>
            </table>
        <?php
        }
        else {
        ?>
            <p>No orders found.</p>
    <?php
        }
    }
    else {
    ?>
        <div class='error-message'>No user logged</div>
    <?php
    }
    ?>
    </body>
</html>
