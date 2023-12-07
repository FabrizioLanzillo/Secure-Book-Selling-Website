<?php
	function setSession($userId, $username, $name, $isAdmin): void{
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