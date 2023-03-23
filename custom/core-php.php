<?php
$rawFieldsArray=[];
$outputDesign='';
$result='';
$result2='';
$result3='';
$outputDesign='';
$class='';
$validationRule='';
$postValue='';
$bottom='';
if($_POST)
{
    $result.="if($"."_POST){".PHP_EOL;
    $result2.="insert into abc(";
    $result3.="update table abc set ";
    $rawFields=$_POST['rawFields'];
    $rawFieldsArray=explode(",",$rawFields);
    $columns='';
    $data='';
    $update='';
    foreach($rawFieldsArray as $index=> $raw)
    {
        $processedField=str_replace(" ","_",trim($raw));
        $result.="$"."data['".$processedField."'"."]="."clearc($"."_POST['".$processedField."']);".PHP_EOL;
        if(!empty($columns))
        {
            $columns.=",";
        }
        $columns.=$processedField;
        if(!empty($data))
        {
            $data.=",";
        }
        $data.="\"'\"."."$"."data['".$processedField."']".".\"'\"";
        if(!empty($update))
        {
            $update.=",";
        }
        $update.=$processedField."="."\"'\"."."$"."data['".$processedField."']".".\"'\"";
    }
    $result.="}";
    $result2.=$columns.") values(".$data.");";
    $result3.=$update." where 1=1";
}
function getPostValue($key)
{
    if($_POST)
    {
        echo $_POST[$key];
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
<h6>Core php</h6>
    <form method="post">
        Fields::
        <br/>
        <textarea name="rawFields" rows="20" cols="300"><?php getPostValue('rawFields') ?></textarea>
        <br/>
        Output Result1::
        <br/>
    <textarea name="result1" rows="20" cols="300"> <?php echo htmlentities($result); ?> </textarea>
    Output Result2::
        <br/>
    <textarea name="result2" rows="20" cols="300"> <?php echo htmlentities($result2); ?> </textarea>
    Output Result3::
        <br/>
    <textarea name="result3" rows="20" cols="300"> <?php echo htmlentities($result3); ?> </textarea>
<br/>
<input type="submit" value="generate" />
</form>
</body>
</html>