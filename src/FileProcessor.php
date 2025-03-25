<?php
class FileProcessor {
    private $filePath;

    public function __construct($filePath) {
        $this->filePath = $filePath;
    }

    public function getLinksFromFile() {
        $lines = file($this->filePath, FILE_IGNORE_NEW_LINES);
        return $lines;
    }

    public function createDirectoryIfNotExists($dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true); // Создаём директорию
        }
    }
}
