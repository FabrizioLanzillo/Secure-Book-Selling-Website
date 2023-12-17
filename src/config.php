<?php
    require_once __DIR__ . "/php/util/sessionManager.php";
    require_once __DIR__ . "/php/util/logger.php";

    $debug = true;
    $logger = new Logger(__DIR__ .'/logs/web_server_logs.txt', $debug);

    $listeningPort = 8000;
    
    define("PROJECT_ROOT", $_SERVER["DOCUMENT_ROOT"]);
    define("SERVER_ROOT", $_SERVER["SERVER_NAME"].":".$listeningPort);