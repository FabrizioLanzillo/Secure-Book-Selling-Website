<?php
require_once __DIR__ . "/../../config.php";

global $logger;
global $errorHandler;
global $sessionHandler;
global $accessControlManager;

if ($sessionHandler->isLogged() and $sessionHandler->isAdmin()) {
    // Sanitize user input
    $customerId = isset($_GET['user_id']) ? htmlspecialchars($_GET['user_id'], ENT_QUOTES, 'UTF-8') : null;

    try {
        if($customerId !== null) {
            // try to remove user from database
            if (deleteCustomer($customerId)) {
                $logger->writeLog('INFO', "Book: " . $customerId . " removed from database");
                header('Location: ./customerList.php');
                exit;
            } else {
                throw new Exception('Could not remove the customer');
            }
        }
        else{
            throw new Exception('Customer to delete is not selected');

        }
    } catch (Exception $e) {
        $errorHandler->handleException($e);
    }
} else {
    $accessControlManager->redirectToHome();
}