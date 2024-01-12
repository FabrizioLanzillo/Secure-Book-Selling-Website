<?php
require_once __DIR__ . "/../../config.php";

global $sessionHandler;
global $shoppingCartHandler;
global $errorHandler;
global $accessControlManager;

$items = $shoppingCartHandler->getBooks();

try{
    if (isset($_POST['totalPrice'])) {
        // Protect against XSS
        $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');
        $totalPrice = htmlspecialchars($_POST['totalPrice'], ENT_QUOTES, 'UTF-8');

        if (!$token || $token !== $_SESSION['token']) {
            // return 405 http status code
            header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
            exit;
        } else {
            $currentTime = date('Y-m-d H:i:s');
            $userId = $_SESSION['userId'];
            addItemToOrders($userId, $currentTime, $items, $totalPrice);
        }
    }
}
catch (Exception $e) {
    $errorHandler->handleException($e);
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- <link rel="stylesheet" type="text/css" href="./css/pippo.css"> -->
        <title>Book Selling - Payment done</title></head>
	<body>
<?php
        include "./../layout/header.php";
?>
        <b>Payment done</b><br>
<?php
        echo "ACCIDENTI A ME CHE FO L'UNIVERSITà DIO **** -cit. Hfjqpowfjpq\n";
?>
	</body>
</html>