<?php require_once 'menu.php'; ?>
<?php
$firstDbName     = 'qatester625';
$firstDbUser     = 'qatester';
$firstDbPassword = 'Billtrust1';
$firstDbHost     = 'qadb.billtrust.local';

$secondDbName     = 'qatester625';
$secondDbUser     = 'root';
$secondDbPassword = 'the-new-password';
$secondDbHost     = 'localhost';



class db
{

    protected $connection;
    public $query;
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
$db11 = new db($firstDbHost, $firstDbUser, $firstDbPassword, $firstDbName);
$db2 = new db($secondDbHost, $secondDbUser, $secondDbPassword, $secondDbName);
$db3 = new db($secondDbHost, $secondDbUser, $secondDbPassword, $secondDbName);
$db4 = new db($secondDbHost, $secondDbUser, $secondDbPassword, $secondDbName);
$db5 = new db($secondDbHost, $secondDbUser, $secondDbPassword, $secondDbName);


$db3->query("show tables");
$tables = $db3->fetchAll();


foreach ($tables as $table2) {
    foreach ($table2 as $table) {
        
        
        $sql0 = "select * from " . $table . " order by 1 desc limit 0,100";
        $query11 = $db11->query($sql0);
        $result11 = $query11->fetchAll();

        if (count($result11) > 0) {
            foreach ($result11 as $row) {
                $columns = implode(", ", array_keys($row));
                $val = array_values($row);
				$whereCondition="";
				foreach($row as $field=>$value){
					if(!empty($whereCondition)){
						$whereCondition.=" and ";
					}
					$whereCondition.=$field."="."'".$value."'";
				}
				$db5->query("select * from " . $table . " where ".$whereCondition);
				$records = $db5->fetchAll();
				
				
                if($val[0]!='0' && empty($records)){
                    $values  = implode("', '", $row);
                    $sql = "INSERT IGNORE  INTO `" . $table . "`($columns) VALUES ('$values')";
					try{
						$db2->query($sql);
					}catch(\Throwable $ex){
						continue;
					}
                    
                }
                
            }
        }
    }
}
