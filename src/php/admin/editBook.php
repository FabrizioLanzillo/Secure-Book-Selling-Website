<?php
require_once __DIR__ . "/../../config.php";

global $sessionHandler;
global $logger;
global $errorHandler;
global $accessControlManager;

function checkBookData(): bool{
    $requiredFields = ['id','title', 'author', 'publisher', 'price', 'category', 'stock'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            return false;
        }
    }
    return true;
}

if ($sessionHandler->isLogged() and $sessionHandler->isAdmin()) {

    // Sanitize user input
    $bookId = isset($_GET['book_id']) ? htmlspecialchars($_GET['book_id'], ENT_QUOTES, 'UTF-8') : null;

// retrieve the book that admin want to edit
    $editBook = (getBookDetails($bookId))->fetch_assoc();

    
    if(checkBookData()){
        
        $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');
        $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
        $author = htmlspecialchars($_POST['author'], ENT_QUOTES, 'UTF-8');
        $publisher = htmlspecialchars($_POST['publisher'], ENT_QUOTES, 'UTF-8');
        $price = htmlspecialchars($_POST['price'], ENT_QUOTES, 'UTF-8');
        $category = htmlspecialchars($_POST['category'], ENT_QUOTES, 'UTF-8');
        $stock = htmlspecialchars($_POST['stock'], ENT_QUOTES, 'UTF-8');
        $id = htmlspecialchars($_POST['id'], ENT_QUOTES, 'UTF-8');

        if (!$token || $token !== $_SESSION['token']) {
            // return 405 http status code
            $accessControlManager ->redirectIfXSRFAttack();
        } else {
            try{
                $book = array(
                    $title,
                    $author,
                    $publisher,
                    $price,
                    $category,
                    $stock,
                    $id,
                );

                $result = updateBook($book);
                if($result){
                    $logger->writeLog('INFO', "Book: ".$book[0]."with id= ".$book[6]." updated");
                    header('Location: //' . SERVER_ROOT . '/php/admin/homeAdmin.php');
                    exit;
                }
                else{
                    throw new Exception('Could not update the book');
                }

            } catch (Exception $e) {
                $errorHandler->handleException($e);
            }
        }
    }
}else{
    header('Location: //' . SERVER_ROOT . '/');
    exit;
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

    <form action="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/admin/editBook.php');?>" method="post">
        <div class="form-group d-none">
            <label for="id">Id:</label>
            <input type="text" class="form-control" id="id" name="id" value="<?php echo htmlspecialchars(!empty($editBook['id']) ? $editBook['id'] : 'id'); ?>" required>
        </div>
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Book" value="<?php echo htmlspecialchars(!empty($editBook['title']) ? $editBook['title'] : 'Book'); ?>" required>
        </div>
        <div class="form-group">
            <label for="author">Author:</label>
            <input type="text" class="form-control" id="author" name="author" placeholder="Bookkin" value="<?php echo htmlspecialchars(!empty($editBook['author']) ? $editBook['author'] : 'Bookkin'); ?>" required>
        </div>
        <div class="form-group">
            <label for="publisher">Publisher:</label>
            <input type="text" class="form-control" id="publisher" name="publisher" placeholder="Book House" value="<?php echo htmlspecialchars(!empty($editBook['publisher']) ? $editBook['publisher'] : 'Book House'); ?>" required>
        </div>
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" class="form-control" id="price" name="price" step="0.05" placeholder="13.90" value="<?php echo htmlspecialchars(!empty($editBook['price']) ? $editBook['price'] : '13.90'); ?>" required>
        </div>
        <div class="form-group">
            <label for="category">Category:</label>
            <input type="text" class="form-control" id="category" name="category" placeholder="Romance" value="<?php echo htmlspecialchars(!empty($editBook['category']) ? $editBook['category'] : 'Romance'); ?>" required>
        </div>
        <div class="form-group">
            <label for="stock">Stock:</label>
            <input type="number" class="form-control" id="stock" name="stock" placeholder="50" value="<?php echo htmlspecialchars(!empty($editBook['stocks_number']) ? $editBook['stocks_number'] : '50'); ?>" required>
        </div>

        <!-- Hidden token to protect against CSRF -->
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">

        <button type="submit" class="btn btn-primary">Update</button>
    </form>

</div>

</body>
</html>

