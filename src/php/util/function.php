<?php
global $sessionHandler;

/**
 * This function generates a random string of a given length of chars for the OTP
 * @param $length , is the given length of the chars
 * @return string
 * @throws \Random\RandomException
 */
function generateRandomString($length): string
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return $randomString;
}

/**
 * This message makes the alert for a given message
 * @param $textMessage , is the given message
 * @return void
 */
function showInfoMessage($textMessage): void
{
    echo '<script>alert("' . htmlspecialchars($textMessage) . '");</script>';
}

/**
 * This function checks if the given POST fields are set and are not empty
 * @param $requiredFields , is an array of POST fields
 * @return bool
 */
function checkFormData($requiredFields): bool
{
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            return false;
        }
    }
    return true;
}

function updateBlockLoginInformation($failedAccessesCounter, $blockedTime, $email): array
{
    return array(
        $failedAccessesCounter,
        $blockedTime,
        $email,
    );
}
