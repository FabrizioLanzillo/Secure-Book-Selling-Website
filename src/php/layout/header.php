<?php
global $sessionHandler;
$currentFile = htmlspecialchars(SERVER_ROOT . $_SERVER['SCRIPT_NAME']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-5">
    <div class="container">

        <?php if ($sessionHandler->isLogged() and $sessionHandler->isAdmin()){ ?>
        <a class="navbar-brand" href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/admin/homeAdmin.php') ?>">
            <?php }else{ ?>
            <a class="navbar-brand" href="//<?php echo htmlspecialchars(SERVER_ROOT . '/') ?>">
                <?php } ?>
                <img src="./../../img/bookselling.png" alt="logo" class="img-fluid" style="width: 200px; height: auto;">
            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <!-- The Profile button for every page of a logged user/admin-->
                    <?php if ($sessionHandler->isLogged()) { ?>
                        <li class="nav-item">
                            <a class="nav-link"
                               href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/profile.php') ?>">Hello, <?php echo htmlspecialchars($_SESSION['name']) ?></a>
                        </li>
                    <?php } ?>
                    <li class="nav-item">
                        <?php if ($sessionHandler->isLogged() and $sessionHandler->isAdmin()) { ?>
                            <a class="nav-link"
                               href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/admin/homeAdmin.php') ?>">Homepage</a>
                        <?php } else { ?>
                            <a class="nav-link" href="//<?php echo htmlspecialchars(SERVER_ROOT . '/') ?>">Homepage</a>
                        <?php } ?>
                    </li>
                    <!-- The Orders Button for all the user pages -->
                    <?php if ($sessionHandler->isLogged() and !$sessionHandler->isAdmin()) { ?>
                        <li class="nav-item">
                            <a class="nav-link"
                               href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/user/orders.php') ?>">Orders</a>
                        </li>
                    <?php } ?>
                    <!-- The cart Button for every page of the user and for the anonymous user -->
                    <?php if ((!$sessionHandler->isLogged() and (strcmp($currentFile, htmlspecialchars(SERVER_ROOT . '/index.php')) == 0)) or
                        (!$sessionHandler->isLogged() and (strcmp($currentFile, htmlspecialchars(SERVER_ROOT . '/php/book_details.php')) == 0)) or
                        (($sessionHandler->isLogged()) and (!$sessionHandler->isAdmin()))) { ?>
                        <li class="nav-item">
                            <a class="nav-link"
                               href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/user/shoppingCart.php') ?>">Shopping
                                Cart</a>
                        </li>
                    <?php } ?>
                    <!-- The login Button for every page of an anonymous user, with the exception for the login page -->
                    <!-- The signup Button for the login page -->
                    <!-- The logout Button for every other page -->
                    <?php if (!$sessionHandler->isLogged()) {
                        if (strcmp($currentFile, htmlspecialchars(SERVER_ROOT . '/php/login.php')) != 0) { ?>
                            <li class="nav-item">
                                <a class="nav-link"
                                   href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/login.php') ?>">Log In</a>
                            </li>
                        <?php } else { ?>
                            <li class="nav-item">
                                <a class="nav-link"
                                   href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/signup.php') ?>">Sign Up</a>
                            </li>
                        <?php }
                    } else { ?>
                        <li class="nav-item">
                            <a class="nav-link"
                               href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/logout.php') ?>">Log Out</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
    </div>
</nav>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>



