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
        $wr2=$_POST['wrapper'];
        file_put_contents(realpath("./public/laravel/wrapper.txt"),$wr2);
    }
}

$rawFieldsArray = [];
$outputDesign = '';
$result = '';

$checkbox = file_get_contents(realpath("./public/laravel/checkbox.txt"));
$date = file_get_contents(realpath("./public/laravel/date.txt"));
$datetime = file_get_contents(realpath("./public/laravel/datetime.txt"));
$radio = file_get_contents(realpath("./public/laravel/radio.txt"));
$select = file_get_contents(realpath("./public/laravel/select.txt"));
$text = file_get_contents(realpath("./public/laravel/text.txt"));
$textarea = file_get_contents(realpath("./public/laravel/textarea.txt"));
$label = file_get_contents(realpath("./public/laravel/label.txt"));
$file = file_get_contents(realpath("./public/laravel/file.txt"));
$wrapper = file_get_contents(realpath("./public/laravel/wrapper.txt"));
$wrapper = str_replace(array("\r\n", "\r", "\n", "\\r", "\\n", "\\r\\n"), "newline", $wrapper);
$wrappers = explode("newline", $wrapper);

if ($_POST) {
    $rawFields = $_POST['rawFields'];
    $rawFieldsArray = explode(",", $rawFields);
    $fieldName = isset($_POST['fieldName']) ? $_POST['fieldName'] : [];
    $outputDesign = '';
    $class = '';
    $type = isset($_POST['designType']) ? $_POST['designType'] : [];
    $labelClass = $_POST['labelClass'];
    $inputClass = $_POST['inputClass'];
    $createController='';




    foreach ($fieldName as $index => $processedField) {
        $labelText = ucwords(str_replace("_", " ", $processedField));
        $type = $_POST['designType'];
        $inputType = $type[$index];
        $class = $_POST['inputClass'] . " " . $inputType;
        if ($inputType == 'text') {
            $outputDesign = $text;
        } else if ($inputType == 'date') {
            $outputDesign = $date;
        } else if ($inputType == 'datetime') {
            $outputDesign = $datetime;
        } else if ($inputType == 'textarea') {
            $outputDesign = $textarea;
        } else if ($inputType == 'radio') {
            $outputDesign = $radio;
        } else if ($inputType == 'checkbox') {
            $outputDesign = $checkbox;
        } else if ($inputType == 'select') {
            $outputDesign = $select;
        } else if ($inputType == 'file') {
            $outputDesign = $file;
        }

        foreach ($wrappers as $wr) {

            if (strpos($wr, '<label')) {
                $label2 = str_replace('name1', $processedField, $label);
                $label2 = str_replace('id1', $processedField.'_label', $label2);
                $label2 = str_replace('value1', $labelText, $label2);
                $label2 = str_replace('class1', $labelClass, $label2);
                $wr = $label2;
            }
            if (strpos($wr, '<input')) {
                $inpt = str_replace('name1', $processedField, $outputDesign);
                $inpt = str_replace('id1', $processedField, $inpt);
if ($inputType == 'select'){
	                $inpt = str_replace('value1', '!empty($'.dashesToCamelCase($processedField).')?$'.dashesToCamelCase($processedField).':[]', $inpt);

}else{
	                $inpt = str_replace('value1', '!empty($'.dashesToCamelCase($processedField).')?$'.dashesToCamelCase($processedField).':null', $inpt);

}
                $inpt = str_replace('class1', $inputClass, $inpt);
                $wr = $inpt;
            }
            if(strpos($wr,'errorMessage')){
                $wr = str_replace('errorMessage', 'errorMessage_'.$processedField, $wr).PHP_EOL.
                "{!!$"."errors->first('".$processedField."')!!}";
            }
            $result .= $wr.PHP_EOL;
        }
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
<h6>Laravel Design From Fields</h6>
    <form method="post">
        Fields::
        <br/>
        <textarea name="rawFields" rows="20" cols="300"><?php getPostValue('rawFields')?></textarea>
        <br/>
    <br/>

<?php ob_start(); ?>   
    	<div class="row">
<div class="col-sm-6">
<label > </label>
</div>
<div class="col-sm-6">
<input >
</div>
</div>
<?php $c11=ob_get_clean(); ?>
 <?php echo htmlspecialchars($c11); ?>
        Wrapper::
        <br/>
        <textarea name="wrapper" rows="20" cols="300"><?php getPostValue('wrapper')?></textarea>
        <br/>
        <br/>
        Input Class
        <br/>
    <input type="text" name="inputClass" value="<?php getPostValue('inputClass')?>"/>
        <br/>
        Label Class
        <br/>
        <input type="text" name="labelClass" value="<?php getPostValue('labelClass')?>" />
    <br/>
        Types
        <br/>
        <?php
foreach ($rawFieldsArray as $index => $rawField) {

    $rawFieldWithUnderScore = str_replace(" ", "_", trim($rawField));
    ?>
            <input type="text" name="fieldName[]" value="<?php echo $rawFieldWithUnderScore; ?>" />
            <select name="designType[]">
                <option <?php if (isset($type[$index]) && $type[$index] == "text") {echo "selected='selected'";}?> value="text">Text</option>
                <option <?php if (isset($type[$index]) && $type[$index] == "textarea") {echo "selected='selected'";}?> value="textarea">TextArea</option>
                <option <?php if (isset($type[$index]) && $type[$index] == "select") {echo "selected='selected'";}?> value="select">Select</option>
                <option <?php if (isset($type[$index]) && $type[$index] == "radio") {echo "selected='selected'";}?> value="radio">Radio</option>
                <option <?php if (isset($type[$index]) && $type[$index] == "checkbox") {echo "selected='selected'";}?> value="checkbox">Checkbox</option>
                <option <?php if (isset($type[$index]) && $type[$index] == "file") {echo "selected='selected'";}?> value="file">File</option>
            </select>
            <br/>
            <?php
}

?>
<br/>
   Output Design::
        <br/>
    <textarea name="result" rows="20" cols="300"> <?php echo htmlentities($result); ?> </textarea>
<br/>
<input type="submit" value="generate" />
</form>
</body>
</html>


