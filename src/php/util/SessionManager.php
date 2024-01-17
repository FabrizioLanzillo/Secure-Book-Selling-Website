<?php
global $logger;

/**
 * This class manages the session variable and checks the type of the current user
 */
class SessionManager
{
    private static ?SessionManager $instance = null;
    private $lifetime;
    private $path;
    private $secure;
    private $httponly;

    /**
     * In the constructor the session cookie params are set and the session is started
     */
    private function __construct($lifetime, $path, $secure, $httponly)
    {
        $this->lifetime = $lifetime;
        $this->path = $path;
        $this->secure = $secure;
        $this->httponly = $httponly;
        $this->startSession();
    }

    /**
     * This method returns the singleton instance of SessionManager.
     * If the instance doesn't exist, it creates one; otherwise, it returns the existing instance.
     * @return SessionManager
     */
    public static function getInstance($lifetime, $path, $secure, $httponly): ?SessionManager
    {
        if (self::$instance == null) {
            self::$instance = new SessionManager($lifetime, $path, $secure, $httponly);
        }

        return self::$instance;
    }

    /**
     * This method sets the session cookie params and start the session
     * It also set the session token to avoid XSRF on login form
     * @return void
     */
    private function startSession(): void
    {
        session_set_cookie_params([
            'path' => $this->path,
            'domain' => $_SERVER['HTTP_HOST'],
            'secure' => $this->secure,
            'httponly' => $this->httponly
        ]);

        session_start();

        // Need to set the session token to avoid XSRF on login form
        if (!isset($_SESSION['token'])) {
            $_SESSION['token'] = md5(uniqid(mt_rand(), true));
        }
    }

    /**
     * This method sets the session variables of the user after the login,
     * they are used to identify the current user
     * @param $userId , is the id of the current user
     * @param $username , is the username of the current user
     * @param $email , is the email of the current user
     * @param $name , is the name of the current user
     * @param $isAdmin , indicate if the current user is an admin or not
     * @return void
     */
    public function setSession($userId, $username, $email, $name, $isAdmin): void
    {
        $_SESSION['userId'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $name;
        $_SESSION['isAdmin'] = $isAdmin;
        $_SESSION['token'] = md5(uniqid(mt_rand(), true));
        $_SESSION['lastInteraction'] = time();
    }

    /**
     * This method saves the credit card information of the current user as array
     * inside the paymentInfo session variable
     * @param $cardHolderName , is the name of the credit card holder
     * @param $cardNumber , is the credit card number
     * @param $Expire , is the expiration date of the card
     * @param $cvv , is the secure code of the credit card
     * @return void
     */
    public function saveCreditCardInfo($cardHolderName, $cardNumber, $Expire, $cvv): void
    {
        $_SESSION['paymentInfo'] = array(
            'cardHolderName' => $cardHolderName,
            'cardNumber' => $cardNumber,
            'expire' => $Expire,
            'cvv' => $cvv
        );
    }

    /**
     * This method clear the shippingInfo session variable or the paymentInfo session variable
     * it depends on the given variable
     * @param $checkoutInfo , is the name of the variable to clear
     * @return void
     */
    public function clearCheckoutInfo($checkoutInfo): void
    {
        if (isset($_SESSION[$checkoutInfo])) {
            unset($_SESSION[$checkoutInfo]);
        }
    }

    /**
     * This method saves the shipping information of the current user as array
     * inside the shippingInfo session variable
     * @param $fullName , is the full name at the domicile
     * @param $address , is the address of the current user
     * @param $city , is the city of the user
     * @param $province , is the province of the user
     * @param $cap , is the cap of the user
     * @param $country , is the country of the user
     * @return void
     */
    public function saveShippingInfo($fullName, $address, $city, $province, $cap, $country): void
    {
        $_SESSION['shippingInfo'] = array(
            'fullName' => $fullName,
            'address' => $address,
            'country' => $country,
            'province' => $province,
            'city' => $city,
            'cap' => $cap
        );
    }

    /**
     * This method is called on the logout and clears all session data
     * and also regenerates the session id, in order to provide a safe logout.
     * @return bool
     */
    public function unsetSession(): bool
    {
        try {
            // this function frees all session variables currently registered.
            session_unset();
            // this function will replace the current session id with a new one, in order to avoid Session Fixation attacks
            session_regenerate_id(true);
            // this destroys all the data associated with the current session
            // It does not unset any of the global variables associated with the session, or unset the session cookie.
            session_destroy();
            $this->startSession();
            return true;
        } catch (Exception) {
            return false;
        }
    }

    /**
     * This function checks id the user is anonymous or not
     * @return int
     */
    public function isLogged(): int
    {
        $loggedFields = ['userId', 'username', 'email', 'name', 'isAdmin', 'token'];
        foreach ($loggedFields as $field) {
            if (!isset($_SESSION[$field])) {
                return 0;
            }
        }
        return 1;
    }

    /**
     * This function checks if the user is admin or not
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $_SESSION['isAdmin'] === 1;
    }

    /**
     * This method handles the lifetime of the session, when is called it compares
     * the timestamp value of the last interaction with the current timestamp,
     * if it has passed more time than the lifetime value (specified in the configuration file),
     * then logout is called, otherwise the value of the last iteration is updated
     * @return void
     */
    public function checkSessionLifetime(): void
    {
        global $logger;

        if ($this->isLogged()) {
            $currentInteraction = time();
            if (($currentInteraction - $_SESSION['lastInteraction']) > $this->lifetime) {
                $logger->writeLog('INFO', "the session for the user: " . $_SESSION['email'] . " is expired");
                header('Location: //' . SERVER_ROOT . '/php/logout.php');
                exit;
            } else {
                $_SESSION['lastInteraction'] = $currentInteraction;
            }
        }
    }

}

