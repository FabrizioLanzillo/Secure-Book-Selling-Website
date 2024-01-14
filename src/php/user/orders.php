<?php
require_once __DIR__ . "/../../config.php";

global $logger;
global $sessionHandler;
global $accessControlManager;

// Checks if the user is logged
if ($sessionHandler->isLogged()) {
    $result = getUserOrders($_SESSION['userId']);
    $logger->writeLog('INFO', "user " . $_SESSION['userId'] . " requested his orders");
} else {
    $accessControlManager->redirectToHome();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="./../../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <title>Book Selling - Orders</title>
</head>
<body>

<?php
include "./../layout/header.php";
?>

<div class="container bg-secondary mt-4 p-4">
    <h1 class="text-white">Your Orders</h1>

    <?php
    if ($result) {
        ?>
        <table class="table table-light table-striped mt-4">
            <thead>
            <tr>
                <th class="text-center align-middle px-0">Time</th>
                <th class="text-center align-middle px-0">Amount</th>
                <th class="text-center align-middle px-0">Payment Method</th>
                <th class="text-center align-middle px-0">Book</th>
                <th class="text-center align-middle px-0">Quantity</th>
                <th class="text-center align-middle px-0">E-book</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $previousTime = null;
            // check if the query returned a result and more than 1 row
            if ($result->num_rows >= 1) {
                while ($order = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <?php
                        // Display orders by time, amount and payment method
                        if ($order['time'] !== $previousTime) {
                            ?>
                            <td class="text-center align-middle px-0"><?php echo htmlspecialchars($order['time']); ?></td>
                            <td class="text-center align-middle px-0"><?php echo htmlspecialchars($order['amount']); ?></td>
                            <td class="text-center align-middle px-0"><?php echo htmlspecialchars($order['payment_method']); ?></td>

                            <?php
                            $previousTime = $order['time'];
                        } else {
                            ?>
                            <td></td>
                            <td></td>
                            <td></td>
                            <?php
                        }
                        ?>
                        <td class="align-middle px-0"><?php echo htmlspecialchars($order['title']); ?></td>
                        <td class="text-center align-middle px-0"><?php echo htmlspecialchars($order['quantity']); ?></td>
                        <td class="text-center align-middle px-0">
                            <form action="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/util/downloadEbook.php'); ?>"
                                  method="POST">
                                <input type="hidden" name="id_book"
                                       value="<?php echo htmlspecialchars($order['id_book']); ?>">
                                <!-- Hidden token to protect against XSRF -->
                                <input type="hidden" name="token"
                                       value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">
                                <button type="submit" class="btn btn-secondary btn-sm ml-1"><i
                                            class="fas fa-download"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <div class='alert alert-danger mt-4'>No orders found.</div>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php
    } else {
        ?>
        <div class='alert alert-danger mt-4'>Error retrieving orders details</div>
        <?php
    }
    ?>
</div>
</body>
</html>
