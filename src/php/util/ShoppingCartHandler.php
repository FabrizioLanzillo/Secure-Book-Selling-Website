<?php
global $sessionHandler;

/**
 * This Class handles all the interactions with the shopping cart
 * for both anonymous and logged-in users.
 *
 * If the user is anonymous the cart is handled and saved only in the session
 *       the details of the book are stored in an associative array with the key $itemId,
 *       The overall structure is then accessible through $_SESSION['cart'].
 * If the user is logged the cart is still handled in the session, but it is also saved in the db.
 *
 * So the logic of the shopping cart is always handled by the session.
 * However, if the user is logged, all changes are also saved in the db, to keep the data persistent
 */
class ShoppingCartHandler{
    private static ?ShoppingCartHandler $instance = null;

    /**
     * In the constructor the session variable of the cart is set
     */
    private function __construct() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    /**
     * This method returns the singleton instance of ShoppingCartHandler.
     * If the instance doesn't exist, it creates one; otherwise, it returns the existing instance.
     * @return ShoppingCartHandler
     */
    public static function getInstance(): ?ShoppingCartHandler{
        if (self::$instance == null) {
            self::$instance = new ShoppingCartHandler();
        }
        return self::$instance;
    }

    /**
     * This method add a new item in the shopping cart
     * If the user is anonymous the item is added only in the data structure of cart saved in the session
     * If the user is logged the item is still added the data structure of cart saved in the session,
     *      but it is also added in the db
     * @throws Exception
     */
    public function addItem($itemId, $quantity): bool {
        global $sessionHandler;

        // Check if the item was already in the cart, and in that case only the quantity needs to be updated
        $quantityToLoad = $quantity;
        if (isset($_SESSION['cart'][$itemId])) {
            $quantityToLoad += $_SESSION['cart'][$itemId]['quantity'];
        }
        // check if the book with the quantity selected, is available in the database
        $result= checkBookAvailability($itemId, $quantityToLoad);
        if ($result) {
            $bookDetails = $result->fetch_assoc();
            if($bookDetails !== null && $result->num_rows === 1){
                $_SESSION['cart'][$itemId] = array(
                    'title' => $bookDetails['title'],
                    'author' => $bookDetails['author'],
                    'publisher' => $bookDetails['publisher'],
                    'quantity' => $quantityToLoad,
                    'price' => $bookDetails['price']
                );
                // if the user is logged, the item is also added in the db
                if($sessionHandler->isLogged()){
                    if(!insertOrUpdateItems($_SESSION['cart'], $_SESSION['email'], false)){
                        throw new Exception("An error occurred while updating the shopping cart in the database.");
                    }
                }
                return true;
            }
            else{
                throw new Exception("Book not found in the database, impossible to add to the shopping cart.");
            }
        }
        else {
            throw new Exception("Error retrieving the book availability.");
        }
    }

    /**
     * This method removes an item from the data structure of cart saved in the session
     * if the user is logged it removes the item also from the db
     * @throws Exception
     */
    public function removeItem($itemId): bool {
        global $sessionHandler;

        if (isset($_SESSION['cart'][$itemId])) {
            // if the quantity is greater than 1, the item is not removed from the cart
            // but only its quantity is decreased
            if( $_SESSION['cart'][$itemId]['quantity'] > 1){
                $_SESSION['cart'][$itemId]['quantity'] --;
                if($sessionHandler->isLogged()){
                    if(!removeItems($itemId, $_SESSION['cart'][$itemId]['quantity'], $_SESSION['email'])){
                        throw new Exception("An error occurred while updating the shopping cart in the database.");
                    }
                }
            }
            else{
                unset($_SESSION['cart'][$itemId]);
                if($sessionHandler->isLogged()){
                    if(!removeItems($itemId,0, $_SESSION['email'])){
                        throw new Exception("An error occurred while updating the shopping cart in the database.");
                    }
                }
            }
            return true;
        }
        else {
            throw new Exception("The item does not exist in the cart.");
        }
    }

    /**
     * This method synchronizes the shopping cart saved in the session with the data from the db
     * @return bool
     */
    public function syncShoppingCart(): bool{

        // all the item stored in the db are taken
        $result = getShoppingCartBooks($_SESSION['email']);
        if($result){
            // check if the query returned a result and more than 1 row
            if ($result->num_rows >= 1) {
                $shoppingCart = array();
                // update the data structure of the shopping cart saved in the session with the data from the db
                while($rowShoppingCartFromDB = $result->fetch_assoc()){
                    $itemId = $rowShoppingCartFromDB['id_book'];
                    $shoppingCart[$itemId] = array(
                        'title' => $rowShoppingCartFromDB['title'],
                        'author' => $rowShoppingCartFromDB['author'],
                        'publisher' => $rowShoppingCartFromDB['publisher'],
                        'quantity' => $rowShoppingCartFromDB['quantity'],
                        'price' => $rowShoppingCartFromDB['price']
                    );
                }
                $_SESSION['cart'] = $shoppingCart;
                return true;
            }
            else{
                $_SESSION['cart'] = [];
                return true;
            }
        }
        else {
            return false;
        }
    }

    /**
     * This method return the shopping cart saved in the session after some checks
     * @throws Exception
     */
    public function getBooks(){

        if (!empty($_SESSION['cart'])) {
            return $_SESSION['cart'];
        }
        else {
            return null;
        }

    }

    /**
     * This method check if any item was added to the shopping cart while the user was anonymous
     * and if so it inserts it/them into the db, in both cases then the syncShoppingCart is called.
     * This method is called immediately after logging in.
     * @throws Exception
     */
    public function checkAndUpdateShoppingCartDB(): bool{
        // we check if the cart should be updated or not
        if (!empty($_SESSION['cart'])) {
            if (!insertOrUpdateItems($_SESSION['cart'], $_SESSION['email'], true)) {
                throw new Exception("An error occurred while updating the shopping cart in the database");
            }
        }
        if(!$this->syncShoppingCart()){
            throw new Exception("Error during the synchronization of the Shopping Cart");
        }
        return true;
    }

    /**
     * This method clear the shopping cart saved in the session
     * if the user is logged it is also cleared in the db
     * @throws Exception
     */
    public function clearShoppingCart(): bool{
        global $sessionHandler;

        // check if the cart has to be cleared
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $itemId => $quantity) {

                if (isset($_SESSION['cart'][$itemId])) {
                    unset($_SESSION['cart'][$itemId]);
                    if ($sessionHandler->isLogged()) {
                        if(!removeItems($itemId, 0, $_SESSION['email'])){
                            throw new Exception("An error occurred while updating the shopping cart in the database");
                        }
                    }
                }
            }
            return true;
        }
        else {
            throw new Exception("Error during the clear of the ShoppingCart");
        }
    }
}