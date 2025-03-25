<?php
class Logger {
    private $logFile;
    private $logLevel;

    public function __construct($logFile, $logLevel = 1) {
        $this->logFile = $logFile;
        $this->logLevel = $logLevel;
    }

    public function writeLog($message, $level = 1) {
        if ($level <= $this->logLevel) {
            file_put_contents($this->logFile, $message . PHP_EOL, FILE_APPEND);
        }
    }
}
