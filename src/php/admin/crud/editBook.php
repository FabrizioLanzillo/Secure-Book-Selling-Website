<?php
require_once "../../../config.php";
require_once "../../util/dbInteraction.php";

global $logger;
global $errorHandler;

function checkBookData(): bool{
    $requiredFields = ['id','title', 'author', 'publisher', 'price', 'category', 'stock'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            return false;
        }
    }
    return true;
}

$bookId = $_GET['book_id'] ?? null;

// retrieve the book that admin want to edit
$editBook = (getBookDetails($bookId))->fetch_assoc();

if(checkBookData()){
    try{
        $book = array(
            $_POST['title'],
            $_POST['author'],
            $_POST['publisher'],
            $_POST['price'],
            $_POST['category'],
            $_POST['stock'],
            $_POST['id'],
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="../../../css/bootstrap/bootstrap.min.css">
    <title>Edit Book</title>
</head>
<body>

<?php
include "../../layout/header.php";
?>

<div class="container bg-light mt-5 w-50">
    <h2>Edit Book</h2>

    <form action="//<?php echo SERVER_ROOT . '/php/admin/crud/editBook.php'?>" method="post">
        <div class="form-group d-none">
            <label for="id">Id:</label>
            <input type="text" class="form-control" id="id" name="id" value="<?php echo !empty($editBook['id']) ? $editBook['id'] : 'id'; ?>" required>
        </div>
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Book" value="<?php echo !empty($editBook['title']) ? $editBook['title'] : 'Book'; ?>" required>
        </div>
        <div class="form-group">
            <label for="author">Author:</label>
            <input type="text" class="form-control" id="author" name="author" placeholder="Bookkin" value="<?php echo !empty($editBook['author']) ? $editBook['author'] : 'Bookkin'; ?>" required>
        </div>
        <div class="form-group">
            <label for="publisher">Publisher:</label>
            <input type="text" class="form-control" id="publisher" name="publisher" placeholder="Book House" value="<?php echo !empty($editBook['publisher']) ? $editBook['publisher'] : 'Book House'; ?>" required>
        </div>
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" class="form-control" id="price" name="price" step="0.05" placeholder="13.90" value="<?php echo !empty($editBook['price']) ? $editBook['price'] : '13.90'; ?>" required>
        </div>
        <div class="form-group">
            <label for="category">Category:</label>
            <input type="text" class="form-control" id="category" name="category" placeholder="Romance" value="<?php echo !empty($editBook['category']) ? $editBook['category'] : 'Romance'; ?>" required>
        </div>
        <div class="form-group">
            <label for="stock">Stock:</label>
            <input type="number" class="form-control" id="stock" name="stock" placeholder="50" value="<?php echo !empty($editBook['stocks_number']) ? $editBook['stocks_number'] : '50'; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>

</div>

</body>
</html>

