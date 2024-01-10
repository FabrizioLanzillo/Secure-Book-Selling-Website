<?php
require_once __DIR__ . "/../../config.php";

global $logger;
global $errorHandler;
global $sessionHandler;

if ($sessionHandler->isLogged() and $sessionHandler->isAdmin()) {

    $bookId = $_GET['book_id'] ?? null;

    try {
        $success = deleteBook($bookId);

        if($success){
            $message = "Book: ".$bookId." removed from database";
            $logger->writeLog('INFO', $message);
            header('Location: ./bookList.php');
            exit;
        }
        else{
            throw new Exception('Could not remove the book');
        }
    }catch (Exception $e){
        $errorHandler->handleException($e);
    }

}else{
    header('Location: //' . SERVER_ROOT . '/');
    exit;
}




