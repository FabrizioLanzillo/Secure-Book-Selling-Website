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
            if ($_POST['editInfo'] === 'shippingInfo') {
                $sessionHandler->clearCheckoutInfo('shippingInfo');
            }
            elseif ($_POST['editInfo'] === 'paymentInfo'){
                $sessionHandler->clearCheckoutInfo('paymentInfo');
            }
            else {
                throw new Exception("Checkout Information Value to edit is wrong");
            }
            $accessControlManager->routeMultiStepCheckout();
        }
    } catch (Exception $e) {
        $errorHandler->handleException($e);
    }
}