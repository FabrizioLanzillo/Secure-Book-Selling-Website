<?php
require_once __DIR__ . "/../config.php";

global $logger;
global $errorHandler;
global $sessionHandler;
global $shoppingCartHandler;
global $accessControlManager;
global $numberLoginAttempt;
global $timeWindowDuration;

/**
 * Login function that checks the credential and checks if brute force attack on access credentials
 * @param $email , is the email to select the user
 * @param $password , is the hashed password
 * @param $timestampAccess , timestamp of the first failed access or the last successful access
 * @param $failedAccesses , is a counter of the failed access of the user
 * @param $blockedTime , Time in seconds while the user is blocked
 * @return bool|null
 */
function login($email, $password, $timestampAccess, $failedAccesses, $blockedTime): ?bool
{

    global $logger;
    global $errorHandler;
    global $sessionHandler;
    global $numberLoginAttempt;
    global $timeWindowDuration;

    try {
        if ($email != null && $password != null) {

            $result = authenticate($email, $password);
            // check if an error occurred while performing the query
            if ($result) {
                // check if the query returned a result and exactly 1 row
                $dataQuery = $result->fetch_assoc();
                if ($dataQuery !== null && $result->num_rows === 1) {
                    //Resets the failed accesses counter
                    $information = updateBlockLoginInformation(0, 0, $email);
                    if (updateFailedAccesses($information)) {
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
                    // In this case the credentials given by the user are incorrect, however, it must be checked that
                    // no brute force attack attempt is in progress.
                    // To verify and defend against a possible brute force attack attempt on login credentials,
                    // it is required that the number of failed logins, in a 30-second time window, must not exceed $numberLoginAttempt.

                    // Check if there is a timestamp of the first failed access
                    $timestampAccess = ($timestampAccess === null) ? 0 : strtotime($timestampAccess);
                    // if we are in the 30-sec time window OR
                    // this is the first failed access because there is not a timestamp of a first failed access
                    // the counter of the failed access is checked
                    if (time() - $timestampAccess < $timeWindowDuration or $timestampAccess === 0 or $failedAccesses === 0) {
                        $failedAccesses = $failedAccesses + 1;
                        if ($failedAccesses >= $numberLoginAttempt) {
                            $information = updateBlockLoginInformation(0, ($blockedTime === 0) ? 30 : $blockedTime * 2, $email);
                        } else {
                            $information = updateBlockLoginInformation($failedAccesses, $blockedTime, $email);
                        }
                        if (!updateFailedAccesses($information)) {
                            throw new Exception('Something went wrong during the update of the security access information.');
                        }
                    }
                    throw new Exception('Email and/or password are not valid.', $failedAccesses);
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
    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $submittedPassword = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Protect against XSRF
    if (!$token || $token !== $_SESSION['token']) {
        // return 405 http status code
        $accessControlManager->redirectIfXSRFAttack();
    } else {
        try {
            // retrieve from the db the salt of the user, and the blockAccess information
            $result = getAccessInformation($email);
            // check if an error occurred while performing the query
            if ($result) {
                // check if the query returned a result and exactly 1 row
                $dataQuery = $result->fetch_assoc();
                if ($dataQuery !== null && $result->num_rows === 1) {

                    // check if the user account is blocked, due to a suspect of brute force attack
                    if ($dataQuery['blockedTime'] !== 0 and $dataQuery['failedAccesses'] === 0) {
                        $blockedTime = $dataQuery['blockedTime'] + strtotime($dataQuery['timestampAccess']);
                        $currentTime = time();
                        // check if the account is still blocked or enough time is passed
                        if (($currentTime - $blockedTime) < 0)
                            throw new Exception('Your account is currently blocked');
                    }

                    // Reconstruction of the hashed password by encrypting the concatenation of
                    // the password provided by the user and the salt taken from the db
                    $hashedPassword = hash('sha256', $submittedPassword . $dataQuery['salt']);

                    if (login($email, $hashedPassword, $dataQuery['timestampAccess'], $dataQuery['failedAccesses'], $dataQuery['blockedTime'])) {
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
