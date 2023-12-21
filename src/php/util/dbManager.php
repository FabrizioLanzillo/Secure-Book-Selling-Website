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

    /** Constructor that imports the db configuration and open the connection
     * @throws Exception
     */
    function __construct($host, $user, $password, $dbName, $port){
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->dbName = $dbName;
        $this->port = $port;
        $this->openConnection();
    }

    /** Method that create a connection with the db
     * @throws Exception
     */
    function openConnection(): void{
        if (!$this->isOpened()) {
            $this->mysqli_connection = new mysqli($this->host,
                $this->user,
                $this->password,
                $this->dbName, $this->port);
            if ($this->mysqli_connection->connect_error) {
                throw new Exception('Connection Error (' . $this->mysqli_connection->connect_errno . ') ' .
                    $this->mysqli_connection->connect_error);
            }
            $this->mysqli_connection->select_db($this->dbName) or
            throw new Exception('Can\'t use the database: ' . $this->mysqli_connection->error);
        }
    }

    /** Method that checks if there is a connection to the db
     * @return bool
     */
    function isOpened(): bool{
        return ($this->mysqli_connection != null);
    }

    /** Method that creates the prepared Statement and executes the query
     * @param $querytext string, is the query statement
     * @param $parameters array, is an array of the parameters of the statement
     * @param $types string, indicate the types of the parameters in the array
     * @return mixed
     * @throws Exception
     */
    function performQuery(string $querytext, array $parameters = [], string $types = ""){
        if (!$this->isOpened()) {
            $this->openConnection();
        }

        $statement = $this->mysqli_connection->prepare($querytext);
        if (!$statement) {
            throw new Exception('Prepare failed (' . $this->mysqli_connection->connect_errno . ') ' .
                                                                $this->mysqli_connection->connect_error);
        }

        if (!empty($parameters) && !$statement->bind_param($types, ...$parameters)) {
            throw new Exception('Bind failed (' . $statement->connect_errno . ') ' .
                                                            $statement->connect_error);
        }

        if (!$statement->execute()) {
            throw new Exception('Execute failed (' . $statement->connect_errno . ') ' .
                                                                $statement->connect_error);
        }

        $result = $statement->get_result();
        if (!$result) {
            throw new Exception('Get Result failed (' . $statement->connect_errno . ') ' .
                                                                    $statement->connect_error);
        }

        return $result;
    }

    /** Method that close the connection with the db
     * @return void
     */
    function closeConnection(): void{
        $this->mysqli_connection->close();
        $this->mysqli_connection = null;
    }
}