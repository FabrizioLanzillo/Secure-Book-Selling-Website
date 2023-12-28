<?php
    require_once __DIR__ . "./../../config.php";
    require_once __DIR__ . "./../util/dbInteraction.php";
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- <link rel="stylesheet" type="text/css" href="./css/homeAdmin.css"> -->
        <title>Book Selling - Home Admin</title></head>
	<body>
<?php
        include "./../layout/header.php";
?>
        <b>Home Admin:</b><br>
<?php
        if(isLogged()){
            echo "<b>Ciao:".$_SESSION['name']."</b><br>";
        }
?>
	</body>
</html>