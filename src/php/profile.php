<?php
    require_once __DIR__ . "./../config.php";
    require_once __DIR__ . "/util/dbInteraction.php";
?>

<?php
global $arr;

if(isLogged()){
    $username = $_SESSION['username'];

    $resultQuery = getUserData($username);

    //TODO teoricamente si può togliere
    if($resultQuery !== false)
        $arr = $resultQuery->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- <link rel="stylesheet" type="text/css" href="../css/profile.css"> -->
    <link rel="stylesheet" type="text/css" href="../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Book Selling - Profile</title></head>
<body>
<?php
include "./layout/header.php";
?>

<div class="d-flex">

<?php if($_SESSION['isAdmin']==1): ?>
    <aside class="d-flex flex-column flex-shrink-0 p-3 bg-light" style="width: 20rem;">
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="//<?php echo SERVER_ROOT. '/php/admin/homeAdmin.php'?>" class="nav-link link-dark" >
                    <i class="fas fa-book"></i>
                    Books
                </a>
            </li>
            <li>
                <a href="//<?php echo SERVER_ROOT. '/php/admin/orderList.php'?>" class="nav-link link-dark">
                    <i class="fas fa-list"></i>
                    Orders
                </a>
            </li>
            <li>
                <a href="//<?php echo SERVER_ROOT. '/php/admin/customerList.php'?>" class="nav-link link-dark">
                    <i class="fas fa-users"></i>
                    Customers
                </a>
            </li>
            <li class="nav-item">
                <a href="//<?php echo SERVER_ROOT. '/php/profile.php'?>" class="nav-link active" aria-current="page">
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
            $isAdmin = isset($_SESSION['isAdmin']) ? $_SESSION['isAdmin'] : 0;

            $imagePath = ($isAdmin == 0) ? "../img/avatar.png" : "../img/federiho.png";
            ?>
            <img src="<?php echo $imagePath; ?>" alt="Profile Picture" class="img-fluid rounded-circle img-thumbnail">
        </div>
        <div class="col-md-4">
            <form>
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" id="name" readonly="readonly" placeholder="<?php echo isset($arr['name']) ? $arr['name'] : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="surname">Surname:</label>
                    <input type="text" class="form-control" id="surname" readonly="readonly" placeholder="<?php echo isset($arr['surname']) ? $arr['surname'] : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" readonly="readonly" placeholder="<?php echo isset($arr['username']) ? $arr['username'] : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="date_birth">Date of birth:</label>
                    <input type="text" class="form-control" id="date_birth" readonly="readonly" placeholder="<?php echo isset($arr['date_of_birth']) ? $arr['date_of_birth'] : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" readonly="readonly" placeholder="<?php echo isset($arr['email']) ? $arr['email'] : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" readonly="readonly" placeholder="*******************">
                </div>
                <button id="change-pwd" type="button" class="btn btn-primary onclick="enablePasswordChange()">Change Password</button>
                <button id="save-pwd" type="button" class="btn btn-primary" style="display: none" onclick="disablePasswordChange()">Save Password</button>
            </form>
        </div>
    </div>
</main>

</div>

<!--
<script>
    function enablePasswordChange(){
        document.getElementById("change-pwd").disable();
        document.getElementById("save-pwd").style.display = 'display';
    }

    function disablePasswordChange(){
        document.getElementById("change-pwd").enable();
        document.getElementById("save-pwd").classList.add("d-none");
    }

</script>


<footer class="bg-dark text-white text-center py-3 mt-4">
    <p>&copy; 2023 Book Store Online. Tutti i diritti riservati.</p>
</footer>
-->
</body>
</html>