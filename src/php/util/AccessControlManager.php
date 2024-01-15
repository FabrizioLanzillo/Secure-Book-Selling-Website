<?php
global $shoppingCartHandler;
global $logger;

/**
 * This class manages redirections in case of unauthorized access by the user
 * it also implements the routing logic for checkout
 */
class AccessControlManager
{
    private static ?AccessControlManager $instance = null;
    private string $orderSummaryPath;
    private string $shippingInfoPath;
    private string $paymentInfoPath;
    private string $loginPath;
    private string $homePath;
    private string $homeAdminPath;
    private string $shoppingCart;

    /**
     * In the constructor the path variables are set
     */
    private function __construct()
    {
        $this->orderSummaryPath = '//' . SERVER_ROOT . '/php/user/orderSummary.php';
        $this->shippingInfoPath = '//' . SERVER_ROOT . '/php/user/shippingInfo.php';
        $this->paymentInfoPath = '//' . SERVER_ROOT . '/php/user/paymentMethod.php';
        $this->loginPath = '//' . SERVER_ROOT . '/php/login.php';
        $this->homePath = '//' . SERVER_ROOT . '/';
        $this->homeAdminPath = '//' . SERVER_ROOT . '/php/admin/homeAdmin.php';
        $this->shoppingCart = '//' . SERVER_ROOT . '/php/user/shoppingCart.php';
    }

    /**
     * This method returns the singleton instance of AccessControlManager.
     * If the instance doesn't exist, it creates one; otherwise, it returns the existing instance.
     * @return AccessControlManager
     */
    public static function getInstance(): ?AccessControlManager
    {
        if (self::$instance == null) {
            self::$instance = new AccessControlManager();
        }
        return self::$instance;
    }

    /**
     * This method redirect the navigation to the homepage
     * If a user is logged in and is also admin the latter is redirected to the admins homepage
     * @param $getParameterName , is an optional parameter that is used when you want to add a GET parameter
     *                                  and corresponds to the parameter name
     * @param $getParameterValue , is an optional parameter that is used when you want to add a GET parameter
     *                                   and corresponds to the parameter value
     * @return void
     */
    function redirectToHome($getParameterName = null, $getParameterValue = null): void
    {
        global $sessionHandler;

        if ($sessionHandler->isLogged() and $sessionHandler->isAdmin()) {
            header('Location: ' . $this->homeAdminPath);
            exit;
        } else {
            if (isset($getParameterName) && isset($getParameterValue)) {
                header('Location: ' . $this->homePath . '?' . $getParameterName . '=' . $getParameterValue);
            } else {
                header('Location: ' . $this->homePath);
            }
            exit;
        }
    }

    /**
     * This method checks if the user is logged or not, and if not the latter is redirected to the login page
     * This method is called in pages that contain sensitive data or in pages that anonymous users cannot access
     * @return void
     */
    function redirectIfAnonymous(): void
    {
        global $sessionHandler;
        global $logger;

        if (!$sessionHandler->isLogged()) {
            $logger->writeLog('WARNING', "Unauthorized Access to the protected area");
            header('Location: ' . $this->loginPath);
            exit;
        }
    }

    /**
     * This method checks if admin tries to access user-only area that admin cannot access
     * @return void
     */
    function redirectIfAdmin(): void
    {
        global $sessionHandler;
        global $logger;

        if ($sessionHandler->isAdmin()) {
            $logger->writeLog('WARNING', "Unauthorized Access to normal-user-only area by admin: " . $_SESSION['email']);
            header('Location: ' . $this->homeAdminPath);
            exit;
        }
    }

    /**
     * This method checks if a normal user tries to access admin-only area that normal users cannot access
     * @return void
     */
    function redirectIfNormalUser(): void
    {
        global $sessionHandler;
        global $logger;

        if (!$sessionHandler->isAdmin()) {
            $logger->writeLog('WARNING', "Unauthorized Access to admin-only area by normal user: " . $_SESSION['email']);
            header('Location: ' . $this->homePath);
            exit;
        }
    }

    /**
     * This method takes care of correctly route the user to the multiple steps of the checkout
     * @throws Exception
     */
    function routeMultiStepCheckout(): void
    {
        global $shoppingCartHandler;

        // If the session variable containing the payment method information and
        // the session variable containing the shipping information are set
        // then I can access the order summary page
        if (isset($_SESSION['paymentInfo']) && isset($_SESSION['shippingInfo'])) {
            if ($shoppingCartHandler->syncShoppingCart()) {
                header('Location: ' . $this->orderSummaryPath);
                exit;
            } else {
                throw new Exception("Error during checking information at checkout.");
            }
        }
        // Otherwise I am redirected to the page that is responsible for collecting the missing data.
        // If both variables are not set the user is first routed to the one that collects payment information
        // and then to the one for shipping information
        else {
            if (isset($_SESSION['paymentInfo'])) {
                header('Location: ' . $this->shippingInfoPath);
            } else {
                header('Location: ' . $this->paymentInfoPath);
            }
            exit;
        }
    }

    /**
     * This method is called in the last step of the payment and checks that the user can access this page,
     * and then has entered all the required information
     * @return void
     */
    function checkFinalStepCheckout(): void
    {
        global $logger;
        if (!(isset($_SESSION['paymentInfo']) && isset($_SESSION['shippingInfo']))) {
            $logger->writeLog('WARNING', "Unauthorized Access to the Final Checkout Page Detected");
            header('Location: ' . $this->shoppingCart);
            exit;
        }
    }

    /**
     * This method return the link of the next step, it is used for the checkout link in the shopping cart
     * if the user has entered all the payment info and shipping info, the link will route the user to the final page
     * otherwise, the user will be directed to the page of the missing info.
     * @return string
     */
    function getNextStepToCheckout(): string
    {
        if (isset($_SESSION['paymentInfo']) && isset($_SESSION['shippingInfo'])) {
            return $this->orderSummaryPath;
        } else {
            if (isset($_SESSION['paymentInfo'])) {
                return $this->shippingInfoPath;
            } else {
                return $this->paymentInfoPath;
            }
        }
    }

    /**
     * This method redirect the user to an error page if a XSRF attack is detected
     * @return void
     */
    function redirectIfXSRFAttack(): void
    {
        global $logger;
        $logger->writeLog('WARNING', "XSRF Attack Detected");
        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
        exit;
    }
}