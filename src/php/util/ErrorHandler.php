<?php

/**
 * This class manages errors and exceptions.
 */
class ErrorHandler
{
    private static ?ErrorHandler $instance = null;

    /**
     * in the constructor error and exception handlers are set up during the instantiation.
     */
    private function __construct()
    {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
    }

    /**
     * This method returns the singleton instance of ErrorHandler.
     * If the instance doesn't exist, it creates one; otherwise, it returns the existing instance.
     * @return ErrorHandler
     */
    public static function getInstance(): ?ErrorHandler
    {
        if (self::$instance == null) {
            self::$instance = new ErrorHandler();
        }

        return self::$instance;
    }

    /**
     * This method handles errors by throwing an ErrorException.
     * @param $level , is the level of the error.
     * @param $message , is the error message.
     * @param $file , is the file where the error occurred.
     * @param $line , is the line number where the error occurred.
     * @throws ErrorException
     */
    public function handleError($level, $message, $file = '', $line = 0)
    {
        throw new ErrorException($message, 0, $level, $file, $line);
    }

    /**
     * This method handles exceptions by displaying an alert with the exception message.
     * @param $exception , is the exception to handle.
     * @return void
     */
    public function handleException($exception): void
    {
        echo '<script>
                 alert("' . htmlspecialchars($exception->getMessage()) . '");
              </script>';
    }
}
