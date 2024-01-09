<?php
    require_once __DIR__ . "/php/util/SessionManager.php";
    require_once __DIR__ . "/php/util/Logger.php";
    require_once __DIR__ . "/php/util/ErrorHandler.php";
    require_once __DIR__ . "/php/util/EmailSender.php";
    require_once __DIR__ . "/php/util/SessionManager.php";
    require_once __DIR__ . "/php/util/ShoppingCartHandler.php";
    require_once __DIR__ . "/php/util/function.php";
    require_once __DIR__ . "/php/util/dbInteraction.php";

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

    define("PROJECT_ROOT", $_SERVER["DOCUMENT_ROOT"]);
    define("SERVER_ROOT", $_SERVER["SERVER_NAME"]);

