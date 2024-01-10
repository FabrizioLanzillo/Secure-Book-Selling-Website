<?php
require_once __DIR__ . "/../../config.php";

global $logger;
global $errorHandler;
global $sessionHandler;

if ($sessionHandler->isLogged() and $sessionHandler->isAdmin()) {

    $customerId = $_GET['user_id'] ?? null;

    try {
        $success = deleteCustomer($customerId);

        if ($success) {
            $message = "Book: " . $customerId . " removed from database";
            $logger->writeLog('INFO', $message);
            header('Location: ./customerList.php');
            exit;
        } else {
            throw new Exception('Could not remove the customer');
        }
    } catch (Exception $e) {
        $errorHandler->handleException($e);
    }

}else{
    header('Location: //' . SERVER_ROOT . '/');
    exit;
}