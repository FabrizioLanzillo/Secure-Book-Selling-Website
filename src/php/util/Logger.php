<?php

/**
 * This class implements the logger mechanism, by writing the log messages on a file
 */
class Logger
{
    private static ?Logger $instance = null;
    private $filePath;
    private $debug;

    /**
     * In the constructor the file path and the debug variables are set
     * @param $filePath , is the file path on which the messages are written
     * @param $debug , is a variable that if it is set to true shows additional details in the messages.
     *                  It serves the purpose of debugging
     */
    private function __construct($filePath, $debug)
    {
        $this->filePath = $filePath;
        $this->debug = $debug;
    }

    /**
     * This method returns the singleton instance of Logger.
     *  If the instance doesn't exist, it creates one; otherwise, it returns the existing instance.
     * @param $filePath , is the file path on which the messages are written
     * @param $debug , is a variable that if it is set to true shows additional details in the messages.
     *                   It serves the purpose of debugging
     * @return Logger
     */
    public static function getInstance($filePath, $debug): ?Logger
    {
        if (self::$instance == null) {
            self::$instance = new Logger($filePath, $debug);
        }
        return self::$instance;
    }

    /**
     * Function that write INFO, ERROR or WARNING type of message
     * in the /src/logs/web_server_logs.txt file
     * the message has more detail if the variable debug is enabled
     */
    /**
     * This function writes the log message, with additional information on the log file
     * @param $logType , is the type of the message, it can be INFO, ERROR or WARNING
     * @param $message , is the message of the log
     * @param $file , optional, is the file where an error or other type of message occurred
     * @param $errorCode , optional, is an error code
     * @param $details , option, are additional details
     * @return void
     */
    public function writeLog($logType, $message, $file = null, $errorCode = null, $details = null): void
    {

        $debugFile = null;
        $debugErrorCode = null;
        $debugDetails = null;

        if ($file != null) {
            $debugFile = $this->debug ? "[File: " . $file . "] - " : "";
        }
        if ($errorCode != null) {
            $debugErrorCode = $this->debug ? " - [Error: " . $errorCode . "]" : "";
        }
        if ($details != null) {
            $debugDetails = $this->debug ? " [Details: " . $details . "]" : "";
        }

        $timestamp = date("Y-m-d H:i:s");
        $logFile = fopen($this->filePath, 'a');

        fwrite($logFile, "+[$logType] $timestamp = $debugFile$message$debugErrorCode$debugDetails\n");
        fclose($logFile);
    }
}
