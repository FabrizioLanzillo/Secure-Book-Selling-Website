<?php
require_once __DIR__ . "/config.php";

global $errorHandler;

// Check if the search form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search_query"])) {

    // Protect against XSS
    $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');
    $searchQuery = htmlspecialchars($_POST["search_query"], ENT_QUOTES, 'UTF-8');

    if (!$token || $token !== $_SESSION['token']) {
        // return 405 http status code
        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
        exit;
    } else {
        $resultQuery = searchBooks($searchQuery);
    }
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
        <form name="search" action="//<?php echo htmlspecialchars(SERVER_ROOT . '/');?>" method="POST">
            <label>
                <input class="search_form_input" type="text" name="search_query" placeholder="Enter book name" required>
            </label>
            <!-- Hidden token to protect against CSRF -->
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">
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
                        <img src="/img/books/<?php echo htmlspecialchars($book['id']);?>.jpg" alt="Book Image" style="width: 100%; height: auto;"> <br>
                        <?php
                        foreach ($book as $key => $value) {
                            if ($key != 'id')
                                echo htmlspecialchars($key . ": " . $value) . "<br>";
                        }
                        ?>
                        <a href="//<?php echo htmlspecialchars(SERVER_ROOT. '/php/book_details.php?book_id='. $book['id']);?>">
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