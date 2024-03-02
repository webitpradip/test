<?php require_once 'menu.php';?>
<br/>
<textarea rows="30" cols="120">
function sendDataToPHP(data,url) {
        const xhr = new XMLHttpRequest();
        // Configure the request
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-Type', 'application/json');

        // Define behavior for when the request completes
        xhr.onload = function () {
            if (xhr.status === 200) {
                console.log('Data sent successfully.');
            } else {
                console.error('Request failed. Status:', xhr.status);
            }
        };

        // Handle network errors
        xhr.onerror = function () {
            console.error('Network error occurred.');
        };

        // Convert data object to JSON string and send the request
        const jsonData = JSON.stringify(data);
        xhr.send(jsonData);
    }
</textarea>
<br>
 sendDataToPHP(variableName,'http://localhost/gen/log/api.php');