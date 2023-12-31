<?php
    require_once __DIR__ . "/php/util/sessionManager.php";
    require_once __DIR__ . "/php/util/Logger.php";
    require_once __DIR__ . "/php/util/ErrorHandler.php";

    $debug = true;
    $logger = new Logger(__DIR__ .'/logs/web_server_logs.txt', $debug);
    $errorHandler = ErrorHandler::getInstance();

    // This sets the lifetime of the session cookie to 10800 seconds (3 hour).
    $lifetime = 10800;
    // This sets the path on the server where the cookie will be available.
    $path = '/';
    // This sets the 'secure' flag of the session cookie, meaning it will only be sent over HTTPS.
    $secure = true;
    // This sets the 'httponly' flag of the session cookie, meaning it can't be accessed by JavaScript.
    $httponly = true;

    // This function starts a new session or resumes an existing one
    session_set_cookie_params([
        'lifetime' => $lifetime,
        'path' => $path,
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => $secure,
        'httponly' => $httponly
     ]);
     
     session_start();

    define("PROJECT_ROOT", $_SERVER["DOCUMENT_ROOT"]);
    define("SERVER_ROOT", $_SERVER["SERVER_NAME"]);

