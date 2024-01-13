<?php
global $shoppingCartHandler;
global $logger;

class AccessControlManager{
    private static ?AccessControlManager $instance = null;
    private $orderSummaryPath;
    private $shippingInfoPath;
    private $paymentInfoPath;
    private $loginPath;
    private $homePath;
    private $homeAdminPath;
    private $shoppingCart;

    private function __construct() {
        $this->orderSummaryPath = '//' . SERVER_ROOT . '/php/user/orderSummary.php';
        $this->shippingInfoPath = '//' . SERVER_ROOT . '/php/user/shippingInfo.php';
        $this->paymentInfoPath = '//' . SERVER_ROOT . '/php/user/paymentMethod.php';
        $this->loginPath = '//' . SERVER_ROOT . '/php/login.php';
        $this->homePath = '//' . SERVER_ROOT . '/';
        $this->homeAdminPath = '//' . SERVER_ROOT . '/php/admin/homeAdmin.php';
        $this->shoppingCart = '//' . SERVER_ROOT . '/php/user/shoppingCart.php';
    }


    public static function getInstance(): ?AccessControlManager{
        if (self::$instance == null) {
            self::$instance = new AccessControlManager();
        }
        return self::$instance;
    }

    function redirectToHome(): void{
        global $sessionHandler;

        if ($sessionHandler->isLogged() and $_SESSION['isAdmin'] == 1) {
            header('Location: ' . $this->homeAdminPath);
            exit;
        }
        else{
            header('Location: ' . $this->homePath);
            exit;
        }
    }

    function redirectIfAnonymous(): void{
        global $sessionHandler;

        if (!$sessionHandler->isLogged()) {
            header('Location: ' . $this->loginPath);
            exit;
        }
    }

    /**
     * @throws Exception
     */
    function routeMultiStepCheckout(): void{
        global $shoppingCartHandler;

        if (isset($_SESSION['paymentInfo']) && isset($_SESSION['shippingInfo'])) {
            if($shoppingCartHandler->syncShoppingCart()){
                header('Location: ' . $this->orderSummaryPath);
                exit;
            }
            else{
                throw new Exception("Error during checking information at checkout.");
            }
        }
        else{
            if (isset($_SESSION['paymentInfo'])){
                header('Location: ' . $this->shippingInfoPath);
            }
            else{
                header('Location: ' . $this->paymentInfoPath);
            }
            exit;
        }
    }

    function checkFinalStepCheckout(): void{
        if (!(isset($_SESSION['paymentInfo']) && isset($_SESSION['shippingInfo']))) {
            header('Location: ' . $this->shoppingCart);
            exit;
        }
    }

    function getNextStepToCheckout(): string{
        if(isset($_SESSION['paymentInfo']) && isset($_SESSION['shippingInfo'])){
            return $this->orderSummaryPath;
        }
        else{
            if (isset($_SESSION['paymentInfo'])){
                return $this->shippingInfoPath;
            }
            else{
                return $this->paymentInfoPath;
            }
        }
    }

    function redirectIfXSRFAttack(): void{
        $logger->writeLog('ERROR', "XSRF attack detected");
        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
        exit;
    }
}