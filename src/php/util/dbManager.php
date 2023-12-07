<?php	

    require_once __DIR__ . "/dbConfig.php";	

    $SecureBookSellingDB = new DbManager($host, $user, $password, $dbName, $port);

    /**
     * Class that handle the connection with the mysql server and the queries
     */
	class DbManager{
		private $mysqli_connection = null;
		private $host;
		private $user;
		private $password;
		private $dbName;
        private $port;

        /**
         * @throws Exception
         */
        function __construct($host, $user, $password, $dbName, $port){
			$this->host = $host;
			$this->user = $user;
			$this->password = $password;
			$this->dbName =	$dbName;
            $this->port =	$port;
			$this->openConnection();
		}

        /**
         * @throws Exception
         */
        function openConnection(){
			if(!$this->isOpened()){		
				$this->mysqli_connection = new mysqli($this->host, $this->user, $this->password, $this->dbName, $this->port);
				if($this->mysqli_connection->connect_error){
					throw new Exception('Connection Error ('. $this->mysqli_connection->connect_errno.') '. $this->mysqli_connection->connect_error);
				}
				$this->mysqli_connection->select_db($this->dbName) or throw new Exception('Can\'t use the database: ' . $this->mysqli_connection->error);
			}
		}

		function isOpened(): bool{
       		return ($this->mysqli_connection != null);
    	}

        /**
         * @throws Exception
         */
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

        /**
         * function that filter the parameter in order to avoid cases of sql injection
         * @throws Exception
         */
        function sqlInjectionFilter($parameter){
			if(!$this->isOpened()){
				$this->openConnection();
			}

			return $this->mysqli_connection->real_escape_string($parameter);
		}
		
		function closeConnection(): void{
            $this->mysqli_connection?->close();
			$this->mysqli_connection = NULL;
		}
	}