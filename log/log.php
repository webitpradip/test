<?php
function logToFile($message, $filename = 'log.txt') {
    ob_start();
    echo "<pre>";
    print_r($message);
    $finalMessage = ob_get_clean();
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $finalMessage" . PHP_EOL;
    $filename = __DIR__.'/../public/'. $filename;
    file_put_contents($filename, $logEntry, FILE_APPEND);
}