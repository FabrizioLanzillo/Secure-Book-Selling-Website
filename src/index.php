<?php
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/php/util/dbInteraction.php";

global $errorHandler;

// Check if the search form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search_query"])) {
    $searchQuery = $_POST["search_query"];
    $resultQuery = searchBooks($searchQuery);
} else {
    $resultQuery = getBooks();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="./css/home.css">
        <title>Book Selling - Home</title>
    </head>
    <body>

    <?php
        include "./php/layout/header.php";
    ?>

    <div class="search_container">
        <form name="search" action="//<?php echo SERVER_ROOT . '/'?>" method="POST">
            <label>
                <input class="search_form_input" type="text" name="search_query" placeholder="Enter book name" required>
            </label>
            <button class="search_button" type="submit">Search</button>
        </form>
    </div>

    <div class="book_grid">
        <?php
        try{
            if ($resultQuery) {
                while ($book = $resultQuery->fetch_assoc()) {
                    // Output each book as a card in the grid
            ?>
                    <div class="book_card">
                        <img src="/img/book.png" alt="Book Image"> <br>
                        <?php
                        foreach ($book as $key => $value) {
                            if ($key != 'id')
                                echo $key . ": " . $value . "<br>";
                        }
                        ?>
                        <a href="//<?php echo SERVER_ROOT. '/php/book_details.php?book_id='. $book['id']?>">
                            <button class="view_details_button">
                                Details
                            </button>
                        </a>
                    </div>
            <?php
                }
            }
            else {
                throw new Exception('Error retrieving books data');
            }
        } catch (Exception $e){
            $errorHandler->handleException($e);
        }
        ?>
    </div>

    </body>
</html>