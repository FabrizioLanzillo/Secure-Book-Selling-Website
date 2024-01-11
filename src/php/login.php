<?php

require_once __DIR__ . "/../config.php";

global $logger;
global $errorHandler;
global $sessionHandler;
global $shoppingCartHandler;

function login($email, $password, $failedAccesses): ?bool{

    global $logger;
    global $errorHandler;
    global $sessionHandler;

    try {
        if ($email != null && $password != null) {
            
            $resultQuery = authenticate($email, $password);
            if ($resultQuery !== false) {

                if ($resultQuery !== null && extract((array)$resultQuery) == 4) {
                    //Resets the failed accesses
                    updateFailedAccesses($email, 0);
                    // creation of the session variables
                    $sessionHandler->setSession($id, $username, $email, $name, $isAdmin);
                    // generation of a new php session id in order to avoid the session fixation attack
                    session_regenerate_id(true);
                    $logger->writeLog('INFO', "SessionID changed in order to avoid Session Fixation attacks");

                    return true;
                }
                else {
                    $failedAccesses = $failedAccesses + 1;
                    if (updateFailedAccesses($email, $failedAccesses)) {
                        throw new Exception('Email and/or password are not valid.', $failedAccesses);
                    } else {
                        throw new Exception('Something went wrong.');
                    }
                }
            }
            else {
                throw new Exception("Error performing the authentication.");
            }
        }
        else {
            throw new Exception('Error retrieving inserted data.');
        }
    }
    catch (Exception $e) {
        $errorHandler->handleException($e);
        $errorCode = $e->getCode();
        if ($errorCode > 0) {
            $logger->writeLog('ERROR',
                "Failed Login for the user: " . $email,
                $_SERVER['SCRIPT_NAME'],
                "LoginFunc",
                $e->getMessage() . " The user failed login " . $failedAccesses . " times");
        }
        else{
            $logger->writeLog('ERROR',
                "Failed Login for the user: " . $email,
                $_SERVER['SCRIPT_NAME'],
                "LoginFunc",
                $e->getMessage());
        }
        return false;
    }
}

// check if the user is logged or not, if the user is logged, it can't access to the login page
if ($sessionHandler->isLogged()) {
    if ($_SESSION['isAdmin'] == '0') {
        header('Location: //' . SERVER_ROOT . '/');
        exit;
    } else {
        header('Location: //' . SERVER_ROOT . '/php/admin/homeAdmin.php');
        exit;
    }
}

// this block is executed only after submit of the POST form
if (isset($_POST['email']) && isset($_POST['password'])) {

    $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');

    if (!$token || $token !== $_SESSION['token']) {
        // return 405 http status code
        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
        exit;
    } else {
        try {
            // Protect against XSS
            $email = htmlspecialchars($_POST['eamil'], ENT_QUOTES, 'UTF-8');
            $rawPassword = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
            // retrieve from the db the salt of the user
            $result = getAccessInformation($email);
    
            if ($result['blockedUntil'] !== null){
                $blockedTime = strtotime($result['blockedUntil']);
                $currentTime = time();
                if (($currentTime-$blockedTime) < 0)
                    throw new Exception('Your account is currently blocked');
            }
                    
            if ($result['salt'] !== false) {
                // hash 256 enc of the password concatenated with the salt
                $password = hash('sha256', $rawPassword . $result['salt']);
    
                if (login($email, $password, $result['failedAccesses'])) {
                    $logger->writeLog('INFO', "Login of the user: " . $email . ", Succeeded");
                    $shoppingCartHandler->checkAndUpdateShoppingCartDB();
    
                    if ($_SESSION['isAdmin'] == '0') {
                        header('Location: //' . SERVER_ROOT . '/');
                        exit;
                    } else {
                        header('Location: //' . SERVER_ROOT . '/php/admin/homeAdmin.php');
                        exit;
                    }
                }
            } else {
                throw new Exception('Error retrieving access information');
            }
        } catch (Exception $e) {
            $errorHandler->handleException($e);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="../css/login.css">
        <title>Book Selling</title>
    </head>
    <body>
        <?php
        include "./layout/header.php";
        ?>

        <div class="login_container">
            <h2>Login</h2>
            <form name="login" action="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/login.php'); ?>" method="POST">
                <label><b>Email</b>
                    <input class="login_form_input" type="text" placeholder="Enter Email" name="email" required>
                </label>

                <label><b>Password</b>
                    <input class="login_form_input" type="password" placeholder="Enter Password" name="password" required>
                </label>

                <!-- Hidden token to protect against CSRF -->
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">

                <button class="login_form_button" type="submit">Login</button>
            </form>
            <a href="//<?php echo htmlspecialchars(SERVER_ROOT. '/php/otp_request.php'); ?>" class="forgot-pwd">Forgot Password?</a>
        </div>
    </body>
</html>
