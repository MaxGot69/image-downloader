<?php

// Файл с ссылками
$file = 'links.txt';
// Папка для сохранения изображений
$saveDir = 'images/';
// Лог-файл
$logFile = 'download.log';

// Проверяем, существует ли папка для сохранения изображений
if (!is_dir($saveDir)) {
    mkdir($saveDir, 0777, true);
}

// Функция для логирования
function logMessage($message) {
    global $logFile;
    file_put_contents($logFile, $message . PHP_EOL, FILE_APPEND);
}

// Чтение ссылок из файла
$links = file($file, FILE_IGNORE_NEW_LINES);

if (!$links) {
    logMessage('Ошибка: не удалось прочитать ссылки из файла.');
    exit;
}

logMessage('Запуск: ' . date('Y-m-d H:i:s'));

foreach ($links as $link) {
    $filename = basename($link);
    $filepath = $saveDir . $filename;

    // Проверяем, существует ли файл
    if (file_exists($filepath)) {
        logMessage("Файл уже существует: $filename");
        continue;
    }

    // Получаем заголовки ответа с помощью curl
    $ch = curl_init($link);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Если ошибка 404, логируем и пропускаем
    if ($httpCode == 404) {
        logMessage("Ошибка: $link (код HTTP 404)");
        continue;
    }

    // Загружаем файл, если код не 404
    $ch = curl_init($link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $fileData = curl_exec($ch);
    curl_close($ch);

    if ($fileData) {
        file_put_contents($filepath, $fileData);
        logMessage("Скачан файл: $filepath");
    } else {
        logMessage("Ошибка при скачивании файла: $link");
    }
}

logMessage('Завершено: ' . date('Y-m-d H:i:s'));
?>
