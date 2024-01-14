<?php
require_once __DIR__ . "/../../config.php";

global $sessionHandler;
global $accessControlManager;

// check path manipulation
// broken access control
if ($sessionHandler->isLogged() and $sessionHandler->isAdmin()) {
    $result = getAllOrdersData();
} else {
    $accessControlManager->redirectToHome();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>

    <title>Book Selling - Home Admin</title>
</head>
<body>
<?php
include "./../layout/header.php";
?>

<div class="d-flex">

    <aside class="d-flex flex-column flex-shrink-0 p-3 bg-light" style="width: 20rem;">
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/admin/homeAdmin.php'); ?>"
                   class="nav-link link-dark">
                    <i class="fas fa-book"></i>
                    Books
                </a>
            </li>
            <li>
                <a href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/admin/orderList.php'); ?>"
                   class="nav-link active" aria-current="page">
                    <i class="fas fa-list"></i>
                    Orders
                </a>
            </li>
            <li>
                <a href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/admin/customerList.php'); ?>"
                   class="nav-link link-dark">
                    <i class="fas fa-users"></i>
                    Customers
                </a>
            </li>
            <li class="nav-item">
                <a href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/profile.php'); ?>"
                   class="nav-link link-dark">
                    <i class="fas fa-user"></i>
                    Admin
                </a>
            </li>
        </ul>
        <hr>
    </aside>

    <main class="container bg-secondary mt-4 p-4">

        <h1 class="text-white">Orders</h1>

        <?php
        if ($result) {
            ?>
            <table class="table table-light table-striped mt-4">
                <thead>
                <tr>
                    <th class="text-center align-middle px-0">Username</th>
                    <th class="text-center align-middle px-0">Time</th>
                    <th class="text-center align-middle px-0">Amount</th>
                    <th class="text-center align-middle px-0">Payment Method</th>
                    <th class="text-center align-middle px-0">Book</th>
                    <th class="text-center align-middle px-0">Quantity</th>
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
                            if ($order['time'] !== $previousTime) {
                                ?>
                                <td class="text-center align-middle px-0"><?php echo htmlspecialchars($order['username']); ?></td>
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
                                <td></td>
                                <?php
                            }
                            ?>
                            <td class="align-middle px-0"><?php echo htmlspecialchars($order['title']); ?></td>
                            <td class="text-center align-middle px-0"><?php echo htmlspecialchars($order['quantity']); ?></td>
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
    </main>
</div>
</body>
</html>