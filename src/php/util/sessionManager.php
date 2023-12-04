<?php

	function setSession($userId, $username, $name, $isAdmin){
		$_SESSION['userId'] = $userId;
        $_SESSION['username'] = $username;
		$_SESSION['name'] = $name;
		$_SESSION['isAdmin'] = $isAdmin;
	}

	function isLogged(){
		if(isset($_SESSION['userId']) && isset($_SESSION['username'])){
			return $_SESSION['userId'];
		}
		else{
			return false;
		}
	}

	function pathRedirection(){
		if($_SESSION['isAdmin'] == '1'){
			echo "<script>alert('".$_SESSION['userId']." is Admin')</script>";
			// TODO: call to the admin homepage with header function
		}
		else{
			echo "<script>alert('".$_SESSION['userId']." is a normal user')</script>";
			// TODO: call to the user homepage with header function    
		}
	}
?>