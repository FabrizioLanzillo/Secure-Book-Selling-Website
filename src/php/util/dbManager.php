<?php	

    require_once __DIR__ . "/dbConfig.php";	

    $SecureBookSellingDB = new DbManager();

    /**
     * Class that handle the connection with the mysql server and the queries
     */
	class DbManager{
		private $mysqli_connection = null;
        
		function DbManager(){									
			$this->openConnection();
		}

		function openConnection(){
			if(!$this->isOpened()){		
				global $host;
				global $user;
				global $password;
				global $dbName;

				$this->mysqli_connection = new mysqli($host, $user, $password);	
				if($this->mysqli_connection->connect_error){
					throw new Exception('Connection Error ('. $this->mysqli_connection->connect_errno.') '. $this->mysqli_connection->connect_error);
				}
				$this->mysqli_connection->select_db($dbName) or throw new Exception('Can\'t use the database: ' . $this->mysqli_connection->error);
			}
		}

		function isOpened(){
       		return ($this->mysqli_connection != null);
    	}

		function performQuery($querytext){				
			if(!$this->isOpened()){
				$this->openConnection();
			}
			$result = $this->mysqli_connection->query($querytext);
			if ($result === false) {
				throw new Exception('Error ('. $this->mysqli_connection->connect_errno.') '. $this->mysqli_connection->connect_error);
			}
	
			return $result;
		}

        // function that filter the parameter in order to avoid cases of sqlinjection
		function sqlInjectionFilter($parameter){
			if(!$this->isOpened()){
				$this->openConnection();
			}

			return $this->mysqli_connection->real_escape_string($parameter);
		}
		
		function closeConnection(){
			if($this->mysqli_connection !== NULL){
				$this->mysqli_connection->close();
			}

			$this->mysqli_connection = NULL;
		}
	}
?>