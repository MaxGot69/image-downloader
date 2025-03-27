<?php

// Функция для скачивания изображения
function downloadImage($url, $savePath) {
    // Инициализация cURL
    $ch = curl_init($url);
    
    // Установка параметров для cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Следуем за редиректами
    curl_setopt($ch, CURLOPT_HEADER, true); // Получаем заголовки
    curl_setopt($ch, CURLOPT_NOBODY, false); // Загружаем содержимое
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Отключаем проверку SSL для сайтов с самоподписанными сертификатами

    // Выполняем запрос и получаем ответ
    $response = curl_exec($ch);

    // Получаем код ответа
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Проверяем, успешный ли запрос
    if ($httpCode >= 200 && $httpCode < 300) {
        // Получаем тело ответа (содержимое)
        $content = curl_exec($ch);

        // Записываем содержимое в файл
        file_put_contents($savePath, $content);
        echo "Скачано: $url\n";
    } else {
        echo "Ошибка: $url (код HTTP $httpCode)\n";
    }

    // Закрытие cURL соединения
    curl_close($ch);
}

// Чтение ссылок из файла
$links = file('links.txt', FILE_IGNORE_NEW_LINES);
$logFile = 'image_download_log.txt';

// Начало записи лога
$logMessage = "Запуск: " . date('Y-m-d H:i:s') . "\n";
file_put_contents($logFile, $logMessage, FILE_APPEND);

// Перебор всех ссылок
foreach ($links as $url) {
    $savePath = 'images/' . basename($url);

    // Проверяем, если файл уже существует, пропускаем
    if (file_exists($savePath)) {
        echo "Файл уже существует: $savePath\n";
        continue;
    }

    // Скачиваем изображение
    downloadImage($url, $savePath);
    
    // Запись в лог после скачивания
    $logMessage = "Ссылка: $url | Код ответа: HTTP/1.1 " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

$logMessage = "Завершено: " . date('Y-m-d H:i:s') . "\n";
file_put_contents($logFile, $logMessage, FILE_APPEND);

echo "Процесс завершен.\n";
?>
