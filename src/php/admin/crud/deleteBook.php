<?php
require_once "../../../config.php";
require_once "../../util/dbInteraction.php";

global $logger;
global $errorHandler;

$bookId = $_GET['book_id'] ?? null;

//if(isLogged() && isAdmin())

try {
    $success = deleteBook($bookId);

    if($success){
        $message = "Book: ".$bookId." removed from database";
        $logger->writeLog('INFO', $message);
        header('Location: ../homeAdmin.php'/*?event='.$message*/);
        exit;
    }
    else{
        throw new Exception('Could not remove the book');
    }
}catch (Exception $e){
    $errorHandler->handleException($e);
}


