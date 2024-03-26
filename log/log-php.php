<?php require_once 'menu.php';?>
<br/>
require_once('<?php echo __DIR__. DIRECTORY_SEPARATOR ; ?>log.php');
<br/>
logToFile($variable);
<h2 align="center">Or</h2>
file_get_contents('http://localhost/gen/log/service.php', false, stream_context_create(['http' => ['method' => 'POST', 'header' => "Content-type: application/x-www-form-urlencoded\r\n", 'content' => json_encode($variable)]]));
<h2 align="center">Or</h2>
curl -X POST -d "This is a test data entry." http://localhost/gen/log/service.php

