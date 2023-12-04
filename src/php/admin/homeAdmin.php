<?php
    session_start();
	require_once __DIR__ . "./../util/dbInteraction.php";
    require_once __DIR__ . "./../util/sessionManager.php";
    require_once __DIR__ . "./../../config.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <!-- <link rel="stylesheet" type="text/css" href="./css/homeAdmin.css"> -->
    </head>
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