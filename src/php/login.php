<?php

	session_start();
	require_once __DIR__ . "/util/dbInteraction.php";
    require_once __DIR__ . "/util/sessionManager.php";
    require_once __DIR__ . "./../config.php";

    echo "<b>Test Connection to the DB:</b><br>";


    function login ($email, $password){
        if($email != null && $password != null){
            
            $resultQuery = authenticate($email, $password);
            if($resultQuery !== false){

                if($resultQuery !== null && extract($resultQuery) == 4){	
                    if(!isset ($_SESSION)){
                        session_start();
                    }
                    echo "id: ".$id."<br>";
                    echo "username: ".$username."<br>";
                    echo "name: ".$name."<br>";
                    echo "isAdmin: ".$isAdmin."<br>";
                    setSession($id, $username, $name, $isAdmin);
                    return null;
                }
                else{
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

    $error = null;

    if(isset($_POST['email']) && isset($_POST['password'])){
    	
        $email = $_POST['email'];
    	$password = $_POST['password'];
        
        $error = login("f.lanzillo@studenti.unipi.it", "prova");
    }

    if ($error !== null){
        echo '<script>
                 alert("'.$error.'");
                //   window.location.assign("//'.SERVER_ROOT.'/php/login.php")
              </script>';
            
    }
    
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../css/login.css">
    </head>
    <body>
        <h2>Login Form</h2>
        <form name = "login" action="//<?php echo SERVER_ROOT. '/php/login.php'?>" method="POST">
            <div class="container">
                <label for="email"><b>Email</b></label>
                <input type="text" placeholder="Enter Email" name="email" required>

                <label for="password"><b>Password</b></label>
                <input type="password" placeholder="Enter Password" name="password" required>

                <button type="submit">Login</button>
            </div>
        </form>
    </body>
</html>
