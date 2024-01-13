<?php
class ErrorHandler {
    private static ?ErrorHandler $instance = null;

    private function __construct() {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
    }

    public static function getInstance(): ?ErrorHandler
    {
        if (self::$instance == null) {
            self::$instance = new ErrorHandler();
        }

        return self::$instance;
    }

    /**
     * @throws ErrorException
     */
    public function handleError($level, $message, $file = '', $line = 0) {
        throw new ErrorException($message, 0, $level, $file, $line);
    }

    public function handleException($exception): void
    {
        echo '<script>
                 alert("'.htmlspecialchars($exception->getMessage()).'");
              </script>';
    }
}
