<?php
try{
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
$result3=[];
$result4=[];
if($_POST){
    if($_POST['content1'] && $_POST['content2']){

        $content1Array=eval($_POST['content1']);
        $content2Array=eval($_POST['content2']);

        $result1A=array_diff($content1Array,$content2Array);
        $result2A=array_diff($content2Array,$content1Array);
        $result1=$result1A;
        $result2=$result2A;
        $result3=array_diff(array_flip($content1Array),array_flip($content2Array));
        $result4=array_diff(array_flip($content2Array),array_flip($content1Array));
        $result3=array_flip($result3);
        $result4=array_flip($result4);
    }
}

function getPostValue($key)
{
    if ($_POST) {
        echo $_POST[$key];
    }
}
}catch(Exception $e){
    echo $e->getMessage()."<br/>";
    echo $e->getLine();
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
    <textarea name="result1" rows="20" cols="300"><?php var_export($result1); ?></textarea>
<br/>
<br/>
   B-A:(Second array has extra this but first array does not have this)
        <br/>
    <textarea name="result2" rows="20" cols="300"><?php var_export($result2); ?></textarea>
<br/>

<h3>Keys</h3>
<br/>
   A-B:(First array has extra this but second array does not have this)
        <br/>
    <textarea name="result3" rows="20" cols="300"><?php var_export($result3); ?></textarea>
<br/>
<br/>
   B-A:(Second array has extra this but first array does not have this)
        <br/>
    <textarea name="result4" rows="20" cols="300"><?php var_export($result4); ?></textarea>
<br/>


<input type="submit" value="generate" />
</form>
</body>
</html>


