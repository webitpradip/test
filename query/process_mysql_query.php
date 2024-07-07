<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "ecommerce";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql_query = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql_query = $_POST['sql_query'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Query Processor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }

        textarea {
            width: 100%;
            margin-bottom: 10px;
        }

        button {
            display: block;
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        #result {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<?php require_once('menu.php') ?>
    <div class="container">
        <h1>SQL Query Processor</h1>
        <form id="sqlForm" method="post">
            <textarea name="sql_query" id="sql_query" rows="10" cols="50" placeholder="Enter your SQL query here..."><?php echo $sql_query; ?></textarea><br>
            <button type="submit">Process Query</button>
        </form>
        <div id="result">
            <?php
function arrayToInsertIgnore($tableName, $row) {
        $row = array_filter($row);
        if(empty($row)){ return ''; }
        $fields = implode(", ", array_keys($row));
        $values = implode(", ", array_map(function ($value) {
            return "'" . addslashes($value) . "'";
        }, array_values($row)));

        return "INSERT IGNORE INTO `$tableName` ($fields) VALUES ($values);";
}

function getJoinings($sql_query) {
    $tables = [];
    $joins = [];
    $keywords = ['INNER JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'FULL JOIN', 'CROSS JOIN', 'FROM'];

    // Normalize the SQL query by removing extra spaces and new lines
    $sql_query = preg_replace('/\s+/', ' ', $sql_query);

    // Split the query into parts based on join keywords
    $parts = preg_split('/\b('.implode('|', $keywords).')\b/i', $sql_query, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

    for ($i = 0; $i < count($parts); $i++) {
        $part = trim($parts[$i]);
        if (in_array(strtoupper($part), $keywords) || strtoupper($part) === 'FROM') {
            $i++;
            $tablePart = trim($parts[$i]);

            // Check if the table part is a subquery with alias
            if (preg_match('/\((SELECT .*?)\)\s+AS\s+(\w+)$/i', $tablePart, $matches)) {
                $subQuery = $matches[1];
                $alias = $matches[2];
                $tables[$alias] = '(' . $subQuery . ')';
            } 
            // Check if the table part is a regular table with alias
            else if (preg_match('/(.*?)\s+(?:AS\s+)?(\w+)$/i', $tablePart, $matches)) {
                $tableName = $matches[1];
                $alias = $matches[2];
                $tables[$alias] = $tableName;
            } 
            // If there's no alias, use the table name as the alias
            else {
                $tables[$tablePart] = $tablePart;
            }

            // Extract join conditions
            if (preg_match('/\s+ON\s+(.*)/i', $parts[$i], $joinMatches)) {
                $joins[] = trim($joinMatches[1]);
            }
        }
    }

    return [
        'tables' => $tables,
        'joins' => $joins
    ];
}            
function getTableNamesWithAliases($sql_query) {
    $tables = [];
    $keywords = ['INNER JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'FULL JOIN', 'CROSS JOIN', 'FROM'];

    // Remove new lines and extra spaces
    $sql_query = preg_replace('/\s+/', ' ', $sql_query);

    // Split the query into parts based on join keywords
    $parts = preg_split('/\b('.implode('|', $keywords).')\b/i', $sql_query, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

    for ($i = 0; $i < count($parts); $i++) {
        $part = trim($parts[$i]);
        if (in_array(strtoupper($part), $keywords) || strtoupper($part) === 'FROM') {
            $i++;
            $tablePart = trim($parts[$i]);

            // Separate table name from alias and ON conditions
            if (preg_match('/(.*?)\s+ON\s+(.*)/i', $tablePart, $matches)) {
                $tablePart = $matches[1];
            }

            // Extract table name and alias
            if (preg_match('/(.*?)\s+(?:AS\s+)?(\w+)$/i', $tablePart, $matches)) {
                $tableName = $matches[1];
                $alias = $matches[2];
                $tables[$alias] = $tableName;
            } else {
                // If there's no alias, use the table name as the alias
                $tables[$tablePart] = $tablePart;
            }
        }
    }

    return $tables;
}

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
               

                // Extract the part of the query after "FROM"
                $from_part = '';
                $from_position = stripos($sql_query,'From');

                if ($from_position) {
                    $from_part = substr($sql_query,$from_position,strlen($sql_query));
                }

                // Extract table names including subqueries
                $table_names = [];
                if (preg_match_all('/(?:\bFROM\b|\bJOIN\b)\s+((?:\((?:[^()]+|(?R))*\)|[^\s,]+)(?:\s+AS\s+[^\s,]+)?)/is', $sql_query, $matches)) {
                    $table_names = $matches[1];
                }

                // Store the results in variables
                $from_part_var = $from_part;
                $table_names_var = $table_names;
                $table_name_with_alias = getTableNamesWithAliases($sql_query);
                $joinings = getJoinings($sql_query);
                $joinings = isset($joinings['joins']) ? $joinings['joins'] : []; 

                foreach($table_name_with_alias as $alias => $fullTableName ){
                    $sql = " Select ".$alias.".* ".$from_part_var;
                    echo "<h5>".$fullTableName."</h5>";
                    $query = $conn->query($sql);
                    if($query->num_rows >0){
                        while($singleTablesData = $query->fetch_assoc()){
                            echo arrayToInsertIgnore($fullTableName,$singleTablesData)."<br/><br/>";
                        }
                    }
                }

                // Output the results
                echo "<p><strong>FROM Part:</strong> " . htmlspecialchars($from_part_var) . "</p>";
                echo "<p><strong>Table Names:</strong> " . htmlspecialchars(implode(', ', $table_names_var)) . "</p>";
               
            }
            ?>
        </div>
    </div>
    <script>
        document.getElementById('sqlForm').addEventListener('submit', function (event) {
            // No AJAX necessary as we handle form submission with PHP directly
        });
    </script>
</body>
</html>
