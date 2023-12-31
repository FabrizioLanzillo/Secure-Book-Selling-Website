<?php
    require_once __DIR__ . "./../config.php";

    global $logger;

    unsetSession();

    session_regenerate_id(true);
    $logger->writeLog('INFO', "SessionID changed in order to avoid Session Fixation attacks ");
    $logger->writeLog('INFO', "Logout of the user succeeded");

	header("Location: ./../");