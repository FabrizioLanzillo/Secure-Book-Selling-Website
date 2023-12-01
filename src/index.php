<?php
	session_start();
	require_once __DIR__ . "/php/util/dbManager.php";
	require_once __DIR__ . "/php/util/dbInteraction.php";


    echo "<b>Test Connection to the DB:</b><br>";

	$resultQuery = getUsers();

	while($user = $resultQuery->fetch_assoc()){
		foreach($user as $key => $value){
			
			echo $key . ": " . $value . "<br>";
		}
		echo "<br>";
	}
	
?>

