<?php
function dashesToCamelCase($string, $capitalizeFirstCharacter = false)
{
    $str = str_replace('-', '', ucwords($string, '-'));
    if (!$capitalizeFirstCharacter) {
        $str = lcfirst($str);
    }
    return $str;
}
function underToCamel($string, $capitalizeFirstCharacter = false)
{
    $str = str_replace('_', '', ucwords($string, '_'));
    if (!$capitalizeFirstCharacter) {
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
$eol=PHP_EOL;
$content1='';
$store_code='';
$create_code='';
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
$createController='';
$validationRule='';
$postValue='';

if ($_POST) {
    $rawFields          = $_POST['rawFields'];
    $rawFieldsArray     = explode(",", $rawFields);
    $fieldName          = isset($_POST['fieldName']) ? $_POST['fieldName'] : [];
    $outputDesign       = '';
    $class              = '';
    $modelName          = $_POST['modelName'];
    $smallModelName     = strtolower($modelName);
    $type               = isset($_POST['designType']) ? $_POST['designType'] : [];
    $labelClass         = $_POST['labelClass'];
    $create_code           = '';
    foreach ($rawFieldsArray as $index => $rawField) {
        $rawFieldWithUnderScore = str_replace(" ", "_", trim($rawField));
        $fldArr = explode("_", $rawFieldWithUnderScore);

        if (in_array('id', $fldArr)) {
            $string     =   implode('_', array_slice($fldArr, 0, -1));
            $camelModelFromField =   underToCamel($string, false);
            $capModelFromField   =   underToCamel($string, true);
            ob_start();
            ?>
            $data['<?php echo $camelModelFromField; ?>'] = \App\Models\<?php echo $camelModelFromField; ?>::pluck('<?php echo $string."_name" ?>', 'id')->toArray();
            <?php
            $create_code.=ob_get_clean();
        }
        $processedField=str_replace(" ","_",trim($rawField));
        if(count($rawFieldsArray)-1==$index)
        {
            $validationRule.="'".$processedField."' => 'required'".PHP_EOL;
        }
        else
        {
            $validationRule.="'".$processedField."' => 'required',".PHP_EOL;
        }


        $postValue.="$"."data['".$processedField."']="."$"."request->input('".$processedField."');".PHP_EOL;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////
ob_start();
    // all data from requests are stored in $data array
    echo $postValue;
    ?>
    // validator checking
    $validator = Validator::make($data, [
        <?php echo $validationRule; ?>
    ]);
    // if any validation does not mathched redirect back to form page
    if ($validator->fails()) {

    return redirect(route('user.edit', ['id' => $id]))
    ->withErrors($validator)
    ->withInput();
    }

    try{
        $user = \App\Models\<?php echo $modelName; ?>::where('id', decryptC($id))->update($data);
        $request->session()->flash('alert-success', '<?php echo $modelName ?> successfully updated');
    }catch(Exception $e){
        $request->session()->flash('alert-success', 'Something went wrong!');
    }
    return redirect(route('<?php echo $modelName ?>s.index'));
<?php
    $store_code=ob_get_clean();

    ////////////////////////////////////////////////////////////////////////////////////////////////////////
    ob_start();
    ?>
    $user_list = route('<?php echo $smallModelName; ?>s.index');
    <?php echo $create_code; ?>
    $data['title'] = 'Edit <?php echo $modelName; ?>';
    $data['id'] = $id;
    $<?php echo $smallModelName; ?> = <?php echo $modelName; ?>::find(base64_deocde($id));
    $data['model'] = $<?php echo $smallModelName; ?>;
    return view('securePanel.<?php echo $smallModelName;  ?>.form', $data);
    <?php
    $create_code.=ob_get_clean();

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
                $label = str_replace('name1', $processedField, $label);
                $label = str_replace('id1', $processedField, $label);
                $label = str_replace('value1', $labelText, $label);
                $label = str_replace('class1', $labelClass, $label);
                $wr = $label;
            }
            if (strpos($wr, '<input')) {
                $inpt = str_replace('name1', $processedField, $outputDesign);
                $inpt = str_replace('id1', $processedField, $inpt);
                $inpt = str_replace('value1', '$'.dashesToCamelCase($processedField), $inpt);
                $inpt = str_replace('class1', $labelClass, $inpt);
                $wr = $inpt;
            }
            if(strpos($wr,'errorMessage')){
                $wr = str_replace('errorMessage', 'errorMessage_'.$processedField, $wr);
            }
            $result .= $wr;
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
<?php require_once 'menu.php'?>
<h6>Laravel Create</h6>
<form method="post">
    Fields::
    <br/>
    <textarea name="rawFields" rows="20" cols="300"><?php getPostValue('rawFields')?></textarea>
    <br/>
    <br/>
    <br/>
    Main Model Name
    <br/>
    <input type="text" name="modelName" value="<?php getPostValue('modelName')?>" />
    <br/>
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
    <br/>
    Create code::
    <br/>
    <textarea name="create_code" rows="20" cols="300"> <?php echo htmlentities($create_code); ?> </textarea>
    <br/>
    <br/>
    Store code::
    <br/>
    <textarea name="store_code" rows="20" cols="300"> <?php echo htmlentities($store_code); ?> </textarea>
    <br/>
    <input type="submit" value="generate" />
</form>
</body>
</html>


