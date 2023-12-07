<?php

	session_start();
	require_once __DIR__ . "/config.php";
	require_once __DIR__ . "/php/util/dbInteraction.php";

	$resultQuery = getUsers();
	
	if($resultQuery){
		while($user = $resultQuery->fetch_assoc()){
			foreach($user as $key => $value){
				
				echo $key . ": " . $value . "<br>";
			}
			echo "<br>";
		}
    }
	else{
		echo "<script>alert('Error retrieving users data');</script>";
	}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="./css/index.css">
        <title>Book Selling - Home</title>
    </head>
	<body>
<?php
		include "./php/layout/header.php";

        if(isLogged()){
            echo "<b>Ciao:".$_SESSION['name']."</b><br>";
        }
?>
	</body>
</html>