<?php

require_once 'src/FileProcessor.php';
require_once 'src/ImageSaver.php';
require_once 'src/Logger.php';


$logFile = 'logs/log_' . date('Y-m-d_H-i-s') . '.txt'; 
$logger = new Logger($logFile, 3); 

$logger->writeLog("Запуск скрипта: " . date('Y-m-d H:i:s')); 


$fileProcessor = new FileProcessor('links.txt'); 
$imageSaver = new ImageSaver('images'); 


$links = $fileProcessor->getLinksFromFile();
$logger->writeLog("Количество ссылок: " . count($links));

$downloaded = 0;
$skipped = 0;
$errors = 0;

foreach ($links as $url) {
    if (!empty($url)) { 
        $responseCode = get_headers($url, associative: 1)[0];
        $logger->writeLog("Ответ от сервера для {$url}: {$responseCode}"); 
        if ($responseCode == 404) {
            $logger->writeLog("Ошибка 404: {$url} не найдено на сервере"); 
            continue; 
        }

        
        if (!is_dir('images')) {
            mkdir('images', 0777, true); 
        }

        
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


$logger->writeLog("Завершение скрипта: " . date('Y-m-d H:i:s'));
$logger->writeLog("Итого - Скачано: $downloaded, Пропущено: $skipped, Ошибок: $errors");

