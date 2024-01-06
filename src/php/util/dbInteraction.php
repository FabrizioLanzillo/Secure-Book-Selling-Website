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

            $result = $SecureBookSellingDB->performQuery("SELECT", $query);
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

    /** This function inserts an user into the database
     * @param $userInfromation Array, are the following params in order: username, password, salt, email, name, surname, date_of_birth
     * @return true|false
     */
    function insertUser($userInformation){

        global $SecureBookSellingDB;
        global $logger;

        try{
            $query = "INSERT INTO user (username, password, salt, email, name, surname, date_of_birth, isAdmin) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?);";

            $result = $SecureBookSellingDB->performQuery("INSERT", $query, $userInformation, "sssssssi");
            $SecureBookSellingDB->closeConnection();
			return true;
            
        }
        catch(Exception $e){
            $logger->writeLog(  'ERROR',
                                "Error performing the query to insert new user",
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
            
            $result = $SecureBookSellingDB->performQuery("SELECT", $query, [$email, $password], "ss");
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

            $result = $SecureBookSellingDB->performQuery("SELECT", $query, [$email], "s");
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

    /** This function retrieves all the books in the database
     * @return values|false
     */
    function getBooks(){

        global $SecureBookSellingDB;
        global $logger;
        global $debug;

        try{
            $query = "SELECT id, title, author, price FROM book;";

            $result = $SecureBookSellingDB->performQuery("SELECT", $query);
			
            $SecureBookSellingDB->closeConnection();
			return $result;
        }
        catch(Exception $e){
            $logger->writeLog(  "ERROR",
                                "Error performing the query to retrieve all the books",
                                $_SERVER['SCRIPT_NAME'],
                                "MySQL - Code: ".$e->getCode(),
                                $e->getMessage());
            $SecureBookSellingDB->closeConnection();
			return false;
        }  
    }

    /** This function retrieves the books that have a title like the one submitted
     * @param $title string, is the partial or full title of the book 
     * @return values|false
     */
    function searchBooks($title){

        global $SecureBookSellingDB;
        global $logger;
        global $debug;

        try{
            $query = "SELECT id, title, author, price FROM book WHERE title LIKE ?;";

            $titleParam = "%$title%";

            $result = $SecureBookSellingDB->performQuery("SELECT", $query, [$titleParam], "s");
			
            $SecureBookSellingDB->closeConnection();
			return $result;
        }
        catch(Exception $e){
            $logger->writeLog(  "ERROR",
                                "Error performing the query to retrieve a searched book",
                                $_SERVER['SCRIPT_NAME'],
                                "MySQL - Code: ".$e->getCode(),
                                $e->getMessage());
            $SecureBookSellingDB->closeConnection();
            return false;
        }  
    }

    /** This function retrieves all the information about a book
     * @param $bookId smallint, is the id of the book
     * @return values|false
     */
    function getBookDetails($bookId){

        global $SecureBookSellingDB;
        global $logger;
        global $debug;

        try{
            $query = "SELECT id, title, author, publisher, price, category, stocks_number FROM book WHERE id = ?;";

            $result = $SecureBookSellingDB->performQuery("SELECT", $query, [$bookId], "s");
			
            $SecureBookSellingDB->closeConnection();
			return $result;
        }
        catch(Exception $e){
            $logger->writeLog(  "ERROR",
                                "Error performing the query to retrieve all the details of the book",
                                $_SERVER['SCRIPT_NAME'],
                                "MySQL - Code: ".$e->getCode(),
                                $e->getMessage());
            $SecureBookSellingDB->closeConnection();
            return false;
        }
    }

    /** This function retrieves all the orders of a user
     * @param $userId smallint, is the id of the user
     * @return values|false
     */
    function getUserOrders($userId){

        global $SecureBookSellingDB;
        global $logger;
        global $debug;

        try{
            $query = "SELECT o.id, b.title, o.amount, o.status, o.payment_method
            FROM orders o INNER JOIN book b ON o.id_book = b.id
            WHERE id_user = ?;";

            $result = $SecureBookSellingDB->performQuery("SELECT", $query, [$userId], "s");
			
            $SecureBookSellingDB->closeConnection();
			return $result;
        }
        catch(Exception $e){
            $logger->writeLog(  "ERROR",
                                "Error performing the query to retrieve all the orders of a user",
                                $_SERVER['SCRIPT_NAME'],
                                "MySQL - Code: ".$e->getCode(),
                                $e->getMessage());
            $SecureBookSellingDB->closeConnection();
            return false;
        }
    }

    /** This function retrieves all the personal data of a user
     * @param $username
     * @return values|false
     */
    function getUserData($username){

        global $SecureBookSellingDB;
        global $logger;

        try{
            $query = "SELECT username, name, surname, email, password, date_of_birth 
                      FROM user 
                      WHERE username = ?;";

            $result = $SecureBookSellingDB->performQuery("SELECT", $query, [$username], "s");

            $SecureBookSellingDB->closeConnection();
            return $result;
        }
        catch(Exception $e){
            $logger->writeLog(  "ERROR",
                "Error performing the query to retrieve all the personal data of a user",
                $_SERVER['SCRIPT_NAME'],
                "MySQL - Code: ".$e->getCode(),
                $e->getMessage());
            $SecureBookSellingDB->closeConnection();
            return false;
        }
    }

    /** Retrieve all books' data
     * @return books|false
     */
    function getAllBooksData(){

        global $SecureBookSellingDB;
        global $logger;

        try{
            $query = "SELECT id, title, author, publisher, price, category, stocks_number 
                      FROM book
                      ORDER BY title;";

            $result = $SecureBookSellingDB->performQuery("SELECT", $query, [], "isssfsi");

            $SecureBookSellingDB->closeConnection();
            return $result;
        }
        catch(Exception $e){
            $logger->writeLog(  "ERROR",
                "Error performing the query to retrieve all books' data",
                $_SERVER['SCRIPT_NAME'],
                "MySQL - Code: ".$e->getCode(),
                $e->getMessage());
            $SecureBookSellingDB->closeConnection();
            return false;
        }
    }

    /** Retrieve all customers' data
     * @return users_info|false
     */
    function getAllCustomersData(){

        global $SecureBookSellingDB;
        global $logger;

        try{
            $query = "SELECT id, name, surname, username, email, date_of_birth 
                          FROM user
                          WHERE isAdmin = 0
                          ORDER BY username;";

            $result = $SecureBookSellingDB->performQuery("SELECT", $query, [], "sssss");

            $SecureBookSellingDB->closeConnection();
            return $result;
        }
        catch(Exception $e){
            $logger->writeLog(  "ERROR",
                "Error performing the query to retrieve all customers' data",
                $_SERVER['SCRIPT_NAME'],
                "MySQL - Code: ".$e->getCode(),
                $e->getMessage());
            $SecureBookSellingDB->closeConnection();
            return false;
        }
    }

    /** Retrieve all orders' data
     * @return orders_info|false
     */
    function getAllOrdersData(){

        global $SecureBookSellingDB;
        global $logger;

        try{
            $query = "SELECT u.username as username, b.title as title, o.amount as amount, o.status as status, o.payment_method as payment_method
                      FROM orders o 
                        INNER JOIN user u ON o.id_user = u.id
                        INNER JOIN book b ON o.id_book = b.id
                      ORDER BY u.username;";

            $result = $SecureBookSellingDB->performQuery("SELECT", $query, [], "sssss");

            $SecureBookSellingDB->closeConnection();
            return $result;
        }
        catch(Exception $e){
            $logger->writeLog(  "ERROR",
                "Error performing the query to retrieve all customers' data",
                $_SERVER['SCRIPT_NAME'],
                "MySQL - Code: ".$e->getCode(),
                $e->getMessage());
            $SecureBookSellingDB->closeConnection();
            return false;
        }
    }

/** Insert new book into database
 * @param $bookInfo
 * @return bool
 */
function insertBook($bookInfo): bool{

    global $SecureBookSellingDB;
    global $logger;

    try{
        $query = "INSERT INTO book (title, author, publisher, price, category, stocks_number) 
                        VALUES (?, ?, ?, ?, ?, ?);";

        $SecureBookSellingDB->performQuery("INSERT", $query, $bookInfo, "sssdsi");
        $SecureBookSellingDB->closeConnection();
        return true;

    }
    catch(Exception $e){
        $logger->writeLog(  'ERROR',
            "Error performing the query to insert new book",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: ".$e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

function deleteBook($bookId): bool{

    global $SecureBookSellingDB;
    global $logger;

    try{
        $query = "DELETE FROM book WHERE id = ?;";

        $SecureBookSellingDB->performQuery("DELETE", $query, [$bookId], "i");
        $SecureBookSellingDB->closeConnection();
        return true;

    }
    catch(Exception $e){
        $logger->writeLog(  'ERROR',
            "Error performing the query to delete the book",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: ".$e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

function updateBook($bookInfo): bool{

    global $SecureBookSellingDB;
    global $logger;

    try{
        $query = "UPDATE book 
                  SET title = ?, author = ?, publisher = ?, price = ?, category = ?, stocks_number = ? 
                  WHERE id = ?";

        $SecureBookSellingDB->performQuery("UPDATE", $query, $bookInfo, "sssdsii");
        $SecureBookSellingDB->closeConnection();
        return true;

    }
    catch(Exception $e){
        $logger->writeLog(  'ERROR',
            "Error performing the query to update book",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: ".$e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

function deleteCustomer($customerId): bool{

    global $SecureBookSellingDB;
    global $logger;

    try{
        $query = "DELETE FROM user WHERE id = ?;";

        $SecureBookSellingDB->performQuery("DELETE", $query, [$customerId], "i");
        $SecureBookSellingDB->closeConnection();
        return true;

    }
    catch(Exception $e){
        $logger->writeLog(  'ERROR',
            "Error performing the query to delete the customer",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: ".$e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

function updateCustomer($customerInfo): bool{

    global $SecureBookSellingDB;
    global $logger;

    try{
        $query = "UPDATE user 
                  SET username = ?, email = ?, 
                  WHERE username = ?";

        $SecureBookSellingDB->performQuery("UPDATE", $query, $customerInfo, "sssdsii");
        $SecureBookSellingDB->closeConnection();
        return true;

    }
    catch(Exception $e){
        $logger->writeLog(  'ERROR',
            "Error performing the query to update book",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: ".$e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}








