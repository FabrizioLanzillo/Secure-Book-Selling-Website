<?php
require_once __DIR__ . "/../../config.php";

global $sessionHandler;
global $shoppingCartHandler;
global $errorHandler;
global $accessControlManager;

// path to the e-book folder, that is not in /var/www/html
// so is not accessible by the user
$eBookPath = '/home/bookselling/e-books/';

// This function checks if the user is anonymous or not
// if in case, the user will be redirected to the login
$accessControlManager->redirectIfAnonymous();

try{
    if(checkFormData(['id_book'])){
        // Protect against XSS
        $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');
        $idBook = htmlspecialchars($_POST['id_book'], ENT_QUOTES, 'UTF-8');

        // Protect against XSRF
        if (!$token || $token !== $_SESSION['token']) {
            // return 405 http status code
            $accessControlManager ->redirectIfXSRFAttack();
        }
        else {
            // check if the user has bought the book selected
            $result = checkBookPurchaseByBook($_SESSION['userId'], $idBook);
            if($result){
                // check if the query returned a result and exactly 1 row
                $dataQuery = $result->fetch_assoc();
                if ($dataQuery !== null && $result->num_rows === 1) {
                    $ebookName = $dataQuery['ebook_name'];

                    $filePath = $eBookPath . $ebookName;
                    if (file_exists($filePath)) {
                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment; filename="'.$ebookName.'"');
                        readfile($filePath);
                        exit;
                    }
                    else {
                        throw new Exception("File not found");
                    }
                }
                else{
                    throw new Exception("this book is not available for download");
                }
            }
            else{
                throw new Exception("Error retrieving book information");
            }
        }
    }
    else{
        throw new Exception("The given data are incorrect");
    }
}
catch (Exception $e) {
    $accessControlManager->redirectToHome("downloadBookError", $e->getMessage());
}