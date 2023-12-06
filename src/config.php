<?php
    require_once __DIR__ . "/php/util/sessionManager.php";
    require_once __DIR__ . "/php/util/logger.php";

    $logger = new Logger('/logs/web_server_logs.txt');

    $listeningPort = 8000;
    
    define("PROJECT_ROOT", $_SERVER["DOCUMENT_ROOT"]);
    define("SERVER_ROOT", $_SERVER["SERVER_NAME"].":".$listeningPort);