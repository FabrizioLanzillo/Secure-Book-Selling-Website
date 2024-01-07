<?php
require_once __DIR__ . "./../config.php";

global $logger;
global $errorHandler;
global $sessionHandler;

try{
    if($sessionHandler->unsetSession()){
        $logger->writeLog('INFO', "SessionID changed in order to avoid Session Fixation attacks ");
        $logger->writeLog('INFO', "Logout of the user succeeded");
        header("Location: ./../");
    }
    else{
        throw new Exception('Error during the logout');
    }
}
catch (Exception $e) {
    $errorHandler->handleException($e);
    $logger->writeLog('INFO', "Logout of the user failed");
}

