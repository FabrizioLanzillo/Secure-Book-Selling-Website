<?php
require_once __DIR__ . "/../config.php";

global $shoppingCartHandler;
global $errorHandler;
global $accessControlManager;

// Sanitize user input
$bookId = isset($_GET['book_id']) ? htmlspecialchars($_GET['book_id'], ENT_QUOTES, 'UTF-8') : null;
$resultQuery = getBookDetails($bookId);

try{
    if (isset($_POST['bookId'])) {
        // Protect against XSS
        $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');
        $book = htmlspecialchars($_POST['bookId'], ENT_QUOTES, 'UTF-8');
        $quantity = htmlspecialchars($_POST['quantity'], ENT_QUOTES, 'UTF-8');

        if (!$token || $token !== $_SESSION['token']) {
            // return 405 http status code
            $accessControlManager ->redirectIfXSRFAttack();
        } else {
            if($shoppingCartHandler->addItem($book, $quantity)){       /*aggiunta la nuova post qui*/
                showInfoMessage("Book Successfully added to the shopping cart!");
            }
        }
    }
}
catch (Exception $e) {
    $errorHandler->handleException($e);
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
<!--        <link rel="stylesheet" type="text/css" href="../css/book_details.css">-->
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
        <title>Book Selling - Book Details</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    </head>
    <body>

        <?php
        include "./layout/header.php";
        ?>

        <div class="container mt-5">
            <?php
                if ($resultQuery) {
                $bookDetails = $resultQuery->fetch_assoc();
                if ($bookDetails) {
            ?>
            <h1 class="mb-4">Book Details</h1>
            <div class="card">
                <div class="row g-0 d-flex justify-content-center p-4">
                    <div class="col-md-4">
                        <!--                        <img src="../img/front_book.jpg" alt="Book Image" class="img-fluid">-->
<!--                        <img src="../img/bookImages/img---><?php //echo rand(1, 20); ?><!--.jpg" class="img-thumbnail w-75" alt="Book Image">-->
                        <img src="../img/books/<?php echo htmlspecialchars($bookId); ?>.jpg" alt="Book Image" class="img-thumbnail w-75" >
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($bookDetails['title']); ?></h5>
                            <p class="card-text"><strong>Author:</strong> <?php echo htmlspecialchars($bookDetails['author']); ?></p>
                            <p class="card-text"><strong>Publisher:</strong> <?php echo htmlspecialchars($bookDetails['publisher']); ?></p>
                            <p class="card-text"><strong>Price:</strong> $<?php echo htmlspecialchars($bookDetails['price']); ?></p>
                            <p class="card-text"><strong>Genre:</strong> <?php echo htmlspecialchars($bookDetails['category']); ?></p>
                            <p class="card-text"><strong>In stock:</strong> <?php echo htmlspecialchars($bookDetails['stocks_number']); ?></p>

                            <div class="mb-4">
                                <form action="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/book_details.php?book_id=' . $bookId) ?>" method="POST" class="d-inline">
                                    <div class="d-flex flex-row">
                                        <input type="hidden" name="bookId" value="<?php echo htmlspecialchars($bookId); ?>">
                                        <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="<?php echo htmlspecialchars( $bookDetails['stocks_number']); ?>" style="max-width: 5rem;">
                                        <!-- Hidden token to protect against CSRF -->
                                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">
                                        <button type="submit" class="btn btn-primary ml-3">Add to Cart</button>
                                    </div>

                                </form>
                            </div>

                            <a href="../" class="btn btn-secondary">Back to Home</a>
                        </div>
                    </div>
                </div>
            </div>

        <?php
        } else {
            ?>
            <div class='alert alert-danger mt-4'>Book not found in the database</div>
            <?php
        }
        } else {
            ?>
            <div class='alert alert-danger mt-4'>Error retrieving book details</div>
            <?php
        }
        ?>
        </div>
    </body>
</html>