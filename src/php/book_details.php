<?php
require_once __DIR__ . "/../config.php";

global $shoppingCartHandler;
global $errorHandler;

$bookId = $_GET['book_id'] ?? null;
$resultQuery = getBookDetails($bookId);

try{
    if (isset($_POST['bookId'])) {
        if($shoppingCartHandler->addItem($_POST['bookId'], 1)){
            showInfoMessage("Book Successfully added to the shopping cart!");
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
                    <div class="book-image"><img src="../img/book.png" alt="Book_image"></div>
                    <div class="detail-item"><strong>Title:</strong> <?php echo $bookDetails['title']; ?></div>
                    <div class="detail-item"><strong>Author:</strong> <?php echo $bookDetails['author']; ?></div>
                    <div class="detail-item"><strong>Publisher:</strong> <?php echo $bookDetails['publisher']; ?></div>
                    <div class="detail-item"><strong>Price:</strong> $<?php echo $bookDetails['price']; ?></div>
                    <div class="detail-item"><strong>Genre:</strong> <?php echo $bookDetails['category']; ?></div>
                    <div class="detail-item"><strong>In stock:</strong> <?php echo $bookDetails['stocks_number']; ?></div>

                    <a href="../" class="back-button">Back to Home</a>

                    <form action="//<?php echo SERVER_ROOT . '/php/book_details.php?book_id='.$bookId ?>" method="POST">
                        <input type="hidden" name="bookId" value="<?php echo $bookId; ?>">
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