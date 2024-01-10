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
        <!--<link rel="stylesheet" type="text/css" href="./css/home.css">-->
        <link rel="stylesheet" type="text/css" href="./css/bootstrap/bootstrap.min.css">
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

    <div class="container mt-4">
        <div class="row">
        <?php
        try{
            if ($resultQuery) {
                while ($book = $resultQuery->fetch_assoc()) {
                    // Output each book as a card in the grid
            ?>
                    <div class="col-lg-4 mb-4">
                        <div class="card">
                            <a href="//<?php echo SERVER_ROOT. '/php/book_details.php?book_id='. $book['id']?>">
                                <img src="/img/book.png" class="card-img-top" alt="Book Image">
                            </a>    
                            <div class="card-body">
                                <?php
                                foreach ($book as $key => $value) {
                                    if ($key != 'id')
                                        echo $key . ": " . $value . "<br>";
                                }
                                ?>
                                <!--<a href="//<?php echo SERVER_ROOT. '/php/book_details.php?book_id='. $book['id']?>">
                                    <button class="btn btn-primary">
                                        Details
                                    </button>
                                </a>-->
                            </div>
                        </div>
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
    </div>

    </body>
</html>