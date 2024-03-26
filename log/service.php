<?php header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Specify the file path where data will be saved
    $filename = 'service_data.txt'; // File name to store the data
    $filename = __DIR__.'/../public/'. $filename;

    // Retrieve data from the POST request
    // This example assumes the data is sent as a raw string in the body of the request
    // For form data, use $_POST['fieldName'] instead
    $data = file_get_contents('php://input');
    $data = json_decode($data,true);
    ob_start();
    print_r($data);
    $dataNew = ob_get_clean();

    file_put_contents($filename, $dataNew . PHP_EOL, FILE_APPEND | LOCK_EX);
   
}
?>
