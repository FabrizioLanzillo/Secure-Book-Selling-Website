<?php	

    require_once __DIR__ . "/dbManager.php";	
    require_once __DIR__ . "./../../config.php";

    /************************************************ User Function ***************************************************/

    /** TESTING
     * This function get all data from all the users
     */
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
            $logger->writeLog(  'ERROR',
                                "Error performing the query to retrieve all the users",
                                $_SERVER['SCRIPT_NAME'],
                                "MySQL - Code: ".$e->getCode(),
                                $e->getMessage());
            $SecureBookSellingDB->closeConnection();
			return false;
        }
    }

    /** This function check if the credentials passed by the user are valid or not
     * @param $email string, is the email 
     * @param $password string, is the password
     * @return values|false
     */
    function authenticate($email, $password){

        global $SecureBookSellingDB;
        global $logger;
        
        try{

            $query = "SELECT id, username, `name`, isAdmin
                        FROM user
                        WHERE email = ? AND password = ?;";
            
            $result = $SecureBookSellingDB->performQuery($query, [$email, $password], "ss");
            if ($result->num_rows != 1) {
                return null;
            }
            $SecureBookSellingDB -> closeConnection();
            return $result -> fetch_assoc();
        }
        catch(Exception $e){
            $logger->writeLog(  'ERROR',
                                "Error during the authentication of the user: ".$email,
                                $_SERVER['SCRIPT_NAME'],
                                "MySQL - Code: ".$e->getCode(),
                                $e->getMessage());
            $SecureBookSellingDB->closeConnection();
			return false;
        } 
	}

    /** THis function get the salt of a given user
     * @param $email string, is the email to select the user
     * @return string|false
     */
    function getAccessInformation($email){

        global $SecureBookSellingDB;
        global $logger;

        try{

            $query = "SELECT salt
                        FROM user
                        WHERE email = ?;";

            $result = $SecureBookSellingDB->performQuery($query, [$email], "s");
            if ($result->num_rows != 1) {
                return null;
            }
            $SecureBookSellingDB -> closeConnection();
			$result = $result->fetch_assoc();
		    return $result['salt'];
        }
        catch(Exception $e){
            $logger->writeLog(  'ERROR',
                                "Error getting the salt for the user",
                                $_SERVER['SCRIPT_NAME'],
                                "MySQL - Code: ".$e->getCode(),
                                $e->getMessage());
            $SecureBookSellingDB->closeConnection();
			return false;
        }  
    }