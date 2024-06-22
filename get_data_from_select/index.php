<?php
include 'config.php';
include 'functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MySQL Join Query Processor</title>
</head>
<body>
<?php require_once('menu.php') ?>
    <form method="post" action="">
        <label for="query">Enter MySQL Join Query:</label><br>
        <textarea id="query" name="query" rows="10" cols="80"><?php echo isset($_POST) && isset($_POST['query']) ? $_POST['query'] :'' ?></textarea><br>
        <input type="submit" value="Submit">
    </form>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $query = $_POST['query'];

    $tables = getTablesFromQuery($query);
    $tables=array_values($tables);
    $primaryKeys = [];
    foreach ($tables as $table) {
        $primaryKeys[$table] = getPrimaryKeyFieldName($conn, $table);
    }
    $joinKeys = [];
    foreach ($tables as $table) {
        $joinKeys[$table] = getJoiningFieldName($query, $table);
    }
    $joinClause = getJoinClauses($query);
    
  
    $dataHierarchy = [];
    getHierarchicalData($conn, $tables, $joinClause, $joinKeys, $dataHierarchy);

    echo "<pre>";
    print_r($dataHierarchy);
    echo "</pre>";
}
?>

