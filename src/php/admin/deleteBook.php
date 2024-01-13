<?php
require_once __DIR__ . "/../../config.php";

global $logger;
global $errorHandler;
global $sessionHandler;

if ($sessionHandler->isLogged() and $sessionHandler->isAdmin()) {

    // Sanitize user input
    $bookId = isset($_GET['book_id']) ? htmlspecialchars($_GET['book_id'], ENT_QUOTES, 'UTF-8') : null;

    try {
        // try to remove book from database
        $success = deleteBook($bookId);

        if($success){
            $message = "Book: ".$bookId." removed from database";
            $logger->writeLog('INFO', $message);
            header('Location: ./homeAdmin.php');
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




