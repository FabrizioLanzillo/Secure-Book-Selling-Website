<?php
require_once __DIR__ . "/../../config.php";

global $logger;
global $errorHandler;
global $sessionHandler;
global $accessControlManager;

// Check path manipulation and broken access control
// Check if the user is logged
$accessControlManager->redirectIfAnonymous();
// Check if a normal user tries to access this page
$accessControlManager->redirectIfNormalUser();

// Sanitize user input
$bookId = isset($_GET['book_id']) ? htmlspecialchars($_GET['book_id'], ENT_QUOTES, 'UTF-8') : null;

try {
    if ($bookId !== null && $bookId !== "") {
        // try to remove book from database
        if (deleteBook($bookId)) {
            $message = "Book: " . $bookId . " removed from database";
            $logger->writeLog('INFO', $message);
            header('Location: ./homeAdmin.php');
            exit;
        } else {
            throw new Exception('Could not remove the book');
        }
    } else {
        $logger->writeLog('WARNING', "Unauthorized Access to the delete book page");
        throw new Exception('Book to delete is not selected');
    }
} catch (Exception $e) {
    $errorHandler->handleException($e);
}



