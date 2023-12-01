<?php	

    require_once __DIR__ . "/dbManager.php";	

    function getUsers(){

        global $SecureBookSellingDB;

        try{
            $query = "SELECT username, name, surname, email, date_of_birth FROM user;";

            $result = $SecureBookSellingDB->performQuery($query);
			
            $SecureBookSellingDB->closeConnection($query);
			return $result;
        }
        catch(Exception $e){
			echo "Error performing the query: ". $e->getCode() . $e->getMessage();
			return false;
        }  
    }

?> 