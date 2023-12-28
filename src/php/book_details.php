<?php
require_once __DIR__ . "./../config.php";
require_once __DIR__ . "/util/dbInteraction.php";

$bookId = isset($_GET['book_id']) ? $_GET['book_id'] : null;
$cartAdded = isset($_GET['cart_added']) ? $_GET['cart_added'] : null;
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
        require_once __DIR__ . "./../config.php";
        require_once __DIR__ . "/util/dbInteraction.php";

        if ($resultQuery) {
            $bookDetails = $resultQuery->fetch_assoc();
            if ($bookDetails) {
                echo '<h1>Book Details</h1>';
                echo '<div class="book-image"><img src="../img/book.png"></div>';
                echo '<div class="detail-item"><strong>Title:</strong> ' . $bookDetails['title'] . '</div>';
                echo '<div class="detail-item"><strong>Author:</strong> ' . $bookDetails['author'] . '</div>';
                echo '<div class="detail-item"><strong>Publisher:</strong> ' . $bookDetails['publisher'] . '</div>';
                echo '<div class="detail-item"><strong>Price:</strong> $' . $bookDetails['price'] . '</div>';
                echo '<div class="detail-item"><strong>Genre:</strong> ' . $bookDetails['category'] . '</div>';
                echo '<div class="detail-item"><strong>In stock:</strong> ' . $bookDetails['stocks_number'] . '</div>';

                echo '<a href="../index.php" class="back-button">Back to Home</a>';
                echo '<a href="#" class="add-button" onclick="addToCart()">Add to Cart</a>';
                    
            } else {
                echo "<div class='error-message'>Error retrieving book details</div>";
            }
        } else {
            echo "<div class='error-message'>Invalid book ID</div>";
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