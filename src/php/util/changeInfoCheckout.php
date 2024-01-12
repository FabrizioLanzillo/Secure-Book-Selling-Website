<?php
require_once __DIR__ . "/../../config.php";

global $logger;
global $errorHandler;
global $sessionHandler;
global $accessControlManager;

// This function checks if the user is anonymous or not
// if in case, the user will be redirected to the login
$accessControlManager->redirectIfAnonymous();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (checkFormData(['editInfo'])) {

            $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');
            $editInfo = htmlspecialchars($_POST['editInfo'], ENT_QUOTES, 'UTF-8');

            if (!$token || $token !== $_SESSION['token']) {
                // return 405 http status code
                header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
                exit;
            } else {
                if ($editInfo === 'shippingInfo') {
                    $sessionHandler->clearCheckoutInfo('shippingInfo');
                }
                elseif ($editInfo === 'paymentInfo'){
                    $sessionHandler->clearCheckoutInfo('paymentInfo');
                }
                else {
                    throw new Exception("Checkout Information Value to edit is wrong");
                }
                $accessControlManager->routeMultiStepCheckout();
            }
        }
    } catch (Exception $e) {
        $errorHandler->handleException($e);
    }
}