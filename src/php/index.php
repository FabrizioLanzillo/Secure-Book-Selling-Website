<?php echo "Hello there, this is a test for the web server";?>

<?php echo "<br>" ?>

<?php
    // index.php
    // Il nome del servizio definito in >docker-compose.yml.
    $host = 'mysql-server';
    $user = 'SNH';
    $pass = 'SNH_USER_PASSWORD';
    $conn = new mysqli($host, $user, $pass);
    if ($conn->connect_error) {    
        die("Connection failed: " . $conn->connect_error);
    } 
    else {   
        echo "This is a mysql test: Connected to MySQL server successfully!";
    }
?>
