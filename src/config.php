<?php
    require_once __DIR__ . "/php/util/sessionManager.php";
    require_once __DIR__ . "/php/util/logger.php";

    $debug = true;
    $logger = new Logger(__DIR__ .'/logs/web_server_logs.txt', $debug);

    $listeningPort = 8000;

    // Inizia la sessione
    session_start();

    // Imposta i valori desiderati
    $lifetime = 3600; // Ad esempio, 1 ora
    $secure = true; // Imposta a true se vuoi che il cookie venga inviato solo su connessioni sicure
    $httponly = true; // Imposta a true se vuoi che il cookie sia accessibile solo tramite il protocollo HTTP

    // Imposta il cookie
    setcookie(
        session_name(),
        session_id(),
        time() + $lifetime,
        '/',
        $_SERVER['HTTP_HOST'],
        $secure,
        $httponly
    );

    define("PROJECT_ROOT", $_SERVER["DOCUMENT_ROOT"]);
    define("SERVER_ROOT", $_SERVER["SERVER_NAME"].":".$listeningPort);