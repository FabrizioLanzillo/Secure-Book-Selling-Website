<?php
require_once __DIR__ . "/../config.php";

global $logger;
global $errorHandler;
global $sessionHandler;
global $accessControlManager;

// Check path manipulation and broken access control
// Check if the user is logged
$accessControlManager->redirectIfAnonymous();

try{
    // Call of the method that clears all session data, regenerates the session id, and destroy the session
    // in order to provide a safe logout.
    if($sessionHandler->unsetSession()){
        $logger->writeLog('INFO', "SessionID changed after the logout, in order to avoid SESSION FIXATION attacks ");
        $logger->writeLog('INFO', "Logout of the user succeeded");
        $accessControlManager->redirectToHome();
    }
    else{
        throw new Exception('Error during the logout');
    }
}
catch (Exception $e) {
    $logger->writeLog('ERROR', "Logout of the user failed");
    $errorHandler->handleException($e);
}

