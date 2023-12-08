<?php	

    require_once __DIR__ . "/dbManager.php";	
    require_once __DIR__ . "./../../config.php";

    /************************************* User Function ***********************************/
    
    function getUsers(){

        global $SecureBookSellingDB;
        global $logger;
        global $debug;

        try{
            $query = "SELECT username, name, surname, email, date_of_birth, isAdmin FROM user;";

            $result = $SecureBookSellingDB->performQuery($query);
			
            $SecureBookSellingDB->closeConnection();
			return $result;
        }
        catch(Exception $e){
            $file = $debug ? "[File: ".$_SERVER['SCRIPT_NAME']."] " : "";
            $errorCode = $debug ? "[Error: MySQL - Code: ".$e->getCode()."]" : "";
            $message = $file . $errorCode ."[File: ".$_SERVER['SCRIPT_NAME']."] [Error: MySQL - Code: ".$e->getCode()."] - Error performing the query to retrieve all the users";
            $logger->writeLog('ERROR', $message);
			return false;
        }  
    }

    function authenticate($email, $password){

        global $SecureBookSellingDB;
        global $logger;
        global $debug;
        
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

            $file = $debug ? "[File: ".$_SERVER['SCRIPT_NAME']."] " : "";
            $errorCode = $debug ? "[Error: MySQL - Code: ".$e->getCode()."]" : "";
            $message = $file . $errorCode ." - Error during the authentication of the user: ".$email;
            $logger->writeLog('ERROR', $message);
			return false;
        } 
	}	

    function getAccessInformation($email){

        global $SecureBookSellingDB;
        global $logger;
        global $debug;

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
            $file = $debug ? "[File: ".$_SERVER['SCRIPT_NAME']."] " : "";
            $errorCode = $debug ? "[Error: MySQL - Code: ".$e->getCode()."]" : "";
            $message = $file . $errorCode ."[File: ".$_SERVER['SCRIPT_NAME']."] [Error: MySQL - Code: ".$e->getCode()."] - Error getting the salt for the user: ".$email;
            $logger->writeLog('ERROR', $message);
			return false;
        }  
    }