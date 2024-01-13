<?php
require_once __DIR__ . "/../../config.php";
require_once __DIR__ . "/DbManager.php";

$SecureBookSellingDB = DbManager::getInstance();

/************************************************ User Function ***************************************************/

/** TESTING
 * This function get all data from all the users
 */
function getUsers()
{

    global $SecureBookSellingDB;
    global $logger;

    try {
        $query = "SELECT username, name, surname, email, date_of_birth, isAdmin FROM user;";

        $result = $SecureBookSellingDB->performQuery("SELECT", $query);
        $SecureBookSellingDB->closeConnection();
        return $result;
    } catch (Exception $e) {
        $logger->writeLog('ERROR',
            "Error performing the query to retrieve all the users",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

/** This function retrieves all the personal data of a user
 * @param $username
 * @return values|false
 */
function getUserData($username)
{

    global $SecureBookSellingDB;
    global $logger;

    try {
        $query = "SELECT username, name, surname, email, password, date_of_birth 
                          FROM user 
                          WHERE username = ?;";

        $result = $SecureBookSellingDB->performQuery("SELECT", $query, [$username], "s");

        $SecureBookSellingDB->closeConnection();
        return $result;
    } catch (Exception $e) {
        $logger->writeLog("ERROR",
            "Error performing the query to retrieve all the personal data of a user",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}


/** Retrieve all customers' data
 * @return users_info|false
 */
function getAllCustomersData()
{

    global $SecureBookSellingDB;
    global $logger;

    try {
        $query = "SELECT id, name, surname, username, email, date_of_birth 
                          FROM user
                          WHERE isAdmin = 0
                          ORDER BY username;";

        $result = $SecureBookSellingDB->performQuery("SELECT", $query);

        $SecureBookSellingDB->closeConnection();
        return $result;
    } catch (Exception $e) {
        $logger->writeLog("ERROR",
            "Error performing the query to retrieve all customers' data",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

/** This function inserts an user into the database
 * @param $userInfromation Array, are the following params in order: username, password, salt, email, name, surname, date_of_birth
 * @return true|false
 */
function insertUser($userInformation)
{

    global $SecureBookSellingDB;
    global $logger;

    try {
        $query = "INSERT INTO user (username, password, salt, email, name, surname, date_of_birth, isAdmin, failedAccesses, lastOtp) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW());";

        $result = $SecureBookSellingDB->performQuery("INSERT", $query, $userInformation, "sssssssii");
        $SecureBookSellingDB->closeConnection();
        return true;

    } catch (Exception $e) {
        $logger->writeLog('ERROR',
            "Error performing the query to insert new user",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
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
function authenticate($email, $password)
{

    global $SecureBookSellingDB;
    global $logger;

    try {

        $query = "SELECT id, username, `name`, isAdmin
                        FROM user
                        WHERE email = ? AND password = ?;";

        $result = $SecureBookSellingDB->performQuery("SELECT", $query, [$email, $password], "ss");
        $SecureBookSellingDB->closeConnection();
        return $result;
    } catch (Exception $e) {
        $logger->writeLog('ERROR',
            "Error during the authentication of the user: " . $email,
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

/** This function get the salt, failedAccesses and blockedUntil of a given user
 * @param $email string, is the email to select the user
 * @return string|false
 */
function getAccessInformation(string $email)
{

    global $SecureBookSellingDB;
    global $logger;

    try {

        $query = "SELECT salt, failedAccesses, blockedUntil
                        FROM user
                        WHERE email = ?;";

        $result = $SecureBookSellingDB->performQuery("SELECT", $query, [$email], "s");
        $SecureBookSellingDB->closeConnection();
        return $result;
    } catch (Exception $e) {
        $logger->writeLog('ERROR',
            "Error getting the salt for the user",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

/** This function increments or set to 0 the failed attempts of login of a user and eventually blocks it
 * @param $email string, is the email to select the user
 * @param $failedAccesses int, is the number of failed logins
 * @return true|false
 */
function updateFailedAccesses($email, $failedAccesses)
{

    global $SecureBookSellingDB;
    global $logger;

    try {

        if ($failedAccesses >= 5) {
            $query = "UPDATE user
                        SET failedAccesses = ? , blockedUntil = DATE_ADD(NOW(),interval 30 minute)
                        WHERE email = ?;";
        } else {
            $query = "UPDATE user
                        SET failedAccesses = ?
                        WHERE email = ?;";
        }


        $result = $SecureBookSellingDB->performQuery("UPDATE", $query, [$failedAccesses, $email], "is");
        $SecureBookSellingDB->closeConnection();
        return true;
    } catch (Exception $e) {
        $logger->writeLog('ERROR',
            "Error incrementing the failed accesse of the user",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

/** This function get the Time of the last Otp generated
 * @param $email string, is the email to select the user
 * @return string|false
 */
function getOtpTimeInformation($email)
{

    global $SecureBookSellingDB;
    global $logger;

    try {

        $query = "SELECT lastOtp
                        FROM user
                        WHERE email = ?;";

        $result = $SecureBookSellingDB->performQuery("SELECT", $query, [$email], "s");
        if ($result->num_rows != 1) {
            return null;
        }
        $SecureBookSellingDB->closeConnection();
        $result = $result->fetch_assoc();
        return $result['lastOtp'];
    } catch (Exception $e) {
        $logger->writeLog('ERROR',
            "Error getting the lastOtp for the user",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

/** This function updates the user table with the new otp
 * @param $email string, is the email to select the user
 * @param $newOtp string, is the new Otp for the user
 * @return string|false
 */
function setOtp($email, $newOtp)
{

    global $SecureBookSellingDB;
    global $logger;

    try {

        $query = "UPDATE user
                        SET otp = ? , lastOtp = NOW()
                        WHERE email = ?;";

        $result = $SecureBookSellingDB->performQuery("UPDATE", $query, [$newOtp, $email], "ss");
        $SecureBookSellingDB->closeConnection();
        return true;
    } catch (Exception $e) {
        $logger->writeLog('ERROR',
            "Error updating otp for the user",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

/** This function retrieves all the security information of a specific user
 * * @param $email string, is the email to select the user
 * @return values|false
 */
function getSecurityInfo($email)
{

    global $SecureBookSellingDB;
    global $logger;
    global $debug;

    try {
        $query = "SELECT otp, lastOtp
                        FROM user
                        WHERE email = ?;";

        $result = $SecureBookSellingDB->performQuery("SELECT", $query, [$email], "s");

        $SecureBookSellingDB->closeConnection();
        return $result;
    } catch (Exception $e) {
        $logger->writeLog("ERROR",
            "Error performing the query to retrieve security information of a user",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

/** This function updates an user's password and all the other parameters linked to it
 * @param $userInfromation Array, are the following params in order: password, salt, $_POST['email']
 * @return true|false
 */
function updateUserPassword($userInformation)
{

    global $SecureBookSellingDB;
    global $logger;

    try {
        $query = "UPDATE user
                        SET password = ?, salt = ?, otp = NULL
                        WHERE email = ?;";

        $result = $SecureBookSellingDB->performQuery("INSERT", $query, $userInformation, "sss");
        $SecureBookSellingDB->closeConnection();
        return true;

    } catch (Exception $e) {
        $logger->writeLog('ERROR',
            "Error performing the query to insert new user",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

function deleteCustomer($customerId): bool
{

    global $SecureBookSellingDB;
    global $logger;

    try {
        $query = "DELETE FROM user WHERE id = ?;";

        $SecureBookSellingDB->performQuery("DELETE", $query, [$customerId], "i");
        $SecureBookSellingDB->closeConnection();
        return true;

    } catch (Exception $e) {
        $logger->writeLog('ERROR',
            "Error performing the query to delete the customer",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

/************************************************ Books Function **************************************************/

/** This function retrieves all the books in the database
 * @return values|false
 */
function getBooks()
{

    global $SecureBookSellingDB;
    global $logger;
    global $debug;

    try {
        $query = "SELECT id, title, author, price FROM book;";

        $result = $SecureBookSellingDB->performQuery("SELECT", $query);

        $SecureBookSellingDB->closeConnection();
        return $result;
    } catch (Exception $e) {
        $logger->writeLog("ERROR",
            "Error performing the query to retrieve all the books",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

/** This function retrieves the books that have a title like the one submitted
 * @param $title string, is the partial or full title of the book
 * @return values|false
 */
function searchBooks($title)
{

    global $SecureBookSellingDB;
    global $logger;
    global $debug;

    try {
        $query = "SELECT id, title, author, price FROM book WHERE title LIKE ?;";

        $titleParam = "%$title%";

        $result = $SecureBookSellingDB->performQuery("SELECT", $query, [$titleParam], "s");

        $SecureBookSellingDB->closeConnection();
        return $result;
    } catch (Exception $e) {
        $logger->writeLog("ERROR",
            "Error performing the query to retrieve a searched book",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

/** This function retrieves all the information about a book
 * @param $bookId smallint, is the id of the book
 * @return values|false
 */
function getBookDetails($bookId)
{

    global $SecureBookSellingDB;
    global $logger;

    try {
        $query = "SELECT id, title, author, publisher, price, category, stocks_number FROM book WHERE id = ?;";

        $result = $SecureBookSellingDB->performQuery("SELECT", $query, [$bookId], "s");

        $SecureBookSellingDB->closeConnection();
        return $result;
    } catch (Exception $e) {
        $logger->writeLog("ERROR",
            "Error performing the query to retrieve all the details of the book",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

/** Retrieve all books' data
 * @return books|false
 */
function getAllBooksData()
{

    global $SecureBookSellingDB;
    global $logger;

    try {
        $query = "SELECT id, title, author, publisher, price, category, stocks_number 
                          FROM book
                          ORDER BY title;";

        //$result = $SecureBookSellingDB->performQuery("SELECT", $query, [], "isssfsi");
        $result = $SecureBookSellingDB->performQuery("SELECT", $query);

        $SecureBookSellingDB->closeConnection();
        return $result;
    } catch (Exception $e) {
        $logger->writeLog("ERROR",
            "Error performing the query to retrieve all books' data",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

/** Insert new book into database
 * @param $bookInfo
 * @return bool
 */
function insertBook($bookInfo): bool
{

    global $SecureBookSellingDB;
    global $logger;

    try {
        $query = "INSERT INTO book (title, author, publisher, price, category, stocks_number, ebook_name) 
                            VALUES (?, ?, ?, ?, ?, ?, ?);";

        $SecureBookSellingDB->performQuery("INSERT", $query, $bookInfo, "sssdsis");
        $SecureBookSellingDB->closeConnection();
        return true;

    } catch (Exception $e) {
        $logger->writeLog('ERROR',
            "Error performing the query to insert new book",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

/**
 * @param $bookId
 * @return bool
 */
function deleteBook($bookId): bool
{

    global $SecureBookSellingDB;
    global $logger;

    try {
        $query = "DELETE FROM book WHERE id = ?;";

        $SecureBookSellingDB->performQuery("DELETE", $query, [$bookId], "i");
        $SecureBookSellingDB->closeConnection();
        return true;

    } catch (Exception $e) {
        $logger->writeLog('ERROR',
            "Error performing the query to delete the book",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

/**
 * @param $bookInfo
 * @return bool
 */
function updateBook($bookInfo): bool
{

    global $SecureBookSellingDB;
    global $logger;

    try {
        $query = "UPDATE book 
                      SET title = ?, author = ?, publisher = ?, price = ?, category = ?, stocks_number = ? 
                      WHERE id = ?";

        $SecureBookSellingDB->performQuery("UPDATE", $query, $bookInfo, "sssdsii");
        $SecureBookSellingDB->closeConnection();
        return true;

    } catch (Exception $e) {
        $logger->writeLog('ERROR',
            "Error performing the query to update book",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

/******************************************** Shopping Cart Function **********************************************/

function checkBookAvailability($bookId, $quantity)
{
    global $SecureBookSellingDB;
    global $logger;

    try {

        $query = "SELECT *
                        FROM book
                        WHERE   id = ?
                                AND
                                ? <= book.stocks_number;";

        $result = $SecureBookSellingDB->performQuery("SELECT", $query, [$bookId, $quantity], "ii");
        if ($result->num_rows != 1) {
            return null;
        }
        $SecureBookSellingDB->closeConnection();
        return $result;
    } catch (Exception $e) {
        $logger->writeLog('ERROR',
            "Error getting the price of the book",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

function insertOrUpdateItems($shoppingCartInformation, $email, $increment)
{
    global $SecureBookSellingDB;
    global $logger;

    try {
        if ($increment) {
            $query = "INSERT INTO shopping_cart (email, id_book, title, author, publisher, price, quantity) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)
                       ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity);";
        } else {
            $query = "INSERT INTO shopping_cart (email, id_book, title, author, publisher, price, quantity) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)
                       ON DUPLICATE KEY UPDATE quantity = VALUES(quantity);";
        }
        foreach ($shoppingCartInformation as $itemId => $itemDetails) {
            $parameters = array(
                $email,
                $itemId,
                $itemDetails['title'],
                $itemDetails['author'],
                $itemDetails['publisher'],
                $itemDetails['price'],
                $itemDetails['quantity']
            );

            $result = $SecureBookSellingDB->performQuery("INSERT", $query, $parameters, "sisssdi");
        }

        $SecureBookSellingDB->closeConnection();
        return true;
    } catch (Exception $e) {
        $logger->writeLog('ERROR',
            "Error performing the query to insert/update of the shopping cart",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

function removeItems($bookID, $quantity, $email)
{
    global $SecureBookSellingDB;
    global $logger;

    try {

        if ($quantity <= 0) {
            // Se la quantità è 0 o inferiore, elimina la riga corrispondente
            $deleteQuery = "DELETE FROM shopping_cart WHERE id_book = ? AND email = ?";
            $SecureBookSellingDB->performQuery("DELETE", $deleteQuery, [$bookID, $email], "is");
        } else {
            // Altrimenti, decrementa la quantità
            $updateQuery = "UPDATE shopping_cart SET quantity = ? WHERE id_book = ? AND email = ?";
            $SecureBookSellingDB->performQuery("UPDATE", $updateQuery, [$quantity, $bookID, $email], "iis");
        }

        $SecureBookSellingDB->closeConnection();
        return true;
    } catch (Exception $e) {
        $logger->writeLog('ERROR',
            "Error performing the query to remove items from the shopping cart",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

function getShoppingCartBooks($email)
{
    global $SecureBookSellingDB;
    global $logger;

    try {

        $query = "SELECT *
                            FROM shopping_cart
                            WHERE email = ?;";

        $result = $SecureBookSellingDB->performQuery("SELECT", $query, [$email], "s");

        $SecureBookSellingDB->closeConnection();
        return $result;
    } catch (Exception $e) {
        $logger->writeLog('ERROR',
            "Error getting the books from the shopping cart",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

/************************************************ Orders Function *************************************************/

/** This function retrieves all the orders of a user
 * @param $userId smallint, is the id of the user
 * @return values|false
 */
function getUserOrders($userId)
{

    global $SecureBookSellingDB;
    global $logger;
    global $debug;

    try {
        $query = "SELECT b.title, o.time, o.amount, o.quantity, o.payment_method, b.id AS id_book
                        FROM orders o INNER JOIN book b ON o.id_book = b.id
                        WHERE id_user = ?
                        ORDER BY o.time DESC";

        $result = $SecureBookSellingDB->performQuery("SELECT", $query, [$userId], "s");

        $SecureBookSellingDB->closeConnection();
        return $result;
    } catch (Exception $e) {
        $logger->writeLog("ERROR",
            "Error performing the query to retrieve all the orders of a user",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

function addItemToOrders($userId, $currentTime, $cartItems, $totalPrice)
{
    global $SecureBookSellingDB;
    global $logger;

    try {

        $query = "INSERT INTO orders (id_user, id_book, time, amount, quantity, payment_method) 
                    VALUES (?, ?, ?, ?, ?, ?)";

        foreach ($cartItems as $itemId => $itemDetails) {
            $parameters = array(
                $userId,
                $itemId,
                $currentTime,
                $totalPrice,
                $itemDetails['quantity'],
                "Card",
            );
            $SecureBookSellingDB->performQuery("INSERT", $query, $parameters, "iisdis");
        }

        $query = "UPDATE book 
                      SET stocks_number = stocks_number - ? 
                      WHERE id = ?";

        foreach ($cartItems as $itemId => $itemDetails) {
            $parameters = array(
                $itemDetails['quantity'],
                $itemId,
            );
            $SecureBookSellingDB->performQuery("UPDATE", $query, $parameters, "ii");
        }

        $SecureBookSellingDB->closeConnection();
        return true;
    } catch (Exception $e) {
        $logger->writeLog('ERROR',
            "Error performing the query to insert into the orders",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

/** Retrieve all orders' data
 * @return orders_info|false
 */
function getAllOrdersData()
{

    global $SecureBookSellingDB;
    global $logger;

    try{
        $query = "SELECT u.username as username, b.title, o.time, o.amount, o.quantity, o.payment_method, b.id AS id_book
                    FROM orders o INNER JOIN book b ON o.id_book = b.id
                                    INNER JOIN user u ON o.id_user = u.id
                    ORDER BY o.time DESC";

        $result = $SecureBookSellingDB->performQuery("SELECT", $query);
        $SecureBookSellingDB->closeConnection();
        return $result;
    } catch (Exception $e) {
        $logger->writeLog("ERROR",
            "Error performing the query to retrieve all customers' data",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

function checkBookPurchaseByBook($userId, $bookId)
{
    global $SecureBookSellingDB;
    global $logger;

    try {
        $query = "SELECT b.ebook_name
                        FROM orders o INNER JOIN book b ON o.id_book = b.id
                        WHERE o.id_user = ? AND o.id_book = ?
                        LIMIT ?";

        $limit = 1;

        $result = $SecureBookSellingDB->performQuery("SELECT", $query, [$userId, $bookId, $limit], "iii");

        $SecureBookSellingDB->closeConnection();
        return $result;
    } catch (Exception $e) {
        $logger->writeLog("ERROR",
            "Error performing the query to check the book purchase",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}