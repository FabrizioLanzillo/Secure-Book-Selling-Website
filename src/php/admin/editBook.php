<?php
require_once __DIR__ . "/../../config.php";

global $sessionHandler;
global $logger;
global $errorHandler;
global $accessControlManager;

if ($sessionHandler->isLogged() and $sessionHandler->isAdmin()) {
    $result = false;
    // Sanitize user input
    $bookId = isset($_GET['book_id']) ? htmlspecialchars($_GET['book_id'], ENT_QUOTES, 'UTF-8') : null;
    // retrieve the book that admin want to edit
    if ($bookId !== null) {
        $result = getBookDetails($bookId);
    }

    if (checkFormData(['id', 'title', 'author', 'publisher', 'price', 'category', 'stock'])) {

        // Protect against XSS
        $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');
        $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
        $author = htmlspecialchars($_POST['author'], ENT_QUOTES, 'UTF-8');
        $publisher = htmlspecialchars($_POST['publisher'], ENT_QUOTES, 'UTF-8');
        $price = htmlspecialchars($_POST['price'], ENT_QUOTES, 'UTF-8');
        $category = htmlspecialchars($_POST['category'], ENT_QUOTES, 'UTF-8');
        $stock = htmlspecialchars($_POST['stock'], ENT_QUOTES, 'UTF-8');
        $id = htmlspecialchars($_POST['id'], ENT_QUOTES, 'UTF-8');

        // Protect against XSRF
        if (!$token || $token !== $_SESSION['token']) {
            // return 405 http status code
            $accessControlManager->redirectIfXSRFAttack();
        } else {
            try {
                $book = array(
                    $title,
                    $author,
                    $publisher,
                    $price,
                    $category,
                    $stock,
                    $id,
                );

                // update book information
                if (updateBook($book)) {
                    $logger->writeLog('INFO', "Book: " . $book[0] . "with id= " . $book[6] . " updated");
                    $accessControlManager->redirectToHome();
                } else {
                    throw new Exception('Could not update the book');
                }
            } catch (Exception $e) {
                $errorHandler->handleException($e);
            }
        }
    }
} else {
    $accessControlManager->redirectToHome();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="../../../css/bootstrap.min.css">
    <title>Edit Book</title>
</head>
<body>

<?php
include "./../layout/header.php";
?>

<div class="container bg-light mt-5 w-50">
    <h2>Edit Book</h2>
    <?php
    if ($result) {
        $dataBook = $result->fetch_assoc();
        if ($dataBook !== null && $result->num_rows === 1) {
            ?>
            <form action="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/admin/editBook.php'); ?>" method="post">
                <div class="form-group d-none">
                    <label for="id">Id:</label>
                    <input type="text" class="form-control" id="id" name="id"
                           value="<?php echo htmlspecialchars($dataBook['id']); ?>"
                           required>
                </div>
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="Book"
                           value="<?php echo htmlspecialchars($dataBook['title']); ?>"
                           required>
                </div>
                <div class="form-group">
                    <label for="author">Author:</label>
                    <input type="text" class="form-control" id="author" name="author" placeholder="Bookkin"
                           value="<?php echo htmlspecialchars($dataBook['author']); ?>"
                           required>
                </div>
                <div class="form-group">
                    <label for="publisher">Publisher:</label>
                    <input type="text" class="form-control" id="publisher" name="publisher" placeholder="Book House"
                           value="<?php echo htmlspecialchars($dataBook['publisher']); ?>"
                           required>
                </div>
                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="number" class="form-control" id="price" name="price" step="0.05" placeholder="13.90"
                           value="<?php echo htmlspecialchars($dataBook['price']); ?>"
                           required>
                </div>
                <div class="form-group">
                    <label for="category">Category:</label>
                    <input type="text" class="form-control" id="category" name="category" placeholder="Romance"
                           value="<?php echo htmlspecialchars($dataBook['category']); ?>"
                           required>
                </div>
                <div class="form-group">
                    <label for="stock">Stock:</label>
                    <input type="number" class="form-control" id="stock" name="stock" placeholder="50"
                           value="<?php echo htmlspecialchars($dataBook['stocks_number']); ?>"
                           required>
                </div>

                <!-- Hidden token to protect against XSRF -->
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">

                <button type="submit" class="btn btn-primary">Update</button>
            </form>
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

