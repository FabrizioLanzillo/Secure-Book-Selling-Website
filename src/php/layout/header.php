<?php
    $currentFile = SERVER_ROOT.$_SERVER['SCRIPT_NAME'];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="./../../css/header.css">
        <title>Book Selling Header</title>
    </head>
    <body>
        <header>
<?php	
            if(!isset($_SESSION)){
                session_start();
            }
?>
            <!-- This is the container for the first button and it can be -->
            <!-- The Index page for not logged user -->
            <!-- The User home page for logged user -->
            <!-- The Admin home page for logged admin -->
            <div class="container button-container">
<?php
                if(!isLogged()){
               
?>
                    <a href="//<?php echo SERVER_ROOT. '/index.php'?>">
<?php
                }
                else{
                    if($_SESSION['isAdmin'] == 0){
?>
                        <a href="//<?php echo SERVER_ROOT. '/php/user/homeUser.php'?>">
<?php
                    }
                    else{
?>
                        <a href="//<?php echo SERVER_ROOT. '/php/admin/homeAdmin.php'?>">
<?php                        
                    }
                }
?>      
                        <button class="button_header">
                            Home
                        </button>
                    </a>
            </div>
            <!-- This is the container for the second button and it can be -->
            <!-- The Profile Button for every page, excluding the login and the signup page -->
            <div class="container button-container">
<?php
                if(isLogged()){
               
?>
                    <a href="//<?php echo SERVER_ROOT. '/php/profile.php'?>">
                        <button class="button_header">
                            Profile
                        </button>
                    </a>
<?php
                }
?>
            </div>

            <div class="container flex-container"></div>
            <div class="container logo-container">
                <h1>Book Selling</h1>
            </div>
            <div class="container flex-container"></div>
            <!-- This is the container for the third button and it can be -->
            <!-- The Signup Button for index page -->
            <!-- The cart Button for every page of the user -->
            <!-- The  Button for every page of the admin -->
            <div class="container button-container">
<?php
                if(!isLogged()){
                    if((strcmp($currentFile, SERVER_ROOT.'/index.php') == 0)){                     
?>
                        <a href="//<?php echo SERVER_ROOT. '/php/signup.php'?>">
                            <button class="button_header">
                                Sign Up
                            </button>
                        </a>
<?php
                    }
                }
                else{
                    if($_SESSION['isAdmin'] == 0){
?>
                        <a href="//<?php echo SERVER_ROOT. '/php/user/shoppingCart.php'?>">
                            <button class="button_header">
                                Shopping Cart
                            </button>
                        </a>
<?php
                    }
                    else{
?>
                        <a>
                            <button class="button_header">
                                TODEFINE
                            </button>
                        </a>
<?php
                    }
                }
?>
            </div>
            <!-- This is the container for the fourth button and it can be -->
            <!-- The login Button for index page -->
            <!-- The signup Button for the login page -->
            <!-- The logout Button for every other page -->
            <div class="container button-container">
<?php
                if(!isLogged()){
                    if((strcmp($currentFile, SERVER_ROOT.'/index.php') == 0) || 
                        (strcmp($currentFile, SERVER_ROOT.'/php/signup.php') == 0)){                  
?>
                        <a href="//<?php echo SERVER_ROOT. '/php/login.php'?>">
                            <button class="button_header">
                                Log In
                            </button>
                        </a>
<?php
                    }
                    elseif((strcmp($currentFile, SERVER_ROOT.'/php/login.php') == 0)){
?>
                        <a href="//<?php echo SERVER_ROOT. '/php/signup.php'?>">
                            <button class="button_header">
                                Sign Up
                            </button>
                        </a>
<?php                        
                    }
                }
                else{
?>
                    <a href="//<?php echo SERVER_ROOT. '/php/logout.php'?>">
                        <button class="button_header">
                            Log Out
                        </button>
                    </a>
<?php
                }
?>
            </div>
        </header>
    </body>
</html>
