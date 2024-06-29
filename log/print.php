<?php require_once 'menu.php';?>
<?php
function addErrorLogging($filePath) {
    // Regular expression to match any function definition and capture the parameters
    $code = $content = file_get_contents($filePath);
    $pattern = '/(function\s+\w+\s*\(([^)]*)\)\s*\{)/';

    // Callback function to append error_log after the opening brace
    $callback = function ($matches) {
        $functionHeader = $matches[1];
        $params = $matches[2];

        $errorLog = "    error_log(__FILE__.':'.__LINE__);";

        // Add error logging for each parameter
        if (!empty(trim($params))) {
            $paramList = explode(',', $params);
            foreach ($paramList as $param) {
                $param = trim($param);
                $paramName = $param;
                $paramName = str_replace('$','',$paramName);
                $errorLog .= "\n    error_log('$paramName: ' . print_r($param, true));";
            }
        }

        return $functionHeader . "\n" . $errorLog;
    };

    // Perform the replacement
    $modifiedCode = preg_replace_callback($pattern, $callback, $code);

    file_put_contents($filePath, $modifiedCode); 
}
function add_logging_to_methods($filePath) {
    $content = file_get_contents($filePath);
    $lines = explode("\n", $content);

    $method_pattern = '/function\s+(\w+)\s*\(([^)]*)\)\s*{/'; 
    $param_pattern = '/\$(\w+)/';

    $modified_lines = [];
    $inside_method = false;

    foreach ($lines as $line) {
        $modified_lines[] = $line;

        if (preg_match($method_pattern, $line, $method_match)) {
            // Found a method definition
            $method_name = $method_match[1];
            $params = $method_match[2];

            // Add error_log for the file and line
            $modified_lines[] = "    error_log(__FILE__.':'.__LINE__);";

            // Add error_log for each parameter
            if (preg_match_all($param_pattern, $params, $param_matches)) {
                foreach ($param_matches[0] as $param) {
                    $modified_lines[] = "    error_log('$param: ' . print_r($param, true));";
                    $modified_lines[] = "error_log(__FILE__.':'.__LINE__ );";
                }
            }

            $inside_method = true;
        }
    }
}
    function add_logging_to_methods_new($filePath) {
        $content = file_get_contents($filePath);
        $lines = explode("\n", $content);
    
        $method_pattern = '/function\s+\w+\s*\([^\)]*\)\s*\s*([\s\S]*?)\n\{/'; 
        $param_pattern = '/\$(\w+)/';
    
        $modified_lines = [];
        $inside_method = false;
    
        foreach ($lines as $line) {
            $modified_lines[] = $line;
    
            if (preg_match($method_pattern, $line, $method_match)) {
                // Found a method definition
                $method_name = $method_match[1];
                $params = $method_match[2];
    
                // Add error_log for the file and line
                $modified_lines[] = "    error_log(__FILE__.':'.__LINE__);";
    
                // Add error_log for each parameter
                if (preg_match_all($param_pattern, $params, $param_matches)) {
                    foreach ($param_matches[0] as $param) {
                        $modified_lines[] = "    error_log('$param: ' . print_r($param, true));";
                        $modified_lines[] = "error_log(__FILE__.':'.__LINE__ );";
                    }
                }
    
                $inside_method = true;
            }
        }
    

    // Join modified lines into the new content
    $new_content = implode("\n", $modified_lines);
    file_put_contents($filePath, $new_content);
}
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
               
            }
        }
        // Handle if, for, foreach, while statements without {
        elseif (preg_match('/(if|for|foreach|while)\s*\((.*)\)/', $line, $matches)) {
            $insideBlock = true;
            $blockType = $matches[1];
            $newContent[] = $line;
          
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
            
        }
        // Handle opening { for if, for, foreach, while, else without {
        elseif ($insideBlock && preg_match('/{/', $line)) {
            $insideBlock = false;
            $newContent[] = $line;
            if ($blockType !== 'else') {
                preg_match_all('/\$(\w+)/', $lines[$index-1], $varMatches);
                foreach ($varMatches[1] as $var) {
                    $newContent[] = "\terror_log('$var: ' . print_r($$var, true));";
                   
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
    add_logging_to_methods($filePath);
    add_logging_to_methods_new($filePath);
    addErrorLogging($filePath);
	
    echo "Done with file path:".$filePath;
}

// Usage
insert_logging("C:\\docker\\ejob\\blog.php");
?>
