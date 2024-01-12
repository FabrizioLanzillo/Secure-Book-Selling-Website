<?php
global $sessionHandler;

class ShoppingCartHandler{
    private static ?ShoppingCartHandler $instance = null;

    private function __construct() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public static function getInstance(): ?ShoppingCartHandler{
        if (self::$instance == null) {
            self::$instance = new ShoppingCartHandler();
        }
        return self::$instance;
    }

    /**
     * @throws Exception
     */
    public function addItem($itemId, $quantity): bool {
        // Check if the item was already in the cart, and in that case the quantity needs to be updated
        global $sessionHandler;

        $quantityToLoad = $quantity;
        if (isset($_SESSION['cart'][$itemId])) {
            $quantityToLoad += $_SESSION['cart'][$itemId]['quantity'];
        }
        // check if the book with the quantity selected, is available in the database
        $resultQuery = checkBookAvailability($itemId, $quantityToLoad);
        if ($resultQuery !== false) {
            $bookDetails = $resultQuery->fetch_assoc();
            if($bookDetails !== null){
                $_SESSION['cart'][$itemId] = array(
                    'title' => $bookDetails['title'],
                    'author' => $bookDetails['author'],
                    'publisher' => $bookDetails['publisher'],
                    'quantity' => $quantityToLoad,
                    'price' => $bookDetails['price']
                );
                if($sessionHandler->isLogged()){
                    insertOrUpdateItems($_SESSION['cart'], $_SESSION['email'], false);
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
     * @throws Exception
     */
    public function removeItem($itemId): bool {
        global $sessionHandler;
        if (isset($_SESSION['cart'][$itemId])) {
            if( $_SESSION['cart'][$itemId]['quantity'] > 1){
                $_SESSION['cart'][$itemId]['quantity'] --;
                if($sessionHandler->isLogged()){
                    removeItems($itemId, $_SESSION['cart'][$itemId]['quantity'], $_SESSION['email']);
                }
            }
            else{
                unset($_SESSION['cart'][$itemId]);
                if($sessionHandler->isLogged()){
                    removeItems($itemId,0, $_SESSION['email']);
                }
            }
            return true;
        }
        else {
            throw new Exception("The item does not exist in the cart.");
        }
    }

    public function syncShoppingCart(): bool{
        $resultQuery = getShoppingCartBooks($_SESSION['email']);
        if($resultQuery !== false){
            if($resultQuery->num_rows > 0){
                $shoppingCart = array();
                while($rowShoppingCartFromDB = $resultQuery->fetch_assoc()){
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
     * @throws Exception
     */
    public function checkAndUpdateShoppingCartDB(): bool{
        // we check if the cart should be updated or not
        if (!empty($_SESSION['cart'])) {
            if (!insertOrUpdateItems($_SESSION['cart'], $_SESSION['email'], true)) {
                throw new Exception("Error during the update of the Shopping Cart");
            }
        }
        if(!$this->syncShoppingCart()){
            throw new Exception("Error during the synchronization of the Shopping Cart");
        }
        return true;
    }

    /**
     * @throws Exception
     */
    public function clearShoppingCart(): bool{

        global $sessionHandler;
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $itemId => $quantity) {

                if (isset($_SESSION['cart'][$itemId])) {
                    unset($_SESSION['cart'][$itemId]);
                    if ($sessionHandler->isLogged()) {
                        removeItems($itemId, 0, $_SESSION['email']);
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