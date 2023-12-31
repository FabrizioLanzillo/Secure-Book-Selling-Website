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
            <!-- This is the container for the first button and it can be: -->
            <!-- The Profile button for every page of a logged user/admin-->
            <div class="container button-container">
<?php
                if(isLogged()){
?>
                    <a href="//<?php echo SERVER_ROOT. '/php/profile.php'?>">
                        <button class="button_header">
                            Hello, <?php echo $_SESSION['name']?>
                        </button>
                    </a>
<?php
                }
?>
            </div>

            <!-- This is the container for the second button and it can be: -->
            <!-- The Orders Button for all the user pages -->
            <div class="container button-container">
<?php
                if(isLogged() and $_SESSION['isAdmin'] == 0){
?>
                        <a href="//<?php echo SERVER_ROOT. '/php/user/orders.php'?>">
                            <button class="button_header">
                                Orders
                            </button>
                        </a>
<?php
                }
?>
            </div>
            <div class="container flex-container"></div>
            <div class="container logo-container">
<?php
                if(isLogged() and ($_SESSION['isAdmin'] == 1)){
?>
                    <a href="//<?php echo SERVER_ROOT. '/php/admin/homeAdmin.php'?>">
<?php
                }
                else{
?>
                    <a href="//<?php echo SERVER_ROOT. '/'?>">
<?php
                }
?>
                        <img src="./../../img/book_selling_logo.png" alt="logo">
                    </a>
            </div>
            <div class="container flex-container"></div>

            <!-- This is the container for the third button and it can be: -->
            <!-- The cart Button for every page of the user and for the anonymous user -->
            <div class="container button-container">
<?php
                if((!isLogged() and (strcmp($currentFile, SERVER_ROOT.'/index.php') == 0)) or
                    ((isLogged()) and ($_SESSION['isAdmin'] == 0))){
?>
                        <a href="//<?php echo SERVER_ROOT. '/php/user/shoppingCart.php'?>">
                            <button class="button_header">
                                Shopping Cart
                            </button>
                        </a>
<?php
                }
?>
            </div>

            <!-- This is the container for the fourth button and it can be: -->
            <!-- The login Button for every page of an anonymous user, with the exception for the login page -->
            <!-- The signup Button for the login page -->
            <!-- The logout Button for every other page -->
            <div class="container button-container">
<?php
                if(!isLogged()){
                    if(strcmp($currentFile, SERVER_ROOT.'/php/login.php') != 0){
?>
                        <a href="//<?php echo SERVER_ROOT. '/php/login.php'?>">
                            <button class="button_header">
                                Log In
                            </button>
                        </a>
<?php
                    }
                    else{
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
