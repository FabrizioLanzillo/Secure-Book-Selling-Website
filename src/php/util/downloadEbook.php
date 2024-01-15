<?php
require_once __DIR__ . "/../../config.php";

global $sessionHandler;
global $shoppingCartHandler;
global $errorHandler;
global $accessControlManager;
global $logger;

// Check path manipulation and broken access control
// Check if the user is logged
$accessControlManager->redirectIfAnonymous();
// Check if an admin tries to access this page
$accessControlManager->redirectIfAdmin();

// path to the e-book folder, that is not in /var/www/html
// so is not accessible by the user
$eBookPath = '/home/bookselling/e-books/';

try {
    if (checkFormData(['id_book'])) {

        // Protect against XSS
        $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');
        $idBook = htmlspecialchars($_POST['id_book'], ENT_QUOTES, 'UTF-8');

        // Protect against XSRF
        if (!$token || $token !== $_SESSION['token']) {
            // return 405 http status code
            $accessControlManager->redirectIfXSRFAttack();
        } else {
            // check if the user has bought the book selected
            $result = checkBookPurchaseByBook($_SESSION['userId'], $idBook);
            if ($result) {
                // check if the query returned a result and exactly 1 row
                $dataQuery = $result->fetch_assoc();
                if ($dataQuery !== null && $result->num_rows === 1) {
                    $ebookName = $dataQuery['ebook_name'];
                    // creation of the filePath and download request
                    $filePath = $eBookPath . $ebookName;
                    if (file_exists($filePath)) {
                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment; filename="' . $ebookName . '"');
                        readfile($filePath);
                        $logger->writeLog('INFO', "E-Book: " . $ebookName . " downloaded correctly by the user: " . $_SESSION['email']);
                        exit;
                    } else {
                        throw new Exception("File not found");
                    }
                } else {
                    throw new Exception("Book is not available for download");
                }
            } else {
                throw new Exception("Error retrieving book information");
            }
        }
    } else {
        throw new Exception("The given data are incorrect");
    }
} catch (Exception $e) {
    // in case of error the user is redirect to home and a message with the error will be displayed
    $logger->writeLog('ERROR', "E-Book download attempt of the user: " . $_SESSION['email'] .
        " failed for this reason: " . $e->getMessage());
    $accessControlManager->redirectToHome("downloadBookError", $e->getMessage());
}