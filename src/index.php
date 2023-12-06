<?php

	session_start();
	require_once __DIR__ . "/config.php";
	require_once __DIR__ . "/php/util/dbInteraction.php";

	$resultQuery = getUsers();
	
	if($resultQuery != false){
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


	// $username = 'insert';
	// echo "username: ". $username . "<br>";
	// $salt = bin2hex(random_bytes(32));
	// echo "salt: ". $salt . "<br>";
	// echo "hash: ".hash('sha256', $username . $salt);

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
?>
	</body>
</html>