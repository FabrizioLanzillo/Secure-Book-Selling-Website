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
require_once __DIR__ . "/php/util/InputValidation.php";

define("PROJECT_ROOT", $_SERVER["DOCUMENT_ROOT"]);
define("SERVER_ROOT", $_SERVER["SERVER_NAME"]);
define("CURRENT_SCRIPT", basename($_SERVER['PHP_SELF']));

// Content Security Policy (CSP)
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

// When the debug variable is true, the application may display more detailed error messages, useful during debugging
$debug = true;
// The lifetime variable sets the lifetime of the session to 7200 seconds (2 hours)
$lifetime = 7200;
// Set of number of attempt for the login function before being blocked
$numberLoginAttempt = 10;
// Set of the duration in seconds of the time window for the login brute force check
$timeWindowDuration = 30;

// --- Session Cookie Params ---
// The path variable sets the domain where the session cookie will work.
// Use a single slash ('/') for all paths on the domain.
$path = '/';
// When the secure variable is true, the session cookie will only be sent over secure connections.
$secure = true;
// When the httponly variable is true, then PHP will attempt to send the httponly flag when setting the session cookie.
// The httponly flagMarks the cookie as accessible only through the HTTP protocol.
// This means that the cookie won't be accessible by scripting languages, such as JavaScript.
// This setting can effectively help to reduce identity theft through XSS attacks
$httponly = true;

$logger = Logger::getInstance(__DIR__ . '/logs/web_server_logs.txt', $debug);
$errorHandler = ErrorHandler::getInstance();
$emailSender = EmailSender::getInstance();
$sessionHandler = SessionManager::getInstance($lifetime, $path, $secure, $httponly);
$shoppingCartHandler = ShoppingCartHandler::getInstance();
$accessControlManager = AccessControlManager::getInstance();
$validator = InputValidation::getInstance();

// If the page is different from the logout, this method is invoked in order to check if the session is expired
// if the session is expired then logout is called.
if (CURRENT_SCRIPT !== 'logout.php') {
    $sessionHandler->checkSessionLifetime();
}

