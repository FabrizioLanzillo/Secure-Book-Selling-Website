<?php
    session_start();
    require_once __DIR__ . "./../config.php";
    require_once __DIR__ . "/util/dbInteraction.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <!-- <link rel="stylesheet" type="text/css" href="./css/profile.css"> -->
    </head>
	<body>
<?php
        include "./layout/header.php";
?>
        <b>Profile:</b><br>
<?php
        if(isLogged()){
            echo "<b>Ciao:".$_SESSION['name']."</b><br>";
        }
?>
	</body>
</html>