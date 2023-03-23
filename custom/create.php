<?php
$rawFieldsArray=[];
$outputDesign='';
$result='';
$outputDesign='';
$class='';
$validationRule='';
$postValue='';
$bottom='';
if($_POST)
{
    $rawFields=$_POST['rawFields'];
    $rawFieldsArray=explode(",",$rawFields);
    foreach($rawFieldsArray as $index=> $raw)
    {
        $processedField=str_replace(" ","_",trim($raw));
        $validationRule.="$"."this->form_validation->set_rules('".$processedField."', '".ucfirst(trim($raw))."', 'trim|required');".PHP_EOL;
        $postValue.="$"."data['".$processedField."']="."$"."this->input->post('".$processedField."',true);".PHP_EOL;
    }
    $top="if("."$"."this->form_validation->run() == TRUE){".PHP_EOL;
    $bottom.="if(1){".PHP_EOL;
    $bottom.="$"."this->session->set_flashdata('success_message', 'Successfully done');".PHP_EOL;

    $bottom.="}".PHP_EOL;
    $bottom.="else{".PHP_EOL;
    $bottom.="$"."this->session->set_flashdata('error_message', 'Error occured');".PHP_EOL;

    $bottom.="}".PHP_EOL;
    $bottom.="}".PHP_EOL;
    $result=$validationRule.$top.$postValue.$bottom;
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
<h6>CodeIgniter</h6>
<form method="post">
    Fields::
    <br/>
    <textarea name="rawFields" rows="20" cols="300"><?php getPostValue('rawFields') ?></textarea>
    <br/>
    Output Result::
    <br/>
    <textarea name="result" rows="20" cols="300"> <?php echo htmlentities($result); ?> </textarea>
    <br/>
    <input type="submit" value="generate" />
</form>
</body>
</html>