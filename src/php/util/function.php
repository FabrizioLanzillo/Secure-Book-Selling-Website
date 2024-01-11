<?php
global $sessionHandler;

use JetBrains\PhpStorm\NoReturn;

function generateRandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function showInfoMessage($textMessage){
    echo '<script>alert("'.$textMessage.'");</script>';
}

function checkFormData($requiredFields): bool{
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            return false;
        }
    }
    return true;
}