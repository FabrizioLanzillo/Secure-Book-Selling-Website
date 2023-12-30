<?php
    require_once __DIR__ . "./../config.php";
    require_once __DIR__ . "/util/dbInteraction.php";

    function checkFormData() {
        $requiredFields = ['name', 'surname', 'email', 'username', 'password', 'repeat_password', 'birthdate'];
        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                return false;
            }
        }
    
        return true;
    }

    // TABLE: `user` (`id`, `username`, `password`, `salt`, `email`, `name`, `surname`, `date_of_birth`, `isAdmin`)
    // We need an unique email and username
    function signup($Data){

        global $logger;

        $insertUser = insertUser($Data);
        if($insertUser == false) {
            return "Couldn't register the user";
        }
    }

    $error = null;

    // this block is executed only after the submit of the POST form
    if(checkFormData()){
        if ($_POST['password'] !== $_POST['repeat_password'])
                $error = "The inserted passwords don't match";
        if($error === null) {
                $salt = bin2hex(random_bytes(32));
                $hashedPassword = hash('sha256', $_POST['password'] . $salt);
                $userData = array(
                    "username" => $_POST['username'],
                    "password" => $hashedPassword,
                    "salt" => $salt,
                    "email" => $_POST['email'],
                    "name" => $_POST['name'],
                    "surname" => $_POST['surname'],
                    "birthdate" => $_POST['birthdate'],
                );
                //var_dump($userData);
                $error = signup($userData);
                if ($error === null) {
                        $logger->writeLog('INFO', "Signup of the user: ".$userData['email'].", Succeeded");
                        header('Location: //' . SERVER_ROOT . '/php/login.php');
                        exit;
                }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="../css/signup.css">
        <title>Book Selling - Sign Up</title>
    </head>
    <body>
        <?php
            include "./layout/header.php";
        ?>

        <div class="signup_container">
            <h2>Sign up</h2>
            <form name = "sign_up" action="//<?php echo SERVER_ROOT. '/php/signup.php'?>" method="POST">
                <label><b>Name</b>
                    <input class="signup_form_input" type="text" placeholder="Name" name="name" required>
                </label>

                <label><b>Surname</b>
                    <input class="signup_form_input" type="text" placeholder="Surname" name="surname" required>
                </label>

                <label><b>Email</b>
                    <input class="signup_form_input" type="text" placeholder="Email" name="email" required>
                </label>

                <label><b>Username</b>
                    <input class="signup_form_input" type="text" placeholder="Username" name="username" required>
                </label>

                <label><b>Password</b>
                    <input class="signup_form_input" type="password" placeholder="Password" name="password" required>
                </label>

                <label><b>Repeat password</b>
                    <input class="signup_form_input" type="password" placeholder="Repeat Password" name="repeat_password" required>
                </label>

                <label><b>Date of birth</b>
                    <input class="signup_form_input" type="date" name="birthdate" required>
                </label>

                <button class="signup_form_button" type="submit">Sign up</button>
            </form>
        </div>
        <?php
            if ($error !== null){
                echo '<script>
                         alert("'.$error.'");
                        //   window.location.assign("//'.SERVER_ROOT.'/php/signup.php")
                      </script>';    
            }
        ?>
    </body>
</html>