<?php

// Файл, где хранятся ссылки
$file = 'links.txt';

// Проверка существования файла
if (!file_exists($file)) {
    die("Файл не найден: $file");
}

// Чтение ссылок из файла
$links = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if (!$links) {
    die("Не удалось прочитать ссылки из файла.");
}

// Папка для сохранения изображений
$downloadDir = 'images';

// Проверка существования папки для загрузки
if (!is_dir($downloadDir)) {
    mkdir($downloadDir, 0777, true);
}

// Логирование
$logFile = 'download_log.txt';
$log = fopen($logFile, 'a');
if (!$log) {
    die("Не удалось открыть файл логов для записи.");
}

// Запись лога с датой
$logDate = date("Y-m-d H:i:s");
fwrite($log, "Запуск: $logDate\n");

// Обработка ссылок
$downloadedLinks = [];

foreach ($links as $url) {
    // Генерация имени файла из URL
    $fileName = basename($url);
    $filePath = "$downloadDir/$fileName";

    // Если файл уже существует, пропускаем его
    if (in_array($filePath, $downloadedLinks)) {
        fwrite($log, "Файл уже существует: $filePath\n");
        continue;
    }

    // Инициализация cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Следовать за редиректами
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); // Финальный URL после редиректов
    curl_close($ch);

    // Обработка кода ответа
    if ($httpCode === 301 || $httpCode === 302) {
        fwrite($log, "Редирект: $url => $finalUrl (код HTTP $httpCode)\n");
        // Пытаемся скачать с финальной ссылки
        $fileData = file_get_contents($finalUrl);
        if ($fileData === false) {
            fwrite($log, "Ошибка при скачивании файла: $finalUrl\n");
        } else {
            file_put_contents($filePath, $fileData);
            fwrite($log, "Скачан файл: $filePath\n");
            $downloadedLinks[] = $filePath;
        }
    } elseif ($httpCode === 404) {
        fwrite($log, "Ошибка: $url (код HTTP 404)\n");
    } elseif ($httpCode === 200) {
        // Скачивание файла, если код 200
        $fileData = file_get_contents($url);
        if ($fileData === false) {
            fwrite($log, "Ошибка при скачивании файла: $url\n");
        } else {
            file_put_contents($filePath, $fileData);
            fwrite($log, "Скачан файл: $filePath\n");
            $downloadedLinks[] = $filePath;
        }
    } else {
        fwrite($log, "Неизвестная ошибка при доступе к: $url (код HTTP $httpCode)\n");
    }
}

// Завершение
$logDateEnd = date("Y-m-d H:i:s");
fwrite($log, "Завершено: $logDateEnd\n");
fclose($log);

echo "Процесс завершен.\n";

?>
