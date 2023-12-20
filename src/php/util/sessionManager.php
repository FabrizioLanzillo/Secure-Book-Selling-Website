<?php
	function setSession($userId, $username, $name, $isAdmin): void{
		$_SESSION['userId'] = $userId;
        $_SESSION['username'] = $username;
		$_SESSION['name'] = $name;
		$_SESSION['isAdmin'] = $isAdmin;
	}

	function isLogged(): int{
		if(isset($_SESSION['userId']) && isset($_SESSION['username'])){
			return 1;
		}
		else{
			return 0;
		}
	}