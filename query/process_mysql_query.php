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
    <div class="container">
        <h1>SQL Query Processor</h1>
        <form id="sqlForm" method="post">
            <textarea name="sql_query" id="sql_query" rows="10" cols="50" placeholder="Enter your SQL query here..."></textarea><br>
            <button type="submit">Process Query</button>
        </form>
        <div id="result">
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $sql_query = $_POST['sql_query'];

                // Extract the part of the query after "FROM"
                $from_part = '';
                if (preg_match('/FROM\s+(.+?)(\s+(WHERE|GROUP BY|HAVING|ORDER BY|LIMIT)|$)/is', $sql_query, $matches)) {
                    $from_part = trim($matches[1]);
                }

                // Extract table names including subqueries
                $table_names = [];
                if (preg_match_all('/(?:\bFROM\b|\bJOIN\b)\s+((?:\((?:[^()]+|(?R))*\)|[^\s,]+)(?:\s+AS\s+[^\s,]+)?)/is', $sql_query, $matches)) {
                    $table_names = $matches[1];
                }

                // Store the results in variables
                $from_part_var = $from_part;
                $table_names_var = $table_names;

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
