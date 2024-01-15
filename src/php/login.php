<?php
require_once __DIR__ . "/../config.php";

global $logger;
global $errorHandler;
global $sessionHandler;
global $shoppingCartHandler;
global $accessControlManager;

/**
 * @param $email , is the email to select the user
 * @param $password , is the hashed password
 * @param $failedAccesses , is a counter of the failed access of the user
 * @return bool|null
 */
function login($email, $password, $failedAccesses): ?bool
{

    global $logger;
    global $errorHandler;
    global $sessionHandler;

    try {
        if ($email != null && $password != null) {

            $result = authenticate($email, $password);
            // check if an error occurred while performing the query
            if ($result) {
                // check if the query returned a result and exactly 1 row
                $dataQuery = $result->fetch_assoc();
                if ($dataQuery !== null && $result->num_rows === 1) {
                    //Resets the failed accesses counter
                    if (updateFailedAccesses($email, 0)) {
                        // creation of the session variables of a logged user
                        $sessionHandler->setSession($dataQuery['id'], $dataQuery['username'], $email, $dataQuery['name'], $dataQuery['isAdmin']);
                        // generation of a new php session id in order to avoid the session fixation attack
                        session_regenerate_id(true);
                        $logger->writeLog('INFO', "SessionID changed after the login, in order to avoid SESSION FIXATION Attacks");
                        return true;
                    } else {
                        throw new Exception('Something went wrong during the update.');
                    }
                } else {
                    $failedAccesses = $failedAccesses + 1;
                    if (updateFailedAccesses($email, $failedAccesses)) {
                        throw new Exception('Email and/or password are not valid.', $failedAccesses);
                    } else {
                        throw new Exception('Something went wrong during the update.');
                    }
                }
            } else {
                throw new Exception("Error performing the authentication.");
            }
        } else {
            throw new Exception('Error retrieving inserted data.');
        }
    } catch (Exception $e) {
        $errorHandler->handleException($e);
        $errorCode = $e->getCode();
        if ($errorCode > 0) {
            $logger->writeLog('WARNING',
                "Failed Login for the user: " . $email,
                $_SERVER['SCRIPT_NAME'],
                "LoginFunc",
                $e->getMessage() . " The user failed login " . $failedAccesses . " times");
        } else {
            $logger->writeLog('ERROR',
                "Failed Login for the user: " . $email,
                $_SERVER['SCRIPT_NAME'],
                "LoginFunc",
                $e->getMessage());
        }
        return false;
    }
}

// check if the user is logged or not, if the user is logged the access to the login page is forbidden
// and the user will be redirected to the home page
if ($sessionHandler->isLogged()) {
    $accessControlManager->redirectToHome();
}

// this block is executed only after submit of the login POST form
if (checkFormData(['email', 'password'])) {

    // Protect against XSS
    $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $submittedPassword = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
    $logger->writeLog('INFO', "Protection against XSS applied");

    // Protect against XSRF
    if (!$token || $token !== $_SESSION['token']) {
        // return 405 http status code
        $accessControlManager->redirectIfXSRFAttack();
    } else {
        try {
            $logger->writeLog('INFO', "XSRF control passed");
            // retrieve from the db the salt of the user, and the blockAccess information
            $result = getAccessInformation($email);
            // check if an error occurred while performing the query
            if ($result) {
                // check if the query returned a result and exactly 1 row
                $dataQuery = $result->fetch_assoc();
                if ($dataQuery !== null && $result->num_rows === 1) {

                    // check if the user account is blocked, due to a suspect of brute force attack
                    if ($dataQuery['blockedUntil'] !== null) {
                        $blockedTime = strtotime($dataQuery['blockedUntil']);
                        $currentTime = time();
                        // check if the account is still blocked or enough time is passed
                        if (($currentTime - $blockedTime) < 0)
                            throw new Exception('Your account is currently blocked');
                    }

                    // Reconstruction of the hashed password by encrypting the concatenation of
                    // the password provided by the user and the salt taken from the db
                    $hashedPassword = hash('sha256', $submittedPassword . $dataQuery['salt']);

                    if (login($email, $hashedPassword, $dataQuery['failedAccesses'])) {
                        $logger->writeLog('INFO', "Login of the user: " . $email . ", Succeeded");
                        // the shopping cart in the db is updated if the user has insert something in it
                        // while he was anonymous
                        $shoppingCartHandler->checkAndUpdateShoppingCartDB();
                        $accessControlManager->redirectToHome();
                    }
                } else {
                    throw new Exception("No Account found for the given email");
                }
            } else {
                throw new Exception('Error retrieving access information');
            }
        } catch (Exception $e) {
            $errorHandler->handleException($e);
            $logger->writeLog('ERROR',
                "Failed Login for the user: " . $email,
                $_SERVER['SCRIPT_NAME'],
                $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <title>Book Selling - Login</title>
</head>
<body>
<?php
include "./layout/header.php";
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="login-container">
                <h2 class="text-center">Login</h2>
                <form name="login" action="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/login.php'); ?>"
                      method="POST">
                    <div class="form-group">
                        <label for="email"><b>Email</b></label>
                        <input class="form-control" type="text" placeholder="Enter Email" name="email" required>
                    </div>

                    <div class="form-group mb-5">
                        <label for="password"><b>Password</b></label>
                        <input class="form-control" type="password" placeholder="Enter Password" name="password"
                               required>
                    </div>

                    <!-- Hidden token to protect against XSRF -->
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">

                    <button class="btn btn-primary btn-block" type="submit">Login</button>
                </form>
                <a href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/otpRequest.php') ?>"
                   class="forgot-pwd d-block text-center mt-3">Forgot Password?</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
