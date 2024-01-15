<?php
    require_once __DIR__ . "/php/util/SessionManager.php";
    require_once __DIR__ . "/php/util/Logger.php";
    require_once __DIR__ . "/php/util/ErrorHandler.php";
    require_once __DIR__ . "/php/util/EmailSender.php";
    require_once __DIR__ . "/php/util/SessionManager.php";
    require_once __DIR__ . "/php/util/ShoppingCartHandler.php";
    require_once __DIR__ . "/php/util/function.php";
    require_once __DIR__ . "/php/util/dbInteraction.php";
    require_once __DIR__ . "/php/util/AccessControlManager.php";

    define("PROJECT_ROOT", $_SERVER["DOCUMENT_ROOT"]);
    define("SERVER_ROOT", $_SERVER["SERVER_NAME"]);

    header(
        "Content-Security-Policy: " .
        "default-src 'self'; " .
        "script-src 'self' https://cdnjs.cloudflare.com/ https://www.bookselling.snh/ 'unsafe-inline' https://code.jquery.com/ https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/ https://stackpath.bootstrapcdn.com/; " .
        "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com/ https://stackpath.bootstrapcdn.com/; " .
        "font-src 'self' https://cdnjs.cloudflare.com/ https://stackpath.bootstrapcdn.com/; " .
        "img-src 'self' data:; " .
        "base-uri 'self'; " .
        "form-action 'self'; " .
        "frame-ancestors 'self';"
    );

    $debug = true;
    $lifetime = 10800;
    $path = '/';
    $secure = true;
    $httponly = true;

    $logger = Logger::getInstance(__DIR__ .'/logs/web_server_logs.txt', $debug);
    $errorHandler = ErrorHandler::getInstance();
    $emailSender = EmailSender::getInstance();
    $sessionHandler = SessionManager::getInstance($lifetime, $path, $secure, $httponly);
    $shoppingCartHandler = ShoppingCartHandler::getInstance();
    $accessControlManager = AccessControlManager::getInstance();



