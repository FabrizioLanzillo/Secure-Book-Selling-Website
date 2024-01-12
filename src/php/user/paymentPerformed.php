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
        <h1><b>Payment done</b></h1>
        <a href="//<?php echo htmlspecialchars(SERVER_ROOT. '/')?>?>"
           class="btn btn-lg btn-primary mt-2">Continue Shopping</a>
	</body>
</html>