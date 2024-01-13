<?php
require_once __DIR__ . "/../../config.php";
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!--        <link rel="stylesheet" type="text/css" href="./css/home.css">-->
        <link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
        <title>Book Selling - Home</title>
	<body>
<?php
        include "./../layout/header.php";
?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <h1 class="display-4 text-success"><b>Payment Successful!</b></h1>
                <p class="lead">Thank you for your purchase.</p>
                <a href="//<?php echo htmlspecialchars(SERVER_ROOT. '/')?>" class="btn btn-primary btn-lg mt-3">Continue Shopping</a>
            </div>
        </div>
    </div>

	</body>
</html>