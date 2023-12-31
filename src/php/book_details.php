<?php
require_once __DIR__ . "./../config.php";
require_once __DIR__ . "/util/dbInteraction.php";

$bookId = $_GET['book_id'] ?? null;
$cartAdded = $_GET['cart_added'] ?? null;
$resultQuery = getBookDetails($bookId);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="../css/book_details.css">
        <title>Book Selling - Book Details</title>
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
                    <div class="book-image"><img src="../img/book.png"></div>
                    <div class="detail-item"><strong>Title:</strong> <?php echo $bookDetails['title']; ?></div>
                    <div class="detail-item"><strong>Author:</strong> <?php echo $bookDetails['author']; ?></div>
                    <div class="detail-item"><strong>Publisher:</strong> <?php echo $bookDetails['publisher']; ?></div>
                    <div class="detail-item"><strong>Price:</strong> $<?php echo $bookDetails['price']; ?></div>
                    <div class="detail-item"><strong>Genre:</strong> <?php echo $bookDetails['category']; ?></div>
                    <div class="detail-item"><strong>In stock:</strong> <?php echo $bookDetails['stocks_number']; ?></div>

                    <a href="../" class="back-button">Back to Home</a>
                    <a href="#" class="add-button" onclick="addToCart()">Add to Cart</a>

                    <?php
                }
                else {
            ?>
                    <div class='error-message'>Error retrieving book details</div>
            <?php
                }
            }
            else {
            ?>
                <div class='error-message'>Invalid book ID</div>
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