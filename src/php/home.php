<?php
require_once __DIR__ . "./../config.php";
require_once __DIR__ . "/util/dbInteraction.php";

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
    <link rel="stylesheet" type="text/css" href="../css/home.css">
    <title>Book Selling - Home</title>
</head>
<body>

    <?php
    include "./layout/header.php";
    if (isLogged()) {
        echo "<b>Ciao:" . $_SESSION['name'] . "</b><br>";}
    ?>

    <div class="search_container">
        <form name="search" action="//<?php echo SERVER_ROOT . '/php/home.php'?>" method="POST">
            <label>
                <input class="search_form_input" type="text" name="search_query" placeholder="Enter book name" required>
            </label>
            <button class="search_button" type="submit">Search</button>
        </form>
    </div>

    <div class="book_grid">
        <?php
        if ($resultQuery) {
            while ($book = $resultQuery->fetch_assoc()) {
                // Output each book as a card in the grid
                echo '<div class="book_card">';
                echo '<img src="../img/book.png" alt="Book Image"> <br>';
                foreach ($book as $key => $value) {
                    if ($key != 'id')
                        echo $key . ": " . $value . "<br>";
                }
                echo '<a href="book_details.php?book_id=' . $book['id'] . '" class="view_details_button">Details</a>';
                echo "</div>";
            }
        } else {
            echo "<script>alert('Error retrieving books data');</script>";
        }
        ?>
    </div>

</body>
</html>