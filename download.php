<?php
// Устанавливаем локальные файлы логов с абсолютными путями
$logFile = __DIR__ . '/logs/Image_download_log.txt';
$errorLogFile = __DIR__ . '/logs/errors.log';

// Путь к файлу с ссылками
$linksFile = __DIR__ . '/links.txt';

// Проверка наличия файла с ссылками
if (!file_exists($linksFile)) {
    echo "Файл с ссылками не найден: $linksFile\n";
    exit;
}

// Чтение ссылок из файла
$links = file($linksFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Если файл пуст
if ($links === false) {
    echo "Не удалось прочитать файл с ссылками: $linksFile\n";
    exit;
}

echo "Запуск: " . date("Y-m-d H:i:s") . "\n";
echo "Количество ссылок: " . count($links) . "\n";

// Логирование начала процесса
file_put_contents($logFile, "Запуск: " . date("Y-m-d H:i:s") . "\n", FILE_APPEND);

// Функция для проверки доступности ссылки с помощью cURL
function checkUrl($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);  // Мы не загружаем содержимое, только проверяем доступность
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);  // Таймаут 10 секунд
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Следуем за редиректами

    $response = curl_exec($ch);

    // Получаем код ответа HTTP
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $httpCode;
}

$failedLinks = 0;  // Считаем неудачные ссылки

// Проходим по всем ссылкам
foreach ($links as $url) {
    $httpCode = checkUrl($url);

    if ($httpCode >= 200 && $httpCode < 300) {
        // Если ссылка доступна
        echo "Ссылка доступна: $url\n";
    } else {
        // Если ссылка не доступна
        $failedLinks++;
        echo "Ошибка: ссылка недоступна: $url\n";

        // Логируем ошибку
        file_put_contents($errorLogFile, "Ошибка: ссылка недоступна: $url\n", FILE_APPEND);
    }
}

echo "Завершено: " . date("Y-m-d H:i:s") . "\n";
file_put_contents($logFile, "Завершено: " . date("Y-m-d H:i:s") . "\n", FILE_APPEND);

if ($failedLinks > 0) {
    echo "Процесс завершен с ошибками: $failedLinks ссылок недоступны.\n";
} else {
    echo "Процесс завершен успешно, все ссылки доступны.\n";
}
?>
