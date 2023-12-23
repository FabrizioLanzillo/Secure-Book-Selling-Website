<?php
require_once __DIR__ . "./../config.php";
require_once __DIR__ . "/util/dbInteraction.php";

// Retrieve the book ID from the URL parameter
$bookId = isset($_GET['book_id']) ? $_GET['book_id'] : null;
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

        // Retrieve the book ID from the URL parameter
        $bookId = isset($_GET['book_id']) ? $_GET['book_id'] : null;
        $resultQuery = getBookDetails($bookId);

        if ($resultQuery) {
            $bookDetails = $resultQuery->fetch_assoc();
            if ($bookDetails) {
                // Display book details
                echo '<h1>Book Details</h1>';
                echo '<div class="detail-item"><strong>Title:</strong> ' . $bookDetails['title'] . '</div>';
                echo '<div class="detail-item"><strong>Author:</strong> ' . $bookDetails['author'] . '</div>';
                echo '<div class="detail-item"><strong>Publisher:</strong> ' . $bookDetails['publisher'] . '</div>';
                echo '<div class="detail-item"><strong>Price:</strong> $' . $bookDetails['price'] . '</div>';
                echo '<div class="detail-item"><strong>Genre:</strong> ' . $bookDetails['category'] . '</div>';
                echo '<div class="detail-item"><strong>In stock:</strong> ' . $bookDetails['stocks_number'] . '</div>';
                // Add more details as needed

                // You can also add a button to go back to the home page or a list of all books
                echo '<a href="../index.php" class="back-button">Back to Home</a>';
            } else {
                echo "<div class='error-message'>Error retrieving book details</div>";
            }
        } else {
            echo "<div class='error-message'>Invalid book ID</div>";
        }
        ?>
    </div>

</body>
</html>