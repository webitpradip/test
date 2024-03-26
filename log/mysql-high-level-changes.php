
<?php require_once 'menu.php';?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Database Configuration Form</title>
</head>
<body>
    <form action="" method="post">
        <label for="host">Host:</label>
        <input type="text" id="host" name="host" value="<?php echo isset($_POST['host']) ? ($_POST['host']) : ''; ?>">
        <br>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? ($_POST['username']) : ''; ?>">
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" value="<?php echo isset($_POST['password']) ? ($_POST['password']) : ''; ?>">
        <br>
        <label for="database">Database:</label>
        <input type="text" id="database" name="database" value="<?php echo isset($_POST['database']) ? ($_POST['database']) : ''; ?>">
        <br>
        <label for="database">Time in Minutes:</label>
        <input type="text" id="time" name="time" value="<?php echo isset($_POST['time']) ? ($_POST['time']) : ''; ?>">
        <br>
        <button type="submit">Submit</button>
    </form>
    <?php 
    
    if($_POST){
        
        $host = isset($_POST['host']) ? $_POST['host'] : null;
        $username = isset($_POST['username']) ? $_POST['username'] : null;
        $password = isset($_POST['password']) ? $_POST['password'] : null;
        $database = isset($_POST['database']) ? $_POST['database'] : null;
        $time = isset($_POST['time']) ? $_POST['time'] : null;

        // Connect to the MySQL database
        $mysqli = new mysqli($host, $username, $password, $database);
        
        // Check connection
        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }
        
        // The SQL query to get tables that have been created or updated in the last 5 minutes
        $sql = "SELECT TABLE_SCHEMA, TABLE_NAME, CREATE_TIME, UPDATE_TIME
                FROM information_schema.TABLES
                WHERE (UPDATE_TIME >= NOW() - INTERVAL ".$time." MINUTE
                       OR CREATE_TIME >= NOW() - INTERVAL ".$time." MINUTE)
                  AND TABLE_SCHEMA = '".$database."' and TABLE_NAME not like 'innodb%' "; // Exclude system databases
        
        // Execute the query
        $result = $mysqli->query($sql);
        
        // Check if the query was successful
        if ($result) {
            // Fetch and display each row of the result
            while ($row = $result->fetch_assoc()) {
                echo "<br/>";
                echo "Schema: " . $row["TABLE_SCHEMA"] . ", Table: " . $row["TABLE_NAME"] .
                     ", Created: " . $row["CREATE_TIME"] . ", Last Updated: " . $row["UPDATE_TIME"] . "\n";
            }
            } else {
                echo "Error: " . $mysqli->error;
            }
            
            // Close the connection
            $mysqli->close();
    
        
    }
    ?>
</body>
</html>

