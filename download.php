<?php

// Путь к файлу с ссылками
$inputFile = 'input.txt';
// Папка для сохранения изображений
$outputDir = 'downloads';
// Путь к логам
$logFile = 'image_download_log.txt';
// Путь к файлу с ошибками
$errorLogFile = 'errors.log';

// Функция для записи лога
function writeLog($message, $logFile)
{
    $date = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$date] $message\n", FILE_APPEND);
}

// Проверка наличия и создание директорий
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

// Чтение ссылок из файла
$links = file($inputFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Открываем лог для записи
writeLog("Запуск: " . date('Y-m-d H:i:s'), $logFile);

// Переменная для подсчета недоступных ссылок
$errors = 0;

foreach ($links as $link) {
    $url = trim($link);
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        // Инициализация cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // Следуем за редиректами
        curl_setopt($ch, CURLOPT_HEADER, true);  // Получаем заголовки ответа
        curl_setopt($ch, CURLOPT_NOBODY, true);  // Только заголовки

        // Получаем заголовки ответа
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Проверка кода ответа
        if ($statusCode == 200) {
            // Загружаем файл, если ссылка доступна
            $imageData = file_get_contents($url);
            $filename = $outputDir . DIRECTORY_SEPARATOR . basename($url);
            file_put_contents($filename, $imageData);
            writeLog("Ссылка доступна: $url", $logFile);
        } else {
            // Записываем ошибку, если ссылка недоступна
            $errors++;
            writeLog("Ошибка: ссылка недоступна: $url (код HTTP: $statusCode)", $errorLogFile);
        }

        // Закрываем cURL
        curl_close($ch);
    } else {
        writeLog("Некорректная ссылка: $url", $errorLogFile);
    }
}

// Завершение работы
writeLog("Завершено: " . date('Y-m-d H:i:s'), $logFile);
writeLog("Процесс завершен с ошибками: $errors недоступных ссылок.", $logFile);

echo "Процесс завершен с ошибками: $errors недоступных ссылок.\n";

?>
