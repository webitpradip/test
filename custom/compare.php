<?php
function dashesToCamelCase($string, $capitalizeFirstCharacter = false)
{
    $str = str_replace('-', '', ucwords($string, '-'));
    if(!$capitalizeFirstCharacter) {
        $str = lcfirst($str);
    }
    return $str;
}
$content1Array=[];
$content2Array=[];
$result1='';
$result2='';
if($_POST){
    
    if($_POST['content1'] && $_POST['content2']){
        $content1=$_POST['content1'];
        $content2=$_POST['content2'];
        $content1Array=explode(",",$content1);
        $content2Array=explode(",",$content2);
        $result1A=array_diff($content1Array,$content2Array);
        $result2A=array_diff($content2Array,$content1Array);
        $result1=implode(",",$result1A);
        $result2=implode(",",$result2A);
        $result1=str_replace(' ', '', $result1);
        $result2=str_replace(' ', '', $result2);
    }
}

function getPostValue($key)
{
    if ($_POST) {
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
<?php require_once 'menu.php' ?>
<h6>Compare</h6>
    <form method="post">
        Content1
        <br/>
        <textarea name="content1" rows="20" cols="300"><?php getPostValue('content1')?></textarea>
        <br/>
        <br/>
        Content2
        <br/>
        <textarea name="content2" rows="20" cols="300"><?php getPostValue('content2')?></textarea>
        <br/>
        <br/>
<br/>
   A-B:(First array has extra this but second array does not have this)
        <br/>
    <textarea name="result" rows="20" cols="300"><?php echo htmlentities($result1); ?> </textarea>
<br/>
<br/>
   B-A:(Second array has extra this but first array does not have this)
        <br/>
    <textarea name="result" rows="20" cols="300"><?php echo htmlentities($result2); ?> </textarea>
<br/>
<input type="submit" value="generate" />
</form>
</body>
</html>


