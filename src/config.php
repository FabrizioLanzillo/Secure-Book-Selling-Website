<?php

    $listeningPort = 8000;
    define("PROJECT_ROOT", $_SERVER["DOCUMENT_ROOT"]);
    define("SERVER_ROOT", $_SERVER["SERVER_NAME"].":".$listeningPort);

    // echo "project_root:" . PROJECT_ROOT . "<br>";
    // echo "server_root:" . SERVER_ROOT . "<br>";
?>