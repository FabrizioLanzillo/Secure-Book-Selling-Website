<?php

use ZxcvbnPhp\Zxcvbn;

global $logger;

class InputValidation
{
    private static ?InputValidation $instance = null;

    private $zxcvbn;

    /**
     * Private constructor to ensure a single instance of the InputValidation class.
     * Requires the autoload file and initializes the zxcvbn password strength estimator.
     */
    private function __construct()
    {
        require '/home/bookselling/composer/vendor/autoload.php';

        $this->zxcvbn = new Zxcvbn();
    }

    /**
     * Retrieves the singleton instance of the InputValidation class.
     * If the instance does not exist, creates a new one.
     * @return InputValidation|self|null
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /***************************************** password validation ************************************************/

    /**
     * Validates the strength of a password based on specified criteria and checks for the presence
     * of personal information such as username, email, name, and surname within the password.
     *
     * @param string $password The password to be validated.
     * @param string $email The email associated with the user.
     * @param string $username The username associated with the user.
     * @param string|null $name The name associated with the user (nullable).
     * @param string|null $surname The surname associated with the user (nullable).
     *
     * @return bool Returns true if the password meets the strength criteria and does not contain
     *              personal information; otherwise, returns false.
     */
    private function regexControl($password, $email, $username, $name, $surname): bool
    {
        // Password length and characteristics check
        $isPasswordStrong = strlen($password) >= 9
            && preg_match('/[A-Z]/', $password)
            && preg_match('/[a-z]/', $password)
            && preg_match('/[0-9]/', $password)
            && preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password);

        // Check if the password contains personal information
        $containsPersonalInfo = stripos($password, $username) !== false
            || stripos($password, $email) !== false
            || stripos($password, $name) !== false
            || stripos($password, $surname) !== false;

        return $isPasswordStrong && !$containsPersonalInfo;
    }

    /**
     * Validates the strength of a password using a two-step process:
     * 1. Performs a regular expression check on the password length and characteristics,
     *    ensuring it meets specific criteria such as minimum length, uppercase, lowercase,
     *    numeric, and special character requirements. Additionally, checks for the presence
     *    of personal information within the password (username, email, name, surname).
     * 2. Utilizes the zxcvbn library to assess the password strength and provides a score.
     *
     * @param string $password The password to be validated.
     * @param string $email The email associated with the user.
     * @param string $username The username associated with the user.
     * @param string|null $name The name associated with the user (nullable).
     * @param string|null $surname The surname associated with the user (nullable).
     *
     * @return bool Returns true if the password passes both the regular expression and zxcvbn checks,
     *              indicating it is strong and not easily guessable. Otherwise, throws an Exception
     *              with a descriptive message explaining the weakness and prompts the user to choose
     *              a stronger password.
     *
     * @throws Exception Throws an exception if the password fails the strength checks, providing
     *                   information on why it is considered weak.
     */
    public function checkPasswordStrength($password, $email, $username, $name, $surname): bool
    {
        global $logger;

        // check password regex
        if ($this->regexControl($password, $email, $username, $name, $surname)) {

            // check zxcvbn password strengh function
            $strengthResult = $this->zxcvbn->passwordStrength($password);

            if ($strengthResult['score'] < 3) {
                $logger->writeLog('WARNING', 'Password: ' . $password . ' Score: ' . $strengthResult['score'] .
                    ' Feedback: ' . implode(', ', $strengthResult['feedback']['suggestions']));

                throw new Exception('The password is too weak. Please choose a stronger password.');
            }
            // all checks passed
            return true;

        } else {
            $logger->writeLog('WARNING', "Password does not pass the regex test");
            throw new Exception('The password is too weak. Please choose a stronger password.');
        }
    }


    /*************************************** payment method validation ************************************************************/

    /**
     * Identifies the type of credit card based on its prefix.
     *
     * @param string $creditCardNumber The credit card number to analyze.
     *
     * @return string Returns the type of credit card (e.g., VISA, MasterCard, American Express, or Other).
     */
    private function checkCreditCardPrefix($creditCardNumber): string
    {
        // Define the prefixes known for some major credit cards
        $visaPrefixes = ['4'];
        $mastercardPrefixes = ['51', '52', '53', '54', '55'];
        $amexPrefixes = ['34', '37'];

        // Extract the first two characters of the card number for comparison
        $prefix = substr($creditCardNumber, 0, 2);

        // Verify the prefix against known prefixes
        if (in_array($prefix, $visaPrefixes)) {
            return 'VISA';
        } elseif (in_array($prefix, $mastercardPrefixes)) {
            return 'MasterCard';
        } elseif (in_array($prefix, $amexPrefixes)) {
            return 'American Express';
        } else {
            return 'Other';
        }
    }

    /**
     * Validates a credit card number using the Luhn Algorithm (mod-10) and checks its length and prefix.
     *
     * @param string $creditCardNumber The credit card number to be validated.
     *
     * @return bool Returns true if the credit card number is valid, considering length, Luhn Algorithm, and prefix;
     *              otherwise, returns false.
     */
    private function validateCreditCard($creditCardNumber): bool
    {
        // Remove any spaces, dashes, or other separators from the card number
        $creditCardNumber = preg_replace('/\D/', '', $creditCardNumber);

        // Check if the card number has a length between 13 and 19 digits
        if (!preg_match('/^\d{13,19}$/', $creditCardNumber)) {
            return false;
        }

        // Luhn Algorithm (mod-10)
        $sum = 0;
        $length = strlen($creditCardNumber);

        for ($i = $length - 1; $i >= 0; $i--) {
            $digit = (int)$creditCardNumber[$i];

            // Multiply every second digit by 2, starting from the right
            if (($length - $i) % 2 === 0) {
                $digit *= 2;

                // If the result is greater than 9, subtract 9
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
        }

        // Check the prefix to determine the card type
        $cardType = $this->checkCreditCardPrefix($creditCardNumber);

        // The card number is valid if the sum is a multiple of 10 and the card type is recognized
        return $sum % 10 === 0 && $cardType !== 'Other';
    }

    /**
     * Validates a CVV (Card Verification Value) to ensure it is a 3 or 4-digit numeric value.
     *
     * @param string $cvv The CVV to be validated.
     * @return bool Returns true if the CVV is valid; otherwise, returns false.
     */
    private function validateCVV($cvv): bool
    {
        // Check if CVV is a 3 or 4-digit numeric value
        return preg_match('/^\d{3,4}$/', $cvv);
    }

    /**
     * Validates the format of a cardholder's name, allowing only letters and spaces.
     *
     * @param string $cardholderName The cardholder's name to be validated.
     * @return bool Returns true if the cardholder's name has a correct format; otherwise, returns false.
     */
    private function validateCardholderName($cardholderName): bool
    {
        // Check if the cardholder's name has a correct format (only letters and spaces)
        return preg_match('/^[A-Za-z\s]+$/', $cardholderName);
    }

    /**
     * Validates the format and expiration date of a credit card.
     *
     * @param string $expirationDate The expiration date of the credit card in MM/YY format.
     * @return bool Returns true if the expiration date is valid and in the future; otherwise, returns false.
     */
    private function validateExpirationDate($expirationDate): bool
    {
        // Check if the expiration date is in the MM/YY format
        if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expirationDate)) {
            return false;
        }

        // Extract month and year from the expiration date
        list($expiryMonth, $expiryYear) = explode('/', $expirationDate);

        // Convert to integers for comparison
        $currentMonth = (int)date('m');
        $currentYear = (int)date('y');

        // Check if the expiration date is in the future
        return ($currentYear < $expiryYear) || ($currentYear == $expiryYear && $currentMonth <= $expiryMonth);
    }

    /**
     * Validates a set of payment method details, including cardholder name, credit card number,
     * expiration date, and CVV.
     *
     * @param string $cardholder The cardholder's name.
     * @param string $card The credit card number.
     * @param string $expire The expiration date in MM/YY format.
     * @param string $cvv The CVV (Card Verification Value).
     *
     * @return bool Returns true if all payment method details are valid; otherwise, throws an exception.
     * @throws Exception Throws an exception with an error message for any validation failure.
     */
    public function validatePaymentMethod($cardholder, $card, $expire, $cvv): bool
    {
        if (!$this->validateCardholderName($cardholder)) {
            throw new Exception('Invalid cardholder name.');
        }

        if (!$this->validateCreditCard($card)) {
            throw new Exception('Invalid credit card number.');
        }

        if (!$this->validateExpirationDate($expire)) {
            throw new Exception('Invalid expiration date.');
        }

        if (!$this->validateCVV($cvv)) {
            throw new Exception('Invalid CVV.');
        }

        return true;
    }

    /********************************************** shipping info validation *********************************************************************/

    /**
     * Validates the format of a name for shipping, allowing only letters and spaces.
     *
     * @param string $name The name to be validated for shipping.
     * @return void
     * @throws Exception Throws an exception with an error message for an invalid name.
     */
    private function validateName($name): void
    {
        // Check if the name has a correct format (only letters and spaces)
        if (!preg_match('/^[A-Za-z\s]+$/', $name)) {
            throw new Exception('Invalid name for shipping.');
        }
    }

    /**
     * Validates a common format for location parameters in shipping information, checking for valid characters and length.
     *
     * @param string $value The value to be validated.
     * @param string $errorMessage The error message to be thrown for invalid values.
     * @return void
     * @throws Exception Throws an exception with the specified error message for an invalid value.
     */
    private function validateCommonFormat($value, $errorMessage): void
    {
        // Validate the common format for location parameters in shipping info
        // Check for valid characters, length, etc.
        if (!preg_match('/^[A-Za-z0-9\s.,-]+$/i', $value)) {
            throw new Exception($errorMessage);
        }
    }

    /**
     * Validates the format of a CAP (postal code) for shipping, ensuring it is exactly 5 digits.
     *
     * @param string $cap The CAP (postal code) to be validated for shipping.
     * @return void
     * @throws Exception Throws an exception with an error message for an invalid CAP.
     */
    private function validateCap($cap): void
    {
        // Check if CAP is exactly 5 digits
        if (!preg_match('/^\d{5}$/', $cap)) {
            throw new Exception('Invalid CAP for shipping.');
        }
    }

    /**
     * Validates shipping information, including name, address, city, province, CAP, and country.
     *
     * @param string $name The name for shipping.
     * @param string $address The address for shipping.
     * @param string $city The city for shipping.
     * @param string $province The province for shipping.
     * @param string $cap The CAP (postal code) for shipping.
     * @param string $country The country for shipping.
     * @return bool Returns true if all validations pass; otherwise, throws an exception.
     * @throws Exception Throws an exception with an error message for any validation failure.
     */
    public function validateShippingInformation($name, $address, $city, $province, $cap, $country): bool
    {
        $this->validateName($name);
        $this->validateCommonFormat($address, 'Invalid address for shipping.');
        $this->validateCommonFormat($city, 'Invalid city for shipping.');
        $this->validateCommonFormat($country, 'Invalid country for shipping.');
        $this->validateCap($cap);
        $this->validateCommonFormat($province, 'Invalid province for shipping.');

        return true; // All validations passed
    }

}