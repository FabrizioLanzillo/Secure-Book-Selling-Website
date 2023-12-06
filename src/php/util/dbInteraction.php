<?php	

    require_once __DIR__ . "/dbManager.php";	
    require_once __DIR__ . "./../../config.php";

    /************************************* User Function ***********************************/
    
    function getUsers(){

        global $SecureBookSellingDB;
        global $logger;

        try{
            $query = "SELECT username, name, surname, email, date_of_birth, isAdmin FROM user;";

            $result = $SecureBookSellingDB->performQuery($query);
			
            $SecureBookSellingDB->closeConnection();
			return $result;
        }
        catch(Exception $e){
            $message = "Error performing the query: ". $e->getCode() . $e->getMessage();
            $logger->writeLog('ERROR', $message);
			return false;
        }  
    }

    function authenticate($email, $password){

        global $SecureBookSellingDB;
        global $logger;
        
        try{
            $email=$SecureBookSellingDB->sqlInjectionFilter($email);
            $password=$SecureBookSellingDB->sqlInjectionFilter($password);

            $query = "SELECT id, username, `name`, isAdmin
                        FROM user
                        WHERE
                        (   email = '".$email."'
                            AND
                            password = '".$password."'
                        );";
            
            $result = $SecureBookSellingDB->performQuery($query);
            
            
            $numbRow = mysqli_num_rows($result);
            if($numbRow != 1){
                return null;
            }
            $SecureBookSellingDB -> closeConnection();

            return $result -> fetch_assoc();
        }
        catch(Exception $e){
            $message = "Error performing the authentication: ". $e->getCode() . $e->getMessage();
            $logger->writeLog('ERROR', $message);
			return false;
        } 
	}	

    function getAccessInformation($email){

        global $SecureBookSellingDB;
        global $logger;

        try{
            $email=$SecureBookSellingDB->sqlInjectionFilter($email);

            $query = "SELECT salt
                        FROM user
                        WHERE email = '".$email."';";

            $result = $SecureBookSellingDB->performQuery($query);
			
            $numbRow = mysqli_num_rows($result);
            if($numbRow != 1){
                return null;
            }

            $SecureBookSellingDB->closeConnection();
			$result = $result->fetch_assoc();
		    return $result['salt'];
        }
        catch(Exception $e){
            $message = "Error getting access information: ". $e->getCode() . $e->getMessage();
            $logger->writeLog('ERROR', $message);
			return false;
        }  
    }