<?php
require_once __DIR__ . "/../config.php";

global $shoppingCartHandler;
global $errorHandler;
global $accessControlManager;
global $logger;
global $sessionHandler;

if($sessionHandler->isLogged()){
    // Check path manipulation and broken access control
    // Check if an admin tries to access this page
    $accessControlManager->redirectIfAdmin();
}

$result = false;
// Sanitize user input
$bookId = isset($_GET['book_id']) ? htmlspecialchars($_GET['book_id'], ENT_QUOTES, 'UTF-8') : null;
if ($bookId !== null) {
    $result = getBookDetails($bookId);
}

try {
    if (checkFormData(['bookId'])) {

        // Protect against XSS
        $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $book = filter_input(INPUT_POST, 'bookId', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Protect against XSRF
        if (!$token || $token !== $_SESSION['token']) {
            // return 405 http status code
            $accessControlManager->redirectIfXSRFAttack();
        } else {
            // Adds n of the specified books to the cart of the user
            if ($shoppingCartHandler->addItem($book, $quantity)) {
                $logger->writeLog('INFO', "Book with id=".$book." Successfully added to the shopping cart");
                showInfoMessage("Book Successfully added to the shopping cart!");
            }
        }
    }
} catch (Exception $e) {
    $errorHandler->handleException($e);
    $logger->writeLog('ERROR',
        "User: " . $_SESSION['email'] . " failed to add a book to its shopping cart");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
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
    // Display book details based on what was retrieved in the db
    if ($result) {
        $bookDetails = $result->fetch_assoc();
        if ($bookDetails !== null && $result->num_rows === 1) {
            ?>
            <h1 class="mb-4 text-center"><?php echo htmlspecialchars($bookDetails['title']); ?></h1>
            <div class="card">
                <div class="row g-0 d-flex justify-content-center p-4">
                    <div class="col-md-4 d-flex justify-content-center">
                        <img src="../img/books/<?php echo htmlspecialchars($bookId); ?>.jpg" alt="Book Image"
                             class="img-thumbnail m-auto">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <p class="card-text text-justify"><strong>Synopsis:</strong>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed condimentum dapibus velit,
                                ac vulputate tellus. Duis vestibulum vel elit vitae consectetur.
                                Curabitur auctor, libero eu tempus ullamcorper, dui ante dictum augue, nec interdum
                                velit justo in nulla. Vivamus id nisi vel justo euismod volutpat.
                                Nullam malesuada lacus at neque malesuada, vel sollicitudin risus bibendum. Integer eu
                                nulla id nisl interdum suscipit. Phasellus vel odio at quam bibendum ultrices id eu
                                elit.
                                Fusce tincidunt, felis vel pharetra laoreet, felis elit dapibus tortor, eu vulputate
                                purus sem eu lacus. Etiam tristique sem nec leo cursus, in varius ex laoreet.
                            </p>
                            <p class="card-text">
                                <strong>Author:</strong> <?php echo htmlspecialchars($bookDetails['author']); ?></p>
                            <p class="card-text">
                                <strong>Publisher:</strong> <?php echo htmlspecialchars($bookDetails['publisher']); ?>
                            </p>
                            <p class="card-text"><strong>Price:</strong>
                                $<?php echo htmlspecialchars($bookDetails['price']); ?></p>
                            <p class="card-text">
                                <strong>Genre:</strong> <?php echo htmlspecialchars($bookDetails['category']); ?></p>
                            <p class="card-text"><strong>In
                                    stock:</strong> <?php echo htmlspecialchars($bookDetails['stocks_number']); ?></p>

                            <div class="mb-4">
                                <form action="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/bookDetails.php?book_id=' . $bookId) ?>"
                                      method="POST" class="d-inline">
                                    <div class="d-flex flex-row">
                                        <input type="hidden" name="bookId"
                                               value="<?php echo htmlspecialchars($bookId); ?>">
                                        <input type="number" class="form-control" id="quantity" name="quantity"
                                               value="1" min="1"
                                               max="<?php echo htmlspecialchars($bookDetails['stocks_number']); ?>"
                                               style="max-width: 5rem;">
                                        <!-- Hidden token to protect against CSRF -->
                                        <input type="hidden" name="token"
                                               value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">
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