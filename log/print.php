<?php require_once 'menu.php';?>
<?php

function insert_logging($filePath) {
    $content = file_get_contents($filePath);
    $lines = explode("\n", $content);
    $newContent = [];
    $insideBlock = false;
    $blockType = '';

    foreach ($lines as $index => $line) {
        // Handle variable assignments
        if (preg_match('/\$(\w+)\s*=\s*(.*);/', $line, $matches)) {
            $newContent[] = $line;
            $variable = $matches[1];
            $newContent[] = "error_log('$variable: ' . print_r($$variable, true));";
            $newContent[] = "error_log(__FILE__.':'.__LINE__ );";
        }
        // Handle if, for, foreach, while statements with {
        elseif (preg_match('/(if|for|foreach|while)\s*\((.*)\)\s*{/', $line, $matches)) {
            $newContent[] = $line;
            preg_match_all('/\$(\w+)/', $matches[2], $varMatches);
            foreach ($varMatches[1] as $var) {
                $newContent[] = "\terror_log('$var: ' . print_r($$var, true));";
                $newContent[] = "error_log(__FILE__.':'.__LINE__ );";
            }
        }
        // Handle if, for, foreach, while statements without {
        elseif (preg_match('/(if|for|foreach|while)\s*\((.*)\)/', $line, $matches)) {
            $insideBlock = true;
            $blockType = $matches[1];
            $newContent[] = $line;
            $newContent[] = "error_log(__FILE__.':'.__LINE__ );";
        }
        // Handle else statements with {
        elseif (preg_match('/else\s*{/', $line)) {
            $newContent[] = $line;
            $newContent[] = "error_log(__FILE__.':'.__LINE__ );";
        }
        // Handle else statements without {
        elseif (preg_match('/else/', $line)) {
            $insideBlock = true;
            $blockType = 'else';
            $newContent[] = $line;
            $newContent[] = "error_log(__FILE__.':'.__LINE__ );";
        }
        // Handle opening { for if, for, foreach, while, else without {
        elseif ($insideBlock && preg_match('/{/', $line)) {
            $insideBlock = false;
            $newContent[] = $line;
            if ($blockType !== 'else') {
                preg_match_all('/\$(\w+)/', $lines[$index-1], $varMatches);
                foreach ($varMatches[1] as $var) {
                    $newContent[] = "\terror_log('$var: ' . print_r($$var, true));";
                    $newContent[] = "error_log(__FILE__.':'.__LINE__ );";
                }
            } else {
                $newContent[] = "error_log(__FILE__.':'.__LINE__ );";
            }
        }
        // Handle include/require statements
        elseif (preg_match('/(include|require)(_once)?\s*\((.*)\);/', $line, $matches)) {
            $newContent[] = $line;
            $newContent[] = "error_log('Included file: ' . {$matches[3]});";
        }
        // Handle class method calls
        elseif (preg_match('/(\$[\w]+)->([\w]+)\((.*)\);/', $line, $matches)) {
            $newContent[] = $line;
            $newContent[] = "error_log('Method call: {$matches[2]}');";
            if ($matches[3]) {
                preg_match_all('/\$(\w+)/', $matches[3], $varMatches);
                foreach ($varMatches[1] as $var) {
                    $newContent[] = "error_log('$var: ' . print_r($$var, true));";
                }
            }
        } else {
            // Check for opening braces { for if, for, foreach, while, else without {
            if ($insideBlock && preg_match('/{/', $line)) {
                $insideBlock = false;
                $newContent[] = $line;
                if ($blockType !== 'else') {
                    preg_match_all('/\$(\w+)/', $lines[$index-1], $varMatches);
                    foreach ($varMatches[1] as $var) {
                        $newContent[] = "\terror_log('$var: ' . print_r($$var, true));";
                    }
                } else {
                    $newContent[] = "\terror_log('__LINE__: ' . __LINE__);";
                }
            } else {
                $newContent[] = $line;
            }
        }
    }

    file_put_contents($filePath, implode("\n", $newContent));
	echo "Done with file path:".$filePath;
}

// Usage
insert_logging("C:\\docker\\ejob\\blog.php");
?>
