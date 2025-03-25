<?php
require_once 'src/FileProcessor.php';
require_once 'src/ImageSaver.php';
require_once 'src/Logger.php';

// Инициализируем обработчики и логгер
$fileProcessor = new FileProcessor('links.txt');
$imageSaver = new ImageSaver('images');
$logger = new Logger('logs/image_download_log.txt', 3); // Уровень логирования: отладочный

$logger->writeLog("Запуск: " . date('Y-m-d H:i:s'));
$links = $fileProcessor->getLinksFromFile();
$logger->writeLog("Количество ссылок: " . count($links));

$downloaded = 0;
$skipped = 0;
$errors = 0;

foreach ($links as $url) {
    if (!empty($url)) {
        $result = $imageSaver->saveImage($url);
        if ($result) {
            $logger->writeLog("Скачано: $url", 2);
            $downloaded++;
        } else {
            $logger->writeLog("Пропущено: $url", 2);
            $skipped++;
        }
    } else {
        $errors++;
        $logger->writeLog("Ошибка с URL: $url", 2);
    }
}

$logger->writeLog("Завершение: " . date('Y-m-d H:i:s'));
$logger->writeLog("Итого - Скачано: $downloaded, Пропущено: $skipped, Ошибок: $errors");
?>
