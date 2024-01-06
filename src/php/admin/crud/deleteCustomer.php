<?php
require_once "../../../config.php";
require_once "../../util/dbInteraction.php";

global $logger;
global $errorHandler;

$customerId = $_GET['user_id'] ?? null;

//if(isLogged() && isAdmin())

try {
    $success = deleteCustomer($customerId);

    if($success){
        $message = "Book: ".$customerId." removed from database";
        $logger->writeLog('INFO', $message);
        header('Location: ../customerList.php'/*?event='.$message*/);
        exit;
    }
    else{
        throw new Exception('Could not remove the customer');
    }
}catch (Exception $e){
    $errorHandler->handleException($e);
}
