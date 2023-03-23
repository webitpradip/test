<?php
    function fetchVal($key){
        if(isset($_REQUEST[$key])){
            return $_REQUEST[$key];
        }
        return '';
    }
?>
<html>
    <head>
        <title>Mysql Log</title>
    </head>
    <body>
        SET GLOBAL log_output = 'TABLE'; <br/>
        SET GLOBAL general_log = 'ON';<br/>
        mysql.general_log<br/>

        If you prefer to output to a file instead of a table:<br/>
        SET GLOBAL log_output = "FILE"; the default.<br/>
        SET GLOBAL general_log_file = "/path/to/your/logfile.log";<br/>
        SET GLOBAL general_log = 'ON';<br/>


        SELECT * FROM  mysql.general_log  WHERE command_type ='Query' 
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
            Last Seconds Data:
            <input name="seconds" type="text" value="<?php echo fetchVal('seconds'); ?>"/>
            <br/>
            <!-- Show Each Query Result:
            <input name="show_result" type="checkbox" value="1" <?php echo fetchVal('show_result')==1? "checked='checked'":''; ?> />
            <br/> -->

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

    class db
    {

        protected $connection;
        protected $query;
        protected $show_errors  = true;
        protected $query_closed = true;
        public $query_count     = 0;

        public function __construct($dbhost = 'localhost', $dbuser = 'root', $dbpass = '', $dbname = '', $charset = 'utf8')
        {
            $this->connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
            if ($this->connection->connect_error) {
                $this->error('Failed to connect to MySQL - ' . $this->connection->connect_error);
            }
            $this->connection->set_charset($charset);
        }

        public function query($query)
        {
            if (!$this->query_closed) {
                $this->query->close();
            }
            if ($this->query = $this->connection->prepare($query)) {
                if (func_num_args() > 1) {
                    $x        = func_get_args();
                    $args     = array_slice($x, 1);
                    $types    = '';
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
                $this->query_closed = false;
                $this->query_count++;
            } else {
                $this->error('Unable to prepare MySQL statement (check your syntax) - ' . $this->connection->error);
            }
            return $this;
        }

        public function fetchAll($callback = null)
        {
            $params = array();
            $row    = array();
            $meta   = $this->query->result_metadata();
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
                    if ($value == 'break') {
                        break;
                    }
                } else {
                    $result[] = $r;
                }
            }
            $this->query->close();
            $this->query_closed = true;
            return $result;
        }

        public function fetchArray()
        {
            $params = array();
            $row    = array();
            $meta   = $this->query->result_metadata();
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
            $this->query_closed = true;
            return $result;
        }

        public function close()
        {
            return $this->connection->close();
        }

        public function numRows()
        {
            $this->query->store_result();
            return $this->query->num_rows;
        }

        public function affectedRows()
        {
            return $this->query->affected_rows;
        }

        public function lastInsertID()
        {
            return $this->connection->insert_id;
        }

        public function error($error)
        {
            if ($this->show_errors) {
                exit($error);
            }
        }

        private function _gettype($var)
        {
            if (is_string($var)) {
                return 's';
            }

            if (is_float($var)) {
                return 'd';
            }

            if (is_int($var)) {
                return 'i';
            }

            return 'b';
        }
    }
    function getResultShowTab($db1,$query,$tableName){
        $db1->query($query);
        $rows = $db1->fetchAll();
        $cols = array_keys($rows[0]);
        createTable($tableName,$cols,$rows);
    }

    function createTable($tableName, $heads, $rows)
    {
        ob_start();
    ?>
        <h1> <?php echo $tableName; ?> </h1>
        <table border="1">
            <tr>
                <?php foreach ($heads as $head) { ?>
                    <th><?php echo $head; ?> </th>
                <?php } ?>
            </tr>
            <?php foreach ($rows as $rows2) { ?>
                <tr>
                    <?php foreach ($rows2 as $row) { ?>
                        <td><?php echo $row; ?> </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
    <?php
        $content = ob_get_clean();
        echo $content;
    }

    $db1 = new db($firstDbHost, $firstDbUser, $firstDbPassword, $firstDbName);
    $db2 = new db($firstDbHost, $firstDbUser, $firstDbPassword, $firstDbName);
    $db1->query("show tables");
    $tables = $db1->fetchAll();
    $orConditions = "";
    foreach ($tables as $table2) {
        foreach ($table2 as $table) {
           if(!empty($orConditions)){ $orConditions.=" or argument like "; }
           $orConditions.="'%".$table."%'";
            
        }
    }
    
    $getColNameSql = "Select * from mysql.general_log  where UNIX_TIMESTAMP(event_time) >= (UNIX_TIMESTAMP() - ".$seconds." ) and (argument like '%select%' or argument like '%update%' or argument like '%delete%' or argument like '%insert%' or argument like '%truncate%' ) and (argument like ".$orConditions.") and (command_type='Execute' or command_type='Query')" ; 
    $getColNameSql.=" and (LOCATE('general_log',argument)=0 or LOCATE('general_log',argument) IS NULL )";
    $db1->query($getColNameSql);
    $rows = $db1->fetchAll();
    $cols = array_keys($rows[0]);
    createTable('Mysql Log',$cols,$rows);
    // if($showResult){
    //     foreach($rows as $row){
    //         $isNotLogQuery = isset($row['argument']) && !empty($row['argument']) && !strpos($row['argument'],'general_log');
    //         if($isNotLogQuery){
    //             getResultShowTab($db2,$row['argument'],$row['argument']);
    //         }
    //     }
    // }
  }
?>

    </body>
</html>