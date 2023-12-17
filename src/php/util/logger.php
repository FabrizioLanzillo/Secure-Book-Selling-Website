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
         */
        public function writeLog($logType, $message, $file = null, $errorCode = null): void{

            $debugFile = null;
            $debugLogErrorCode = null;

            if($file != null){
                $debugFile = $this->debug ? "[File: ".$file."] " : "";
            }
            if($errorCode != null){
                $debugLogErrorCode = $this->debug ? "[Error: ".$errorCode."] -" : "";
            }

            $timestamp = date("Y-m-d H:i:s");
            $logFile = fopen($this->filePath, 'a');

            fwrite($logFile, "+[$logType] $timestamp = $debugFile $debugLogErrorCode $message\n");
            fclose($logFile);
        }
    }