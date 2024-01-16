<?php
require_once __DIR__ . "/../../config.php";
require_once __DIR__ . "/DbManager.php";

$SecureBookSellingDB = DbManager::getInstance();

/************************************************** User Function *****************************************************/

/**
 * This function retrieves all the personal data of a user
 * @param $username , is the username of the current user
 */
function getUserData($username)
{
    global $SecureBookSellingDB;
    global $logger;

    try {
        $query = "SELECT username, name, surname, email, password 
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


/**
 * This function retrieves all customers' data, the customer selected are not admin
 */
function getAllCustomersData()
{
    global $SecureBookSellingDB;
    global $logger;

    try {
        $query = "SELECT id, name, surname, username, email 
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

/**
 * This function inserts a user into the database
 * @param $userInformation , is an array with the user information
 */
function insertUser($userInformation): bool
{
    global $SecureBookSellingDB;
    global $logger;

    try {
        $query = "INSERT INTO user (username, password, salt, email, name, surname, isAdmin) 
                        VALUES (?, ?, ?, ?, ?, ?, ?);";

        $SecureBookSellingDB->performQuery("INSERT", $query, $userInformation, "ssssssi");
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

/**
 * This function checks if the credentials passed by the user are valid or not
 * @param $email , is the email inserted by the user
 * @param $password , is the password inserted by the user
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

/**
 * This function gets the salt, failedAccesses and blockedUntil of a given user
 * @param $email , is the email to select the user
 */
function getAccessInformation(string $email)
{
    global $SecureBookSellingDB;
    global $logger;

    try {

        $query = "SELECT salt, firstFailedAccess, failedAccesses, blockedTime
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

/**
 * This function increments or set to 0 the failed attempts of login of a user and eventually blocks it
 * @param $email , is the email to select the user
 * @param $failedAccesses , is the number of failed logins
 */
function updateFailedAccesses($information): bool
{
    global $SecureBookSellingDB;
    global $logger;

    try {
        // In the first case the firstFailedAccess is set such that can be used for the timeout
        if ($information[0] >= 3) {
            $query = "UPDATE user
                        SET firstFailedAccess = NOW(), failedAccesses = 0, blockedTime = ?
                        WHERE email = ?;";
        } else if ($information[0] === 1){
            $query = "UPDATE user
                        SET firstFailedAccess = NOW(), failedAccesses = ?
                        WHERE email = ?;";
        } else {
            $query = "UPDATE user
                        SET failedAccesses = ?
                        WHERE email = ?;";
        }

        $SecureBookSellingDB->performQuery("UPDATE", $query, $information, "is");
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

/**
 * This function gets the Time of the last OTP generated
 * @param $email , is the email to select the user
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
        $SecureBookSellingDB->closeConnection();
        return $result;

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

/**
 * This function sets the new otp in the user table
 * @param $email , is the email to select the user
 * @param $newOtp , is the new OTP for the user
 */
function setOtp($email, $newOtp): bool
{
    global $SecureBookSellingDB;
    global $logger;

    try {

        $query = "UPDATE user
                        SET otp = ? , lastOtp = NOW()
                        WHERE email = ?;";

        $SecureBookSellingDB->performQuery("UPDATE", $query, [$newOtp, $email], "ss");
        $SecureBookSellingDB->closeConnection();
        return true;

    } catch (Exception $e) {
        $logger->writeLog('ERROR',
            "Error updating OTP for the user",
            $_SERVER['SCRIPT_NAME'],
            "MySQL - Code: " . $e->getCode(),
            $e->getMessage());
        $SecureBookSellingDB->closeConnection();
        return false;
    }
}

/**
 * This function retrieves all the security information of a specific user
 * @param $email , is the email to select the user
 */
function getSecurityInfo($email)
{
    global $SecureBookSellingDB;
    global $logger;

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

/**
 * This function updates a user's password and all the other parameters linked to it
 * @param $userInformation , is an array with the new password-related information
 */
function updateUserPassword($userInformation): bool
{
    global $SecureBookSellingDB;
    global $logger;

    try {
        $query = "UPDATE user
                        SET password = ?, salt = ?, otp = NULL
                        WHERE email = ?;";

        $SecureBookSellingDB->performQuery("INSERT", $query, $userInformation, "sss");
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

/**
 * This function deletes a customer from the database
 * @param $customerId , is the id of the customer
 */
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

/************************************************** Books Function ****************************************************/

/**
 * This function retrieves all the books in the database
 */
function getBooks()
{
    global $SecureBookSellingDB;
    global $logger;

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

/**
 * This function retrieves the books that have a title like the one submitted
 * @param $title , is the partial or full title of the book
 */
function searchBooks($title)
{
    global $SecureBookSellingDB;
    global $logger;

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

/**
 * This function retrieves all the information about a book
 * @param $bookId , is the id of the book
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

/**
 * This function retrieves all books' data
 */
function getAllBooksData()
{
    global $SecureBookSellingDB;
    global $logger;

    try {
        $query = "SELECT id, title, author, publisher, price, category, stocks_number 
                          FROM book
                          ORDER BY title;";

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

/**
 * This function inserts new book into database
 * @param $bookInfo , is an array with all the book information
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
 * This function deletes a book from the database
 * @param $bookId , is the id of the book
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
 * This function updates the info of a book
 * @param $bookInfo , is an array with the book's information=
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

/********************************************* Shopping Cart Function *************************************************/

/**
 * This function checks if a given book is in the database
 * and if the quantity requested is lower than the stock number
 * @param $bookId , is the id of the book
 * @param $quantity , is the quantity requested by the user
 */
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

/**
 * This function inserts new books in the book table of the db
 * @param $shoppingCartInformation , is an array with all books' information
 * @param $email , is the email of the logged user
 * @param $increment , if this variable is true the object is already in the cart and only the quantity needs
 *                      to be increased
 */
function insertOrUpdateItems($shoppingCartInformation, $email, $increment): bool
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

            $SecureBookSellingDB->performQuery("INSERT", $query, $parameters, "sisssdi");
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

/**
 * This function removes books or decreases the quantity of the given book
 * @param $bookID , is the id of the given book
 * @param $quantity , is used to determine if the book need to be deleted or only its quantity need to be decreased
 * @param $email , is the email of the logged user
 */
function removeItems($bookID, $quantity, $email): bool
{
    global $SecureBookSellingDB;
    global $logger;

    try {

        if ($quantity <= 0) {
            $deleteQuery = "DELETE FROM shopping_cart WHERE id_book = ? AND email = ?";
            $SecureBookSellingDB->performQuery("DELETE", $deleteQuery, [$bookID, $email], "is");
        } else {
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

/**
 * This function gets all the element in the shopping cart
 * @param $email , is the mail of the logged user
 */
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

/************************************************* Orders Function ****************************************************/

/**
 * This function retrieves all the orders of a user
 * @param $userId , is the id of the user
 */
function getUserOrders($userId)
{
    global $SecureBookSellingDB;
    global $logger;

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

/**
 * This function adds the bought books to the order table in the db
 * @param $userId , is the id of the user
 * @param $currentTime , is the timestamp of the instant of purchase
 * @param $cartItems , is an array with all the books in the shopping cart
 * @param $totalPrice , is the total price of the purchase
 */
function addItemToOrders($userId, $currentTime, $cartItems, $totalPrice): bool
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

/** This function retrieves all orders' data
 */
function getAllOrdersData()
{
    global $SecureBookSellingDB;
    global $logger;

    try {
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

/**
 * This function checks if the given book was bought by the current user
 * @param $userId , is the id of the current user
 * @param $bookId , is the id of the given book
 */
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