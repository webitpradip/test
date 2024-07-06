<?php require_once 'menu.php';?>
<?php
function add_missing_braces($file_path) {
    $lines = file($file_path);
    $new_content = [];
    $inside_if = false;
    $inside_else = false;

    foreach ($lines as $index => $line) {
        // Check for if statements without braces
        if (preg_match('/\s*if\s*\(.*\)\s$/', $line) || preg_match('/\s*if\s*\(.*\)\s*;$/', $line) || preg_match('/\s*if\s*\(.*\)\s*[^{;]*$/',$line)) {
            $new_content[] = $line;
            if (!preg_match('/{/', $line)) {
                // Check next line for opening brace
                if (isset($lines[$index + 1]) && (!preg_match('/\s*{/', $lines[$index + 1]) && trim($lines[$index + 1])!='{' && substr(ltrim($lines[$index + 1]),0,1)!='{') ) {
                    $new_content[] = "{";
                    $inside_if = true;
                }
            }
            continue;
        }

        if ($inside_if) {
            // Find the end of the if statement block
            if (!preg_match('/}/', $line)) {
                $new_content[] = $line;
                $new_content[] = "}";
                $inside_if = false;
                continue;
            }
        }

        // Check for else statements without braces
        if (preg_match('/\s*else\s*$/', $line) || preg_match('/\s*else\s*\s*;$/', $line)) {
            $new_content[] = $line;
            if (!preg_match('/{/', $line)) {
                // Check next line for opening brace
                if (isset($lines[$index + 1]) && !preg_match('/\s*{/', $lines[$index + 1])) {
                    $new_content[] = "{";
                    $inside_else = true;
                }
            }
            continue;
        }

        if ($inside_else) {
            // Find the end of the else statement block
            if (!preg_match('/}/', $line)) {
                $new_content[] = $line;
                $new_content[] = "}";
                $inside_else = false;
                continue;
            }
        }

        $new_content[] = $line;
    }

    // Write the modified content back to the file
    file_put_contents($file_path, implode("", $new_content));
}

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
                    $modified_lines[] = "   __LINE__.error_log('$param: ' . print_r($param, true));";
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
function log_arrays_only($file_path) {
    $lines = file($file_path);
    $new_content = [];

    foreach ($lines as $index => $line) {
        // Check for array assignments
        if (preg_match('/\s*\$[\w]+\s*=\s*array\s*\(.*\)\s*;/', $line) || preg_match('/\s*\$[\w]+\s*=\s*\[.*\]\s*;/', $line)) {
            $new_content[] = $line;
            preg_match('/\s*\$([\w]+)\s*=/', $line, $matches);
            $array_name = $matches[1];
            $new_content[] = "__LINE__.error_log('{$array_name}: ' . print_r(\${$array_name}, true));\n";
        }
        // Check for individual array element assignments
        elseif (preg_match('/\s*\$[\w]+\[[^\]]+\]\s*=/', $line)) {
            $new_content[] = $line;
            preg_match('/\s*\$([\w]+\[[^\]]+\])\s*=/', $line, $matches);
            $array_element = $matches[1];
            $new_content[] = "__LINE__.error_log(\"{$array_element}: \" . print_r(\${$array_element}, true));\n";
        }
        // Check for array elements within function calls
        elseif (preg_match('/\s*[\w]+\s*\(.*\$\w+\[[^\]]+\].*\)\s*;/', $line)) {
            $new_content[] = $line;
            preg_match_all('/\$(\w+\[[^\]]+\])/', $line, $matches);
            foreach ($matches[1] as $array_element) {
                $new_content[] = "__LINE__.error_log(\"{$array_element}: \" . print_r(\${$array_element}, true));\n";
            }
        } else {
            $new_content[] = $line;
        }
    }

    // Write the modified content back to the file
    file_put_contents($file_path, implode("", $new_content));
}
function insert_logging($filePath) {
    add_missing_braces($filePath);
    log_arrays_only($filePath);
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
            $newContent[] = "__LINE__.error_log('$variable: ' . print_r($$variable, true));";
            $newContent[] = "error_log(__FILE__.':'.__LINE__ );";
        }
        // Handle if, for, foreach, while statements with {
        elseif (preg_match('/(if|for|foreach|while)\s*\((.*)\)\s*{/', $line, $matches)) {
            $newContent[] = $line;
            preg_match_all('/\$(\w+)/', $matches[2], $varMatches);
            foreach ($varMatches[1] as $var) {
                $newContent[] = "\t__LINE__.error_log('$var: ' . print_r($$var, true));";
               
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
                    $newContent[] = "\t__LINE__.error_log('$var: ' . print_r($$var, true));";
                   
                }
            } else {
                $newContent[] = "error_log(__FILE__.':'.__LINE__ );";
            }
        }
        // Handle include/require statements
        elseif (preg_match('/(include|require)(_once)?\s*\((.*)\);/', $line, $matches)) {
            $newContent[] = $line;
            $newContent[] = "__LINE__.error_log('Included file: ' . {$matches[3]});";
        }
        // Handle class method calls
        elseif (preg_match('/(\$[\w]+)->([\w]+)\((.*)\);/', $line, $matches)) {
            $newContent[] = $line;
            $newContent[] = "__LINE__.error_log('Method call: {$matches[2]}');";
            if ($matches[3]) {
                preg_match_all('/\$(\w+)/', $matches[3], $varMatches);
                foreach ($varMatches[1] as $var) {
                    $newContent[] = "__LINE__.error_log('$var: ' . print_r($$var, true));";
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
                        $newContent[] = "\t__LINE__.error_log('$var: ' . print_r($$var, true));";
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
insert_logging("C:\\Users\\webit\\Downloads\\auth.php");
?>
