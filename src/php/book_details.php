<?php
require_once __DIR__ . "/../config.php";

global $shoppingCartHandler;
global $errorHandler;

$bookId = $_GET['book_id'] ?? null;
$resultQuery = getBookDetails($bookId);

try{
    if (isset($_POST['bookId'])) {
        // Protect against XSS
        $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');
        $book = htmlspecialchars($_POST['bookId'], ENT_QUOTES, 'UTF-8');

        if (!$token || $token !== $_SESSION['token']) {
            // return 405 http status code
            header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
            exit;
        } else {
            if($shoppingCartHandler->addItem($book, $_POST['quantity'])){       /*aggiunta la nuova post qui*/
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
        <link rel="stylesheet" type="text/css" href="../css/book_details.css">
        <title>Book Selling - Book Details</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    </head>
    <body>

        <?php
        include "./layout/header.php";
        ?>

        <div class="book_detail">
            <?php
            if ($resultQuery) {
                $bookDetails = $resultQuery->fetch_assoc();
                if ($bookDetails) {
                    ?>
                    <h1>Book Details</h1>
          
                    <div class="book-image">
                        <img src="../img/books/<?php echo htmlspecialchars($bookId); ?>.jpg" alt="Book Image" style="width: 45%; height: auto;">
                    </div>
                    <div class="detail-item"><strong>Title:</strong> <?php echo htmlspecialchars($bookDetails['title']); ?></div>
                    <div class="detail-item"><strong>Author:</strong> <?php echo htmlspecialchars($bookDetails['author']); ?></div>
                    <div class="detail-item"><strong>Publisher:</strong> <?php echo htmlspecialchars($bookDetails['publisher']); ?></div>
                    <div class="detail-item"><strong>Price:</strong> $<?php echo htmlspecialchars($bookDetails['price']); ?></div>
                    <div class="detail-item"><strong>Genre:</strong> <?php echo htmlspecialchars($bookDetails['category']); ?></div>
                    <div class="detail-item"><strong>In stock:</strong> <?php echo htmlspecialchars($bookDetails['stocks_number']); ?></div>

                    <a href="../" class="back-button">Back to Home</a>

                    <form action="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/book_details.php?book_id='.$bookId); ?>" method="POST">
                        <input type="hidden" name="bookId" value="<?php echo $bookId; ?>">
                        <!-- Hidden token to protect against CSRF -->
                        <!-- aggiunto input riga sotto -->
                        <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="<?php echo $bookDetails['stocks_number']; ?>" style="max-width: 5rem;">
                        <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">
                        <button type="submit" class="back-button">Add to Cart</button>
                    </form>

                    <?php
                }
                else {
                    ?>
                    <div class='error-message'>Book not found in the database</div>
                    <?php
                }
            }
            else {
                ?>
                <div class='error-message'>Error retrieving book details</div>
                <?php
            }
            ?>
        </div>
        <script>
            //We need to handle this later
            function addToCart() {
                alert('Item has been added to the cart!');
            }
        </script>


    </body>
</html>