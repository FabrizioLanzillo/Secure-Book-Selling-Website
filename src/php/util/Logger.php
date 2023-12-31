<?php

    class Logger {

        private $filePath;
        private $debug;

        public function __construct($filePath, $debug) {
            $this->filePath = $filePath;
            $this->debug = $debug;
        }

        /**
         * Function that write INFO, ERROR or WARNING type of message
         * in the /src/logs/web_server_logs.txt file
         * the message has more detail if the variable debug is enabled
         */
        public function writeLog($logType, $message, $file = null, $errorCode = null, $details = null): void{

            $debugFile = null;
            $debugErrorCode = null;
            $debugDetails = null;

            if($file != null){
                $debugFile = $this->debug ? "[File: ".$file."] - " : "";
            }
            if($errorCode != null){
                $debugErrorCode = $this->debug ? " - [Error: ".$errorCode."]" : "";
            }
            if($details != null){
                $debugDetails = $this->debug ? " [Details: ".$details."]" : "";
            }

            $timestamp = date("Y-m-d H:i:s");
            $logFile = fopen($this->filePath, 'a');

            fwrite($logFile, "+[$logType] $timestamp = $debugFile$message$debugErrorCode$debugDetails\n");
            fclose($logFile);
        }
    }