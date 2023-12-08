<?php

	session_start();
    require_once __DIR__ . "./../config.php";
    require_once __DIR__ . "/util/dbInteraction.php";

    function login ($email, $password): ?string{
        global $logger;
        global $debug;

        if($email != null && $password != null){

            $resultQuery = authenticate($email, $password);
            if($resultQuery !== false){

                if($resultQuery !== null && extract($resultQuery) == 4){	
                    if(!isset ($_SESSION)){
                        session_start();
                    }

                    setSession($id, $username, $name, $isAdmin);
                    $message = "Login of the user: ".$email.", Succeeded";
                    $logger->writeLog('INFO', $message);
                    return null;
                }
                else{
                    $file = $debug ? "[File: ".$_SERVER['SCRIPT_NAME']."] " : "";
                    $errorCode = $debug ? "[Error: LoginFunc]" : "";
                    $message = $file . $errorCode . " - Email and/or password of the user: ".$email.", are not valid";
                    $logger->writeLog('ERROR', $message);
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

    if(isLogged()){
        if($_SESSION['isAdmin'] == '0'){
            header('Location: //'.SERVER_ROOT.'index.php');
            exit;
        }
        else{
            header('Location: //'.SERVER_ROOT.'/php/admin/homeAdmin.php');
            exit;
        }
    }

    $error = null;

    if(isset($_POST['email']) && isset($_POST['password'])){
    	
        $email = $_POST['email'];
        $salt = getAccessInformation($email);
        if($salt !== false) {

            $password = hash('sha256', $_POST['password'] . $salt);
            $error = login($email, $password);

            if ($error === null) {
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
        <h2>Login Form</h2>
        <form name = "login" action="//<?php echo SERVER_ROOT. '/php/login.php'?>" method="POST">
            <div class="container">
                <label><b>Email</b>
                    <input class="login_form_input" type="text" placeholder="Enter Email" name="email" required>
                </label>

                <label><b>Password</b>
                    <input class="login_form_input" type="password" placeholder="Enter Password" name="password" required>
                </label>

                <button class="login_form_button" type="submit">Login</button>
            </div>
        </form>
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
