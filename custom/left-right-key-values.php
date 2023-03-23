<?php
function dashesToCamelCase($string, $capitalizeFirstCharacter = false)
{
    $str = str_replace('-', '', ucwords($string, '-'));
    if(!$capitalizeFirstCharacter) {
        $str = lcfirst($str);
    }
    return $str;
}
if($_POST){
    if(!empty($_POST['wrapper'])){
        $wrapper=$_POST['wrapper'];
    }
}

$rawFieldsArray = [];
$outputDesign = '';
$result = '';


$wrapper = str_replace(array("\r\n", "\r", "\n", "\\r", "\\n", "\\r\\n"), "newline", $wrapper);
$wrappers = explode("newline", $wrapper);
$left   = [];
$right  = [];
$left1  = [];
$right1 = [];
$left2  = [];
$right2 = [];


$lines=[];
if ($_POST) {
    $deviderMain    = $_POST['deviderMain'];
    $deviderLeft    = $_POST['deviderLeft'];
    $deviderRight   = $_POST['deviderRight'];
    $erase   = $_POST['erase'];
    $erase=!empty($erase)?explode(",",$erase):[];

    foreach($wrappers as $wrapper){
        $line    = explode($deviderMain,$wrapper);
        $l=isset($line[0])?trim($line[0]):'';
        $r=isset($line[1])?trim($line[1]):'';
        $l=str_replace([';',' '],'',$l);
        $r=str_replace([';',' '],'',$r);

	$l=str_replace($erase,'',$l);
        $r=str_replace($erase,'',$r);


        $left[]  = $l;
        $right[] = $r;
        if(!empty($deviderLeft)){
            if($l){
                $temp=explode($deviderLeft,$l);
                $l1=isset($temp[0])?trim($temp[0]):'';
                $l2=isset($temp[1])?trim($temp[1]):'';

                $l=str_replace([';',' '],'',$l1);
                $l2=str_replace([';',' '],'',$l2);
                
                $l=str_replace($erase,'',$l1);
                $l2=str_replace($erase,'',$l2);

                $left1[]=$l1;
                $left2[]=$l2;    
            }
        }
        if(!empty($deviderRight)){
            if($r){
                $temp=explode($deviderRight,$r);
                $r1=isset($temp[0])?trim($temp[0]):'';
                $r2=isset($temp[1])?trim($temp[1]):'';

                $r1=str_replace([';',' '],'',$r1);
                $r2=str_replace([';',' '],'',$r2);
                
                $r1=str_replace($erase,'',$r1);
                $r2=str_replace($erase,'',$r2);


                $right1[]=$r1;
                $right2[]=$r2;    
            }
        }
    }

}
function getPostValue($key)
{
    if ($_POST) {
        echo $_POST[$key];
    }
}

$left   = array_filter($left);
$right  = array_filter($right);
$left1  = array_filter($left1);
$right1 = array_filter($right1);
$left2  = array_filter($left2);
$right2 = array_filter($right2);

if($_POST){
    if($_POST['isSort']){
        sort($left);
        sort($right);
        sort($left1);
        sort($right1);
        sort($left2);
        sort($right2);
    }
}


if(!empty($left)){
    $left=implode(",",$left);
}else{
    $left='';
}

if(!empty($right)){
    $right=implode(",",$right);
}else{
    $right='';
}


if(!empty($left1)){
    $left1=implode(",",$left1);
}else{
    $left1='';
}

if(!empty($right1)){
    $right1=implode(",",$right1);
}else{
    $right1='';
}

if(!empty($left2)){
    $left2=implode(",",$left2);
}else{
    $left2='';
}

if(!empty($right2)){
    $right2=implode(",",$right2);
}else{
    $right2='';
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
<h6>Left right key values</h6>
    <form method="post">
        Content
        <br/>
        <textarea name="wrapper" rows="20" cols="300"><?php getPostValue('wrapper')?></textarea>
        <br/>
        <br/>
        Devider Main
        <br/>
        <input type="text" name="deviderMain" value="<?php getPostValue('deviderMain')?>"/>
        <br/>
        <br/>
        Devider Left
        <br/>
        <input type="text" name="deviderLeft" value="<?php getPostValue('deviderLeft')?>"/>
        <br/>
        <br/>
        Devider Right
        <br/>
        <input type="text" name="deviderRight" value="<?php getPostValue('deviderRight')?>"/>
        <br/>
         <br/>
        Erase from content (comma separated)
        <br/>
        <input type="text" name="erase" value="<?php getPostValue('erase')?>"/>
        <br/>
        <input type="checkbox" name="isSort" value="1"
        <?php
            if($_POST){
                if($_POST['isSort']==1){
                    echo "checked='checked'";
                }
            }

        ?>

        > <strong>Sort</strong>
 
 
<br/>
   Left:
        <br/>
    <textarea name="result" rows="20" cols="300"> <?php echo htmlentities($left); ?> </textarea>
<br/>
<br/>
   Right:
        <br/>
    <textarea name="result" rows="20" cols="300"> <?php echo htmlentities($right); ?> </textarea>
<br/>

<br/>
   Left1:
        <br/>
    <textarea name="result2" rows="20" cols="300"> <?php echo htmlentities($left1); ?> </textarea>
<br/>

<br/>
   Right1:
        <br/>
    <textarea name="result3" rows="20" cols="300"> <?php echo htmlentities($right1); ?> </textarea>
<br/>

<br/>
   Left2:
        <br/>
    <textarea name="result4" rows="20" cols="300"> <?php echo htmlentities($left2); ?> </textarea>
<br/>

<br/>
   Right2:
        <br/>
    <textarea name="result5" rows="20" cols="300"> <?php echo htmlentities($right2); ?> </textarea>
<br/>

<input type="submit" value="generate" />
</form>
</body>
</html>


