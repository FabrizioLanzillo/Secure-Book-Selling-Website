<?php
require_once __DIR__ . "/../../config.php";

global $sessionHandler;
global $logger;
global $errorHandler;
global $accessControlManager;

if ($sessionHandler->isLogged() and $sessionHandler->isAdmin()) {
    if(checkFormData(['title', 'author', 'publisher', 'price', 'category', 'stock'])){
        // Protect against XSS
        $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');
        $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
        $author = htmlspecialchars($_POST['author'], ENT_QUOTES, 'UTF-8');
        $publisher = htmlspecialchars($_POST['publisher'], ENT_QUOTES, 'UTF-8');
        $price = htmlspecialchars($_POST['price'], ENT_QUOTES, 'UTF-8');
        $category = htmlspecialchars($_POST['category'], ENT_QUOTES, 'UTF-8');
        $stock = htmlspecialchars($_POST['stock'], ENT_QUOTES, 'UTF-8');
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
                    '1_unix.pdf'
                );

                //add book into database
                if (insertBook($book)) {
                    $logger->writeLog('INFO', "Book: " . $book[0] . " added into database");
                    $accessControlManager->redirectToHome();
                } else {
                    throw new Exception('Could not add the book');
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
    <link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
    <title>Add New Book</title>
</head>
<body>

<?php
include "./../layout/header.php";
?>

<div class="container bg-light mt-5 w-50">
    <h2>Add New Book</h2>

    <form action="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/admin/addBook.php'); ?>" method="post">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Title"
                   value="The Art of Programming" required>
        </div>
        <div class="form-group">
            <label for="author">Author:</label>
            <input type="text" class="form-control" id="author" name="author" placeholder="Author" value="John Coder"
                   required>
        </div>
        <div class="form-group">
            <label for="publisher">Publisher:</label>
            <input type="text" class="form-control" id="publisher" name="publisher" placeholder="Publisher"
                   value="Code Publications" required>
        </div>
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" class="form-control" id="price" name="price" step="0.05" placeholder="Price"
                   value="24.99" required>
        </div>
        <div class="form-group">
            <label for="category">Category:</label>
            <input type="text" class="form-control" id="category" name="category" placeholder="Category"
                   value="Programming" required>
        </div>
        <div class="form-group">
            <label for="stock">Stock:</label>
            <input type="number" class="form-control" id="stock" name="stock" placeholder="Stock" value="100" required>
        </div>

        <!-- Hidden token to protect against CSRF -->
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">

        <button type="submit" class="btn btn-primary">Add</button>
    </form>

</div>

</body>
</html>

