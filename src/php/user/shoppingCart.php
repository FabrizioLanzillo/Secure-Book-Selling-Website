<?php
    require_once __DIR__ . "/../../config.php";

    global $sessionHandler;
    global $shoppingCartHandler;
    global $errorHandler;

    $items = $shoppingCartHandler->getBooks();

    $bookId = $_GET['book_id'] ?? null;
    if($bookId){
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

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="../../css/shoppingCart.css">
        <title>Book Selling - Shopping Cart</title></head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSS di Bootstrap -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
	<body>
<?php
        include "./../layout/header.php";
?>

        <div class="container px-3 my-5 clearfix">
            <!-- Shopping cart table -->
            <div class="card">
                <div class="card-header">
                    <h2>Shopping Cart</h2>
                </div>
                <div class="card-body">
                    <?php
                    if($items !== null){
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
                                    foreach ($items as $itemId => $itemDetails){
                                    ?>
                                        <tr>
                                            <td class="p-4">
                                                <div class="media align-items-center">
                                                    <img src="../../img/book.png" class="d-block ui-w-40 ui-bordered mr-4" alt="">
                                                    <div class="media-body">
                                                        <a href="#" class="d-block text-dark"><?= $itemDetails['title'] ?></a>
                                                        <small>
                                                            <span class="text-muted">Author: </span> <?= $itemDetails['author'] ?> &nbsp;
                                                            <span class="text-muted">Publisher: </span> <?= $itemDetails['publisher'] ?> &nbsp;
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center font-weight-semibold align-middle p-4">$<?= $itemDetails['price'] ?></td>
                                            <td class="text-center font-weight-semibold align-middle p-4"><?= $itemDetails['quantity'] ?></td>
                                            <td class="text-center font-weight-semibold align-middle p-4">$<?= $itemDetails['price'] * $itemDetails['quantity'] ?></td>
                                            <td class="text-center align-middle px-0">
                                                <a href="//<?php echo SERVER_ROOT . '/php/user/shoppingCart.php?book_id='.$itemId ?>">
                                                    <button class="btn btn-danger btn-sm ml-1"><i class="fas fa-trash">×</i></button>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>

                        </div>
                        <!-- / Shopping cart table -->

                        <div class="d-flex flex-wrap justify-content-between align-items-center pb-4">
                            <div class="mt-4">
                                <label class="text-muted font-weight-normal">
                                    Choose Shipping Mode
                                    <select class="form-control">
                                        <option>1</option>
                                        <option>2</option>
                                    </select>
                                </label>
                            </div>
                            <div class="d-flex">
                                <div class="text-right mt-4 mr-5">
                                    <label class="text-muted font-weight-normal m-0">Shipping Cost</label>
                                    <div class="text-large"><strong>$20</strong></div>
                                </div>
                                <div class="text-right mt-4">
                                    <label class="text-muted font-weight-normal m-0">Total price</label>
                                    <div class="text-large"><strong>$1164.65</strong></div>
                                </div>
                            </div>
                        </div>

                    <?php
                    }
                    else{
                    ?>
                        <h4>No items to show in the shopping cart</h4>
                    <?php
                    }
                    ?>

                    <div class="float-right">
                        <button type="button" class="btn btn-lg btn-default md-btn-flat mt-2 mr-3">Back to shopping</button>
                        <button type="button" class="btn btn-lg btn-primary mt-2">Checkout</button>
                    </div>

                </div>
            </div>
        </div>
	</body>
</html>