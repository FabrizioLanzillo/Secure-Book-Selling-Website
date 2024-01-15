<?php
require_once __DIR__ . "/../config.php";

global $sessionHandler;
global $accessControlManager;

// Check path manipulation and broken access control
// Check if the user is logged
$accessControlManager->redirectIfAnonymous();

// Retrieves user's data from the db
$result = getUserData($_SESSION['username']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <title>Book Selling - Profile</title></head>
<body>
<?php
include "./layout/header.php";
?>

<div class="d-flex">

    <?php if ($sessionHandler->isAdmin()): ?>
        <aside class="d-flex flex-column flex-shrink-0 p-3 bg-light" style="width: 20rem;">
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/admin/homeAdmin.php'); ?>"
                       class="nav-link link-dark">
                        <i class="fas fa-book"></i>
                        Books
                    </a>
                </li>
                <li>
                    <a href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/admin/orderList.php'); ?>"
                       class="nav-link link-dark">
                        <i class="fas fa-list"></i>
                        Orders
                    </a>
                </li>
                <li>
                    <a href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/admin/customerList.php'); ?>"
                       class="nav-link link-dark">
                        <i class="fas fa-users"></i>
                        Customers
                    </a>
                </li>
                <li class="nav-item">
                    <a href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/profile.php'); ?>"
                       class="nav-link active" aria-current="page">
                        <i class="fas fa-user"></i>
                        Admin
                    </a>
                </li>
            </ul>
            <hr>
        </aside>
    <?php endif; ?>

    <main class="container mt-4">
        <div class="d-flex justify-content-center mt-4">
            <div class="col-md-3">
                <?php
                $imagePath = !$sessionHandler->isAdmin() ? "../img/avatar.png" : "../img/federiho.png";
                ?>
                <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Profile Picture"
                     class="img-fluid rounded-circle img-thumbnail">
            </div>
            <?php
            if ($result) {
                $user = $result->fetch_assoc();
                if ($user !== null && $result->num_rows === 1) {
            ?>
            <div class="col-md-4">
                <form>
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control" id="name" readonly="readonly"
                               placeholder="<?php echo htmlspecialchars($user['name']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="surname">Surname:</label>
                        <input type="text" class="form-control" id="surname" readonly="readonly"
                               placeholder="<?php echo htmlspecialchars($user['surname']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" class="form-control" id="username" readonly="readonly"
                               placeholder="<?php echo htmlspecialchars($user['username']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" readonly="readonly"
                               placeholder="<?php echo htmlspecialchars($user['email']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" id="password" readonly="readonly"
                               placeholder="*******************">
                    </div>
                    <!-- Hidden token to protect against CSRF -->
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">
                    <a href="otpRequest.php">
                        <button id="change-pwd" type="button" class="btn btn-primary">Change Password</button>
                    </a>
                </form>
            </div>
                    <?php
                } else {
                    ?>
                    <div class='alert alert-danger mt-4'>User not found in the database</div>
                    <?php
                }
            } else {
                ?>
                <div class='alert alert-danger mt-4'>Error retrieving user details</div>
                <?php
            }
        ?>
        </div>
    </main>

</div>

</body>
</html>