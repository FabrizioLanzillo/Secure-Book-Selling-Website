<?php
require_once __DIR__ . "/config.php";

global $errorHandler;
global $accessControlManager;
global $sessionHandler;
global $logger;

if ($sessionHandler->isLogged()) {
    // Check path manipulation and broken access control
    // Check if an admin tries to access this page
    $accessControlManager->redirectIfAdmin();
}

try {
    // Sanitize get parameters, this parameter is set after the user makes a purchase, in order to show the outcome
    $paymentResponse = isset($_GET['paymentResponse']) ? htmlspecialchars($_GET['paymentResponse'], ENT_QUOTES, 'UTF-8') : null;
    if ($paymentResponse !== null) {
        if ($paymentResponse === "OK") {
            showInfoMessage("Payment Successful! Thank you for your purchase.");
        } else {
            throw new Exception($paymentResponse);
        }
    }
    // Sanitize get parameters, this parameter is set after the user download, in order to show the outcome
    $downloadBookError = isset($_GET['downloadBookError']) ? htmlspecialchars($_GET['downloadBookError'], ENT_QUOTES, 'UTF-8') : null;
    if ($downloadBookError !== null) {
        throw new Exception($downloadBookError);
    }

} catch (Exception $e) {
    $errorHandler->handleException($e);
}

// Check if the search form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && checkFormData(['search_query'])) {

    // Protect against XSS
    $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');
    $searchQuery = htmlspecialchars($_POST["search_query"], ENT_QUOTES, 'UTF-8');

    // Protect against XSRF
    if (!$token || $token !== $_SESSION['token']) {
        // return 405 http status code
        $accessControlManager->redirectIfXSRFAttack();
    } else {
        $logger->writeLog('INFO', "XSRF control passed");
        $result = searchBooks($searchQuery);
    }
} else {
    $result = getBooks();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" type="text/css" href="./css/bootstrap.min.css">
    <title>Book Selling - Home</title>
</head>
<body>

<?php
include "./php/layout/header.php";
?>

<div class="container mt-5">
    <form class="d-flex" name="search" action="//<?php echo htmlspecialchars(SERVER_ROOT . '/'); ?>" method="POST">
        <input class="form-control me-2" type="text" name="search_query" placeholder="Search for books" required>
        <!-- Hidden token to protect against CSRF -->
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">
        <button class="btn btn-primary" type="submit">Search</button>
    </form>
</div>

<div class="container mt-5" style="max-width: 1440px;">
    <div class="row d-flex align-items-center">
        <?php
        if ($result) {
            if ($result->num_rows >= 1) {
                while ($book = $result->fetch_assoc()) {
                    // Output each book as a card in the grid
                    ?>
                    <div class="col-lg-3 mb-4 ">
                        <a href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/book_details.php?book_id=' . $book['id']); ?>">
                            <div class="card d-flex align-items-center">
                                <img src="/img/books/<?php echo ($book['id'] < 16) ? htmlspecialchars($book['id']) : 16; ?>.jpg"
                                     alt="Book Image" style="width: 100%; height: auto;"> <br>
                                <div class="card-body d-flex flex-column align-items-center">
                                    <h5 class="card-title text-dark">
                                        <?php
                                        $title = $book['title'];
                                        if (strlen($title) > 30) {
                                            $title = substr($title, 0, 30) . '...';
                                        }
                                        echo htmlspecialchars($title);
                                        ?>
                                    </h5>
                                    <p class="card-text text-dark"><?php echo htmlspecialchars($book['author']) ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class='alert alert-danger mt-4'>Book not found in the database</div>
                <?php
            }
        } else {
            ?>
            <div class='alert alert-danger mt-4'>Error retrieving book details</div>
            <?php
        }
        ?>
    </div>
</div>
</body>
</html>