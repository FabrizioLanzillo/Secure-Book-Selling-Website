<?php
$currentFile = SERVER_ROOT.$_SERVER['SCRIPT_NAME'];
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light rounded">
    <a class="navbar-brand">
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
        </a>


        <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarsExample09" aria-controls="navbarsExample09" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="navbar-collapse collapse" id="navbarsExample09" >
            <ul class="navbar-nav mr-auto">
                <!-- This is the container for the first button and it can be: -->
                <!-- The Profile button for every page of a logged user/admin-->
                <?php
                if(isLogged()){
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="//<?php echo SERVER_ROOT. '/php/profile.php'?>">
                            <button class="btn btn-primary">
                                Hello, <?php echo $_SESSION['name']?>
                            </button>
                        </a>
                    </li>
                    <?php
                }
                ?>
                <!-- This is the container for the second button and it can be: -->
                <!-- The Orders Button for all the user pages -->
                <?php
                if(isLogged() and $_SESSION['isAdmin'] == 0){
                    ?>
                    <li class="nav-item">
                        <a href="//<?php echo SERVER_ROOT. '/php/user/orders.php'?>">
                            <button class="btn btn-primary">
                                Orders
                            </button>
                        </a>
                    </li>
                    <?php
                }
                ?>
                <!-- This is the container for the third button and it can be: -->
                <!-- The cart Button for every page of the user and for the anonymous user -->
                <?php
                if((!isLogged() and (strcmp($currentFile, SERVER_ROOT.'/index.php') == 0)) or
                    ((isLogged()) and ($_SESSION['isAdmin'] == 0))){
                    ?>
                    <li class="nav-item">
                        <a href="//<?php echo SERVER_ROOT. '/php/user/shoppingCart.php'?>">
                            <button class="btn btn-primary">
                                Shopping Cart
                            </button>
                        </a>
                    </li>
                    <?php
                }
                ?>
                <!-- This is the container for the fourth button and it can be: -->
                <!-- The login Button for every page of an anonymous user, with the exception for the login page -->
                <!-- The signup Button for the login page -->
                <!-- The logout Button for every other page -->
                <?php
                if(!isLogged()){
                    if(strcmp($currentFile, SERVER_ROOT.'/php/login.php') != 0){
                        ?>
                        <li class="nav-item">
                            <a href="//<?php echo SERVER_ROOT. '/php/login.php'?>">
                                <button class="btn btn-primary">
                                    Log In
                                </button>
                            </a>
                        </li>
                        <?php
                    }
                    else{
                        ?>
                        <li class="nav-item">
                            <a href="//<?php echo SERVER_ROOT. '/php/signup.php'?>">
                                <button class="btn btn-primary">
                                    Sign Up
                                </button>
                            </a>
                        </li>
                        <?php
                    }
                }
                else{
                    ?>
                    <li class="nav-item">
                        <a href="//<?php echo SERVER_ROOT. '/php/logout.php'?>">
                            <button class="btn btn-primary">
                                Log Out
                            </button>
                        </a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
</nav>
