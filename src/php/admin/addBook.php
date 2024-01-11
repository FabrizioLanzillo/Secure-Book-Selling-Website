<?php
require_once __DIR__ . "/../../config.php";

global $sessionHandler;
global $logger;
global $errorHandler;

function checkBookData(): bool{
    $requiredFields = ['title', 'author', 'publisher', 'price', 'category', 'stock'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            return false;
        }
    }
    return true;
}

if ($sessionHandler->isLogged() and $sessionHandler->isAdmin()) {
    if(checkBookData()){
        try{
            $book = array(
                $_POST['title'],
                $_POST['author'],
                $_POST['publisher'],
                $_POST['price'],
                $_POST['category'],
                $_POST['stock'],
            );

            $result = insertBook($book);
            if($result){
                $logger->writeLog('INFO', "Book: ".$book[0]." added into database");
                header('Location: //' . SERVER_ROOT . '/php/admin/homeAdmin.php');
                exit;
            }
            else{
                throw new Exception('Could not add the book');
            }

        } catch (Exception $e) {
            $errorHandler->handleException($e);
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
    <link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
    <title>Add New Book</title>
</head>
<body>

<?php
include "./../layout/header.php";
?>

<div class="container bg-light mt-5 w-50">
    <h2>Add New Book</h2>

    <form action="//<?php echo SERVER_ROOT . '/php/admin/addBook.php'?>" method="post">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Book" value="Book" required>
        </div>
        <div class="form-group">
            <label for="author">Author:</label>
            <input type="text" class="form-control" id="author" name="author" placeholder="Bookkin" value="Bookkin" required>
        </div>
        <div class="form-group">
            <label for="publisher">Publisher:</label>
            <input type="text" class="form-control" id="publisher" name="publisher" placeholder="Book House" value="Book House" required>
        </div>
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" class="form-control" id="price" name="price" step="0.05" placeholder="13.90" value="13.90" required>
        </div>
        <div class="form-group">
            <label for="category">Category:</label>
            <input type="text" class="form-control" id="category" name="category" placeholder="Romance" value="Romance" required>
        </div>
        <div class="form-group">
            <label for="stock">Stock:</label>
            <input type="number" class="form-control" id="stock" name="stock" placeholder="50" value="50" required>
        </div>
        <button type="submit" class="btn btn-primary">Add</button>
    </form>

</div>

</body>
</html>

