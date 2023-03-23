<?php
$rawFieldsArray=[];
$outputDesign='';
$result='';
if($_POST)
{
    $dateFormat=$_POST['dateFormat'];
    $dateTimeFormat=$_POST['dateTimeFormat'];
    $rawFields=$_POST['rawFields'];
    $rawFieldsArray=explode(",",$rawFields);
    $fieldName=isset($_POST['fieldName'])?$_POST['fieldName']:[];
    $outputDesign='';
    $designTypes=$_POST['designType'];

    $outputDesign.="$"."errorFlag=false;".PHP_EOL;
    $outputDesign.="$"."errorMessage='';".PHP_EOL;
    $outputDesign.="$"."errorArr=[];".PHP_EOL;

    $function="function validateDate("."$"."date, "."$"."format = 'Y-m-d H:i:s') {"
        ."$"."d = DateTime::createFromFormat("."$"."format, $"."date);
        return "."$"."d && "."$"."d->format("."$"."format) == "."$"."date;
    }";
    $outputDesign.=$function.PHP_EOL;



    foreach($fieldName as $index=> $processedField)
    {
        $types=$designTypes[$index];
        foreach($types as $type)
        {
            if($type=='required')
            {
                $outputDesign.="if(empty("."$"."data['".$processedField."'])){".PHP_EOL;
                $outputDesign.="$"."errorFlag=true;".PHP_EOL;
                $outputDesign.="$"."errorMessage.='Please enter value for ".str_replace("_"," ",$processedField)."';".PHP_EOL;

                $outputDesign.="$"."errorArr['".$processedField."'][]='Please enter value for ".str_replace("_"," ",$processedField)."';".PHP_EOL;
                $outputDesign.="}".PHP_EOL;
            }
            if($type=='number')
            {
                $outputDesign.="if(!is_numeric("."$"."data['".$processedField."'])){".PHP_EOL;
                $outputDesign.="$"."errorFlag=true;".PHP_EOL;
                $outputDesign.="$"."errorMessage.='Please enter numeric value for ".str_replace("_"," ",$processedField)."';".PHP_EOL;

                $outputDesign.="$"."errorArr['".$processedField."'][]='Please enter numeric value for ".str_replace("_"," ",$processedField)."';".PHP_EOL;
                $outputDesign.="}".PHP_EOL;
            }
            if($type=='email')
            {
                $outputDesign.="if(!filter_var("."$"."data['".$processedField."'],FILTER_VALIDATE_EMAIL)){".PHP_EOL;
                $outputDesign.="$"."errorFlag=true;".PHP_EOL;
                $outputDesign.="$"."errorMessage.='Please enter valid email for ".str_replace("_"," ",$processedField)."';".PHP_EOL;

                $outputDesign.="$"."errorArr['".$processedField."'][]='Please enter valid email for ".str_replace("_"," ",$processedField)."';".PHP_EOL;
                $outputDesign.="}".PHP_EOL;
            }
            if($type=='date')
            {

                $outputDesign.="if(!validateDate("."$"."data['".$processedField."'],'".$dateFormat."')){".PHP_EOL;
                $outputDesign.="$"."errorFlag=true;".PHP_EOL;
                $outputDesign.="$"."errorMessage.='Please enter valid date for ".str_replace("_"," ",$processedField)."';".PHP_EOL;

                $outputDesign.="$"."errorArr['".$processedField."'][]='Please enter valid date for  ".str_replace("_"," ",$processedField)."';".PHP_EOL;
                $outputDesign.="}".PHP_EOL;
            }
            if($type=='datetime')
            {

                $outputDesign.="if(!validateDate("."$"."data['".$processedField."'],'".$dateTimeFormat."')){".PHP_EOL;
                $outputDesign.="$"."errorFlag=true;".PHP_EOL;
                $outputDesign.="$"."errorMessage.='Please enter valid datetime for ".str_replace("_"," ",$processedField)."';".PHP_EOL;

                $outputDesign.="$"."errorArr['".$processedField."'][]='Please enter valid datetime for  ".str_replace("_"," ",$processedField)."';".PHP_EOL;
                $outputDesign.="}".PHP_EOL;
            }
            if($type=='maxlength')
            {
                $outputDesign.="if(strlen("."$"."data['".$processedField."'])>10){".PHP_EOL;
                $outputDesign.="$"."errorFlag=true;".PHP_EOL;
                $outputDesign.="$"."errorMessage.='Please enter value max 10 characters for ".str_replace("_"," ",$processedField)."';".PHP_EOL;

                $outputDesign.="$"."errorArr['".$processedField."'][]='Please enter value max 10 characters for ".str_replace("_"," ",$processedField)."';".PHP_EOL;
                $outputDesign.="}".PHP_EOL;
            }
            if($type=='minlength')
            {
                $outputDesign.="if(strlen("."$"."data['".$processedField."'])<2){".PHP_EOL;
                $outputDesign.="$"."errorFlag=true;".PHP_EOL;
                $outputDesign.="$"."errorMessage.='Please enter value min 2 characters for ".str_replace("_"," ",$processedField)."';".PHP_EOL;

                $outputDesign.="$"."errorArr['".$processedField."'][]='Please enter value min 2 characters for ".str_replace("_"," ",$processedField)."';".PHP_EOL;
                $outputDesign.="}".PHP_EOL;
            }
            if($type=='minval')
            {
                $outputDesign.="if(intval("."$"."data['".$processedField."'])<0){".PHP_EOL;
                $outputDesign.="$"."errorFlag=true;".PHP_EOL;
                $outputDesign.="$"."errorMessage.='Please enter value more than 0 for ".str_replace("_"," ",$processedField)."';".PHP_EOL;

                $outputDesign.="$"."errorArr['".$processedField."'][]='Please enter value more than 0 for ".str_replace("_"," ",$processedField)."';".PHP_EOL;
                $outputDesign.="}".PHP_EOL;
            }
            if($type=='maxval')
            {
                $outputDesign.="if(intval("."$"."data['".$processedField."'])>100){".PHP_EOL;
                $outputDesign.="$"."errorFlag=true;".PHP_EOL;
                $outputDesign.="$"."errorMessage.='Please enter value less than equal to 100 for ".str_replace("_"," ",$processedField)."';".PHP_EOL;

                $outputDesign.="$"."errorArr['".$processedField."'][]='Please enter value less than equal to 100 for ".str_replace("_"," ",$processedField)."';".PHP_EOL;
                $outputDesign.="}".PHP_EOL;
            }

        }
    }
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
<h6>S-Validation</h6>
<form method="post">
    Fields::
    <br/>
    <textarea name="rawFields" rows="20" cols="300"><?php getPostValue('rawFields') ?></textarea>
    <br/>
    Date format
    <input type="text" name="dateFormat" value="d-m-Y" />
    <br/>
    DateTime format
    <input type="text" name="dateTimeFormat" value="d-m-Y H:i:s" />
    <br/>
    Types
    <br/>
    <?php
    foreach($rawFieldsArray as $index=>$rawField)
    {
        $rawFieldWithUnderScore=str_replace(" ","_",trim($rawField));
        ?>
        <input type="text" name="fieldName[]" value="<?php echo $rawFieldWithUnderScore; ?>" />
        <select name="designType[<?php echo $index; ?>][]" multiple="">
            <option value="required"> Required </option>
            <option value="number">  Number </option>
            <option value="email"> Email </option>
            <option value="date">Date </option>
            <option value="time">Time </option>
            <option value="datetime">Datetime </option>
            <option value="maxlength">Maxlength </option>
            <option value="minlength">Minlength </option>
            <option value="minval">Minval </option>
            <option value="maxval">Maxval </option>
        </select>
        <br/>
        <?php
    }

    ?>
    <br/>
    Output Design::
    <br/>
    <textarea name="result" rows="20" cols="300"> <?php echo htmlentities($outputDesign); ?> </textarea>
    <br/>
    <input type="submit" value="generate" />
</form>
</body>
</html>