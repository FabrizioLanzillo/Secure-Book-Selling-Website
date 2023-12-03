<?php
	session_start();

	unset($_SESSION['userId']);
	unset($_SESSION['username']);
	unset($_SESSION['name']);
    unset($_SESSION['isAdmin']);

	header("Location: ./../index.php");

?>