<?php
require_once __DIR__ . "/../../config.php";

global $sessionHandler;
global $shoppingCartHandler;
global $errorHandler;
global $accessControlManager;

$items = $shoppingCartHandler->getBooks();
$totalPrice = 0;

if (isset($_POST['itemId'])){
    // Protect against XSS
    $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');
    $bookId = htmlspecialchars($_POST['itemId'], ENT_QUOTES, 'UTF-8');

    if (!$token || $token !== $_SESSION['token']) {
        // return 405 http status code
        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
        exit;
    } else {
        try{
            if($shoppingCartHandler->removeItem($bookId)){
                header('Location: //' . SERVER_ROOT . '/php/user/shoppingCart.php');
                exit;
            }
        }
        catch (Exception $e) {
            $errorHandler->handleException($e);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="../../css/shoppingCart.css">
        <title>Book Selling - Shopping Cart</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSS di Bootstrap -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php
        include "./../layout/header.php";
        ?>

        <div class="container px-3 my-5 clearfix">
            <div class="card">
                <div class="card-header">
                    <h2>Shopping Cart</h2>
                </div>
                <div class="card-body">
                    <?php
                    if ($items !== null) {
                        ?>
                        <div class="table-responsive">
                            <table class="table table-bordered m-0">
                                <thead>

                                    <tr>
                                        <!-- Set columns width -->
                                        <th class="text-center py-3 px-4" style="min-width: 400px;">Books & Details</th>
                                        <th class="text-center py-3 px-4" style="width: 100px;">Price</th>
                                        <th class="text-center py-3 px-4" style="width: 120px;">Quantity</th>
                                        <th class="text-center py-3 px-4" style="width: 100px;">Total</th>
                                        <th class="text-center align-middle py-3 px-0" style="width: 40px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($items as $itemId => $itemDetails) {
                                    $totalPrice += $itemDetails['price'] * $itemDetails['quantity'];
                                    ?>
                                    <tr>
                                        <td class="p-4">
                                            <div class="media align-items-center">
                                                <img src="../../img/books/<?php echo htmlspecialchars( $itemId);?>.jpg" class="d-block ui-w-40 ui-bordered mr-4" alt="Book Image">
                                                <div class="media-body">
                                                    <a href="#" class="d-block text-dark"><?= htmlspecialchars($itemDetails['title']) ?></a>
                                                    <small>
                                                        <span class="text-muted">Author: </span> <?= htmlspecialchars($itemDetails['author']) ?> &nbsp;
                                                        <span class="text-muted">Publisher: </span> <?= htmlspecialchars($itemDetails['publisher']) ?> &nbsp;
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center font-weight-semibold align-middle p-4">$<?= htmlspecialchars($itemDetails['price']) ?></td>
                                        <td class="text-center font-weight-semibold align-middle p-4"><?= htmlspecialchars($itemDetails['quantity']) ?></td>
                                        <td class="text-center font-weight-semibold align-middle p-4">$<?= htmlspecialchars($itemDetails['price'] * $itemDetails['quantity']) ?></td>
                                        <td class="text-center align-middle px-0">
                                            <form action="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/user/shoppingCart.php'); ?>" method="POST">
                                                <input type="hidden" name="itemId" value="<?php echo htmlspecialchars($itemId); ?>">
                                                <!-- Hidden token to protect against CSRF -->
                                                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">
                                                <button class="btn btn-danger btn-sm ml-1"><i class="fas fa-trash">×</i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="text-right mt-4 mr-1">
                            <label class="text-muted font-weight-normal m-0">Total price</label>
                            <div class="text-large"><strong>$<?= $totalPrice ?></strong></div>
                            <br>
                        </div>

                        <?php
                    }
                    else {
                        ?>
                        <h4>No items to show in the shopping cart</h4>
                        <?php
                    }
                    ?>

                    <div class="float-right">
                        <a href="../../" class="btn btn-lg btn-default md-btn-flat mt-2 mr-3">Back to shopping</a>
                        <?php
                        if ($items !== null) {
                            if ($sessionHandler->isLogged()) {
                                $pathNextStepToCheckout = $accessControlManager->getNextStepToCheckout();
                                ?>
                                <a href="//<?php echo htmlspecialchars($pathNextStepToCheckout); ?>"
                                   class="btn btn-lg btn-primary mt-2">Checkout</a>
                                <?php
                            }
                            else {
                                ?>
                                <a href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/user/paymentPerformed.php');?>" class="btn btn-lg btn-primary mt-2">Checkout</a>
                                <?php
                            }
                        }
                        ?>

                    </div>
                </div>
            </div>
        </div>
    </body>
</html>