<?php
$rawFieldsArray=[];
$inputArray=[];
$outputDesign='';
$result='';
$allArr=[];
$resArr=[];
$filRes=[];
if($_POST)
{
    $designs =   $_POST['rawFields'];
    $designsArr = explode(PHP_EOL,$designs);
    foreach($designsArr as $designLine){
        if((strpos($designLine, 'input') !== false) || (strpos($designLine, 'select') !== false)  ){
            $inputArray[]=$designLine.PHP_EOL;
        }
    }
    foreach($inputArray as $lines){
        $allArr[]=explode(" ",$lines);
    }

   foreach($allArr as $arr){
        foreach($arr as $ar){
            if(strpos($ar, 'name') !== false){
                $resArr[] = $ar;
            }
        }
   }
   foreach($resArr as $item){
       $tmpArr=explode("=",$item);
       $a = isset($tmpArr[1])?str_replace(['>','<','"','\''],'',$tmpArr[1]):'';
       $filRes[]=$a;
   }
    $filRes = array_unique(array_filter($filRes));
    $outputDesign.=implode(",",$filRes);

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
<h6>Get field from design</h6>
<form method="post">
    Design::
    <br/>
    <textarea name="rawFields" rows="20" cols="300"><?php getPostValue('rawFields') ?></textarea>
    <br/>
    Fields::
    <br/>
    <textarea name="result" rows="20" cols="300"> <?php echo htmlentities($outputDesign); ?> </textarea>
    <br/>
    <input type="submit" value="generate" />
</form>
</body>
</html>