<?php require_once 'menu.php';?>
<?php
function remove_logging($filePath) {
    $content = file_get_contents($filePath);
    $lines = explode("\n", $content);
    $newContent = [];

    foreach ($lines as $line) {
        if (!preg_match('/error_log\(/', $line)) {
            $newContent[] = $line;
        }
    }

    file_put_contents($filePath, implode("\n", $newContent));
	echo "<br/>Done with file path:".$filePath;
}

// Usage
remove_logging("C:\\docker\\ejob\\blog.php");
?>
