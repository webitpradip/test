<?php require_once 'menu.php' ?>
<?php
    function fetchVal($key){
        if(isset($_REQUEST[$key])){
            return $_REQUEST[$key];
        }
        return '';
    }
    $columnName = '';
?>
<html>
    <head>
        <title>Mysql Log</title>
    </head>
    <body>
          <form method="post">
            Enter Host:
            <input name="host" type="text" value="<?php echo fetchVal('host'); ?>"/>
            <br/>
            Enter Username:
            <input name="username" type="text" value="<?php echo fetchVal('username'); ?>"/>
            <br/>
            Enter Password:
            <input name="password" type="password" value="<?php echo fetchVal('password'); ?>"/>
            <br/>
            Enter DB Name:
            <input name="db" type="text" value="<?php echo fetchVal('db'); ?>"/>
            <br/>
            <textarea name="table_names" rows="10" cols="50"><?php echo fetchVal('table_names'); ?></textarea>
  
            <input type="submit" value="Show" />
        </form>
<?php
if(isset($_POST)){
    $firstDbName     = fetchVal('db');
    $firstDbUser     = fetchVal('username');
    $firstDbPassword = fetchVal('password');
    $firstDbHost     = fetchVal('host');
    $seconds         = fetchVal('seconds');
    $showResult      = fetchVal('show_result');
    $searchTablenames = fetchVal('table_names');



class db {

    protected $connection;
    protected $query;
    protected $show_errors = TRUE;
    protected $query_closed = TRUE;
    public $query_count = 0;

    public function __construct($dbhost = 'localhost', $dbuser = 'root', $dbpass = '', $dbname = '', $charset = 'utf8') {
        $this->connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
        if ($this->connection->connect_error) {
            $this->error('Failed to connect to MySQL - ' . $this->connection->connect_error);
        }
        $this->connection->set_charset($charset);
    }

    public function query($query) {
        if (!$this->query_closed) {
            $this->query->close();
        }
        if ($this->query = $this->connection->prepare($query)) {
            if (func_num_args() > 1) {
                $x = func_get_args();
                $args = array_slice($x, 1);
                $types = '';
                $args_ref = array();
                foreach ($args as $k => &$arg) {
                    if (is_array($args[$k])) {
                        foreach ($args[$k] as $j => &$a) {
                            $types .= $this->_gettype($args[$k][$j]);
                            $args_ref[] = &$a;
                        }
                    } else {
                        $types .= $this->_gettype($args[$k]);
                        $args_ref[] = &$arg;
                    }
                }
                array_unshift($args_ref, $types);
                call_user_func_array(array($this->query, 'bind_param'), $args_ref);
            }
            $this->query->execute();
            if ($this->query->errno) {
                $this->error('Unable to process MySQL query (check your params) - ' . $this->query->error);
            }
            $this->query_closed = FALSE;
            $this->query_count++;
        } else {
            $this->error('Unable to prepare MySQL statement (check your syntax) - ' . $this->connection->error);
        }
        return $this;
    }

    public function fetchAll($callback = null) {
        $params = array();
        $row = array();
        $meta = $this->query->result_metadata();
        while ($field = $meta->fetch_field()) {
            $params[] = &$row[$field->name];
        }
        call_user_func_array(array($this->query, 'bind_result'), $params);
        $result = array();
        while ($this->query->fetch()) {
            $r = array();
            foreach ($row as $key => $val) {
                $r[$key] = $val;
            }
            if ($callback != null && is_callable($callback)) {
                $value = call_user_func($callback, $r);
                if ($value == 'break')
                    break;
            } else {
                $result[] = $r;
            }
        }
        $this->query->close();
        $this->query_closed = TRUE;
        return $result;
    }

    public function fetchArray() {
        $params = array();
        $row = array();
        $meta = $this->query->result_metadata();
        while ($field = $meta->fetch_field()) {
            $params[] = &$row[$field->name];
        }
        call_user_func_array(array($this->query, 'bind_result'), $params);
        $result = array();
        while ($this->query->fetch()) {
            foreach ($row as $key => $val) {
                $result[$key] = $val;
            }
        }
        $this->query->close();
        $this->query_closed = TRUE;
        return $result;
    }

    public function close() {
        return $this->connection->close();
    }

    public function numRows() {
        $this->query->store_result();
        return $this->query->num_rows;
    }

    public function affectedRows() {
        return $this->query->affected_rows;
    }

    public function lastInsertID() {
        return $this->connection->insert_id;
    }

    public function error($error) {
        if ($this->show_errors) {
            exit($error);
        }
    }

    private function _gettype($var) {
        if (is_string($var))
            return 's';
        if (is_float($var))
            return 'd';
        if (is_int($var))
            return 'i';
        return 'b';
    }

}

function createTable($tableName,$heads,$rows){
    ob_start();
    ?>
    <h1> <?php echo $tableName; ?> </h1>
    <table border="1">
        <tr>
            <?php foreach($heads as $head){ ?>
            <th><?php echo $head; ?> </th>
            <?php } ?>
        </tr>
        <?php foreach($rows as $rows2){ ?>
        <tr>
            <?php foreach($rows2 as $row){ ?>
            <td><?php echo $row; ?> </td>
            <?php } ?>
        </tr>
        <?php } ?>
    </table>
    <?php
    $content=ob_get_clean();
    echo $content;
}

$db1=new db($firstDbHost, $firstDbUser,  $firstDbPassword,  $firstDbName);
$db2=new db($firstDbHost, $firstDbUser,  $firstDbPassword,  $firstDbName);
$db3=new db($firstDbHost, $firstDbUser,  $firstDbPassword,  $firstDbName);
$db4=new db($firstDbHost, $firstDbUser,  $firstDbPassword,  $firstDbName);

    $getColNameSql="SELECT DISTINCT TABLE_NAME 
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE COLUMN_NAME like '%".$columnName."%'
        AND TABLE_SCHEMA='".$firstDbName."';";
    
    $db1->query($getColNameSql);
    $tables=$db1->fetchAll();
    $tables=array_column($tables,'TABLE_NAME');
    if(!empty($searchTablenames)){
        $searchTablenames = explode(",",$searchTablenames);
        $tables = array_intersect($tables,$searchTablenames);        
    }

    foreach($tables as $table){
        echo "<h1>".$table."</h1>";

        $getColNameSql="SELECT DISTINCT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA='".$firstDbName."'
         and TABLE_NAME = '".$table."'";
        
        $db4->query($getColNameSql);
        $columnNames = $db4->fetchAll();
        $columnNames = array_column($columnNames,'COLUMN_NAME');
        $firstColumnName = isset($columnNames[0])?$columnNames[0]:'';
        echo "<h5>All Fields:</h5>";
        foreach($columnNames as $columnName){
            echo $columnName.",";
        }
        $db1->query("SHOW KEYS FROM ".$table." WHERE Key_name = 'PRIMARY'");
        $rows=$db1->fetchAll();
        
        echo "<h5>Primary/First Column:</h5>";
        foreach($rows as $row){
            echo $row['Column_name'].",";
        $col = isset($row['Column_name'])?$row['Column_name']:$firstColumnName;
        $db3->query("SELECT TABLE_NAME , COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE COLUMN_NAME = '".$col."' 
        AND TABLE_SCHEMA = '".$firstDbName."'");
        $rows3 = $db3->fetchAll();

        if(count($rows3)>0){
            echo "<h5>Transaction Table:</h5>";
           
            foreach($rows3 as $row3){
                if($table!=$row3['TABLE_NAME']){
                    echo "`".$table.'`.`'.$col."`";
                    echo "`= `".$row3['TABLE_NAME']."`.`".$row3['COLUMN_NAME']."`<br/>";
                }
            }
        }
        echo "==========================================================================================";
        
        }
    }
      
}
  

