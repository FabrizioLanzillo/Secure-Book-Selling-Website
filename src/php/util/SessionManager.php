<?php
class SessionManager {
    private static ?SessionManager $instance = null;
    private $lifetime = null;
    private $path = null;
    private $secure = null;
    private $httponly = null;

    private function __construct($lifetime, $path, $secure, $httponly) {
        $this->lifetime = $lifetime;
        $this->path = $path;
        $this->secure = $secure;
        $this->httponly = $httponly;
        $this->startSession();
    }

    public static function getInstance($lifetime, $path, $secure, $httponly): ?SessionManager{
        if (self::$instance == null) {
            self::$instance = new SessionManager($lifetime, $path, $secure, $httponly);
        }

        return self::$instance;
    }

    private function startSession(): void{

        session_set_cookie_params([
            'lifetime' => $this->lifetime,
            'path' => $this->path,
            'domain' => $_SERVER['HTTP_HOST'],
            'secure' => $this->secure,
            'httponly' => $this->httponly
        ]);

        session_start();
    }

    public function setSession($userId, $username, $name, $isAdmin): void {
        $_SESSION['userId'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['name'] = $name;
        $_SESSION['isAdmin'] = $isAdmin;
    }

    public function unsetSession(): bool {
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

    public function isLogged(): int {
        if(isset($_SESSION['userId']) && isset($_SESSION['username']) && isset($_SESSION['name']) && isset($_SESSION['isAdmin'])){
            return 1;
        }
        else{
            return 0;
        }
    }
}

