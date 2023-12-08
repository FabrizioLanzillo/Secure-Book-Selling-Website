<?php

    class Logger {

        private $filePath;

        public function __construct($filePath) {
            $this->filePath = $filePath;
        }

        /**
         * Function that write INFO, ERROR or WARNING type of message
         * in the /src/logs/web_server_logs.txt file
         */
        public function writeLog($logType, $message): void{
            $timestamp = date("Y-m-d H:i:s");
            $logFile = fopen($this->filePath, 'a');

            fwrite($logFile, "+[$logType] $timestamp = $message\n");
            fclose($logFile);
        }
    }