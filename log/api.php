<?php header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");


$data = json_decode(file_get_contents('php://input'), true); // Retrieve JSON data from POST request

if ($data !== null) {
    $filename = 'jsdata.txt'; // File name to store the data
    $filename = __DIR__.'/../public/'. $filename;
    // Store data in a text file
    file_put_contents($filename, json_encode($data) . PHP_EOL, FILE_APPEND);
    echo 'Data stored successfully.';
} else {
    echo 'Error: Invalid data received.';
}
?>
