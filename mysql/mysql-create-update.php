<?php
$rawFieldsArray=[];
$mysqlTableCreateStr ='';
$mysqlTableUpdateStr ='';
if($_POST)
{
    $rawFields=$_POST['rawFields'];
    $rawFieldsArray=explode(",",$rawFields);
    $mysqlTableCreateStr='create table abc(';
    $mysqlTmp='';

    $mysqlTableUpdateStr='alter table abc ';
    $mysqlTmpUpdt='';
    if(is_array($rawFieldsArray)) {
        $field_types='';
        $input_types='';
        $serials='';
        foreach ($rawFieldsArray as $field) {
            $field=trim($field);
            $field=str_replace(" ","_",$field);
            $type = $_POST['database_type_' . $field];
            $type2 ='';// $_POST['input_type_' . $field];


            $tmp = $field;
            $field_types.=!empty($field_types)?",":"";
            $field_types.=$type;

            $input_types.=!empty($input_types)?",":"";
            $input_types.=$type2;

//            $serials.=!empty($serials)?",":"";
//            $serials.=$serial;

            $typeTmp = '';
            if ($type == 'varchar') {
                $typeTmp = ' varchar(255) null';
            } else if ($type == 'date') {
                $typeTmp = 'date null';
            }
            else if ($type == 'integer') {
                $typeTmp = 'integer(11) null';
            } else if ($type == 'datetime') {
                $typeTmp = 'datetime null';
            } else if ($type == 'text') {
                $typeTmp = 'text null';
            }
            $mysqlTmp .= !empty($mysqlTmp) ? "," : "";
            $mysqlTmp .= $tmp ." ". $typeTmp;

            $mysqlTmpUpdt .= !empty($mysqlTmpUpdt) ? "," : "";
            $mysqlTmpUpdt .= " ADD COLUMN " . $tmp . " " . $typeTmp;
        }
    }
    $mysqlTableCreateStr.=$mysqlTmp.")";
    $mysqlTableUpdateStr.=$mysqlTmpUpdt.";";
}
function getPostValue($key)
{
    if($_POST)
    {
        if(isset($_POST[$key]))
        {
            echo $_POST[$key];
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<?php require_once 'menu.php'?>
<h6>MySql</h6>
    <form method="post">
        Fields::
        <br/>
        <textarea name="rawFields" rows="20" cols="300"><?php getPostValue('rawFields') ?></textarea>
        <br/>
<?php
        foreach($rawFieldsArray as $index=> $raw) {
            $processedField = str_replace(" ", "_", trim($raw));
            ?>
            <div class="col-md-3">
                <input type="text" name="fields[]" value="<?php echo $processedField; ?>">
            </div>
            <div class="col-md-3">
                Database Type::
                <select name="<?php echo 'database_type_'.$processedField; ?>" >
                    <option
                            value="varchar">Varchar
                    </option>
                    <option
                            value="integer">Integer
                    </option>
                    <option
                            value="date">Date
                    </option>
                    <option
                            value="datetime">DateTime
                    </option>
                    <option
                            value="text">Text
                    </option>
                </select>
            </div>
            <?php
        }
        ?>
        <p>Mysql Create:</p>
        <br/>
    <textarea name="result1" rows="20" cols="300"> <?php echo $mysqlTableCreateStr; ?> </textarea>
        <p>Mysql Update:</p>
        <br/>
        <textarea name="result2" rows="20" cols="300"> <?php echo $mysqlTableUpdateStr; ?> </textarea>
<br/>
<input type="submit" value="generate" />
</form>
</body>
</html>
