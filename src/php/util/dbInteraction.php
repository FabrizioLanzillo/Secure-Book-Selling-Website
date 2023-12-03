<?php	

    require_once __DIR__ . "/dbManager.php";	

    /************************************* User Function ***********************************/
    
    function getUsers(){

        global $SecureBookSellingDB;

        try{
            $query = "SELECT username, name, surname, email, date_of_birth, isAdmin FROM user;";

            $result = $SecureBookSellingDB->performQuery($query);
			
            $SecureBookSellingDB->closeConnection($query);
			return $result;
        }
        catch(Exception $e){
			echo "Error performing the query: ". $e->getCode() . $e->getMessage();
			return false;
        }  
    }

    function authenticate($email, $password){

        global $SecureBookSellingDB;
        
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

            $dataRow = $result -> fetch_assoc();
            return $dataRow;
        }
        catch(Exception $e){
			echo "Error performing the authentication: ". $e->getCode() . $e->getMessage();
			return false;
        } 
	}	

    function getAccessInformation($email){

        global $SecureBookSellingDB;

        try{
            $email=$SecureBookSellingDB->sqlInjectionFilter($email);

            $query = "SELECT salt
                        FROM user
                        WHERE email = '".$email."';";

            $result = $SecureBookSellingDB->performQuery($query);
			
            $SecureBookSellingDB->closeConnection($query);
			$result = $result->fetch_assoc();
		    return $result['salt'];
        }
        catch(Exception $e){
			echo "Error getting access information: ". $e->getCode() . $e->getMessage();
			return false;
        }  
    }


?> 