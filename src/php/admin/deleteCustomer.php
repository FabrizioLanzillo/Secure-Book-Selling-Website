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
$customerId = isset($_GET['user_id']) ? htmlspecialchars($_GET['user_id'], ENT_QUOTES, 'UTF-8') : null;

try {
    if ($customerId !== null && $customerId !== "") {
        // try to remove user from database
        if (deleteCustomer($customerId)) {
            $logger->writeLog('INFO', "Book: " . $customerId . " removed from database");
            header('Location: ./customerList.php');
            exit;
        } else {
            throw new Exception('Could not remove the customer');
        }
    } else {
        $logger->writeLog('WARNING', "Unauthorized Access to the delete customer page");
        throw new Exception('Customer to delete is not selected');
    }
} catch (Exception $e) {
    $errorHandler->handleException($e);
}
