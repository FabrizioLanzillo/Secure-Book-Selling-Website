<?php

    require_once __DIR__ . "./../config.php";
    require_once __DIR__ . "/util/dbInteraction.php";

    global $logger;

    function login ($email, $password): ?string{

        global $logger;

        if($email != null && $password != null){

            $resultQuery = authenticate($email, $password);
            if($resultQuery !== false){

                if($resultQuery !== null && extract($resultQuery) == 4){
                    // creation of the session variables
                    setSession($id, $username, $name, $isAdmin);
                    // generation of a new php session id in order to avoid the session fixation attack
                    session_regenerate_id(true);
                    $logger->writeLog('INFO', "SessionID changed in order to avoid Session Fixation attacks ");

                    return null;
                }
                else{
                    $logger->writeLog('ERROR', "Email and/or password of the user: ".$email.", are not valid.", $_SERVER['SCRIPT_NAME'], "LoginFunc");
                    return 'Email and/or password are not valid, please try again';
                }
            }
            else{
                return "Error performing the authentication";
            }
        }
        else{
            return 'Error retrieving inserted data';
        }
    }

    // check if the user is logged or not, if the user is logged, it can't access to the login page
    if(isLogged()){
        if($_SESSION['isAdmin'] == '0'){
            header('Location: //'.SERVER_ROOT.'/index.php');
            exit;
        }
        else{
            header('Location: //'.SERVER_ROOT.'/php/admin/homeAdmin.php');
            exit;
        }
    }

    $error = null;

    // this block is executed only after the submit of the POST form
    if(isset($_POST['email']) && isset($_POST['password'])){
    	
        $email = $_POST['email'];
        // retrieve from the db the salt of the user
        $salt = getAccessInformation($email);
        if($salt !== false) {
            // hash 256 enc of the password concatenated with the salt
            $password = hash('sha256', $_POST['password'] . $salt);
            $error = login($email, $password);

            if ($error === null) {
                $logger->writeLog('INFO', "Login of the user: ".$email.", Succeeded");

                if ($_SESSION['isAdmin'] == '0') {
                    header('Location: //' . SERVER_ROOT . '/index.php');
                    exit;
                } else {
                    header('Location: //' . SERVER_ROOT . '/php/admin/homeAdmin.php');
                    exit;
                }
            }
        }
        else{
            return "Error retrieving access information";
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
            <form name = "login" action="//<?php echo SERVER_ROOT. '/php/login.php'?>" method="POST">
                <label><b>Email</b>
                    <input class="login_form_input" type="text" placeholder="Enter Email" name="email" required>
                </label>

                <label><b>Password</b>
                    <input class="login_form_input" type="password" placeholder="Enter Password" name="password" required>
                </label>

                <button class="login_form_button" type="submit">Login</button>
            </form>
        </div>
        <?php
            if ($error !== null){
                echo '<script>
                         alert("'.$error.'");
                        //   window.location.assign("//'.SERVER_ROOT.'/php/login.php")
                      </script>';
                    
            }
        ?>
    </body>
</html>
