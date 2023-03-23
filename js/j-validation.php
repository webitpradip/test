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
    
    $outputDesign.="$"."(document).ready(function () {

    "."$"."('#myform').validate(";

    $outputDesign2.=$outputDesign;
    
    
    $riles=[];
    
    $conditions=[];
    $messages=[];
    foreach($fieldName as $index=> $processedField)
    {
       $types=$designTypes[$index];
       foreach($types as $type)
       {
           if($type=='required')
           {
               $conditions[$processedField]['required']=true;
               $messages[$processedField]['required']="<?"."php echo trans('validation.required_".$processedField."') ?".">";
           }
           if($type=='number')
           {
               $conditions[$processedField]['integer']=true;
               $messages[$processedField]['integer']="<?"."php echo trans('validation.integer_".$processedField."') ?".">";

               
           }
          if($type=='email')
           {
               $conditions[$processedField]['email']=true;
               $messages[$processedField]['email']="<?"."php echo trans('validation.email_".$processedField."') ?".">";

           }
           if($type=='date')
           {
               
               $conditions[$processedField]['date']=true;
               $messages[$processedField]['date']="<?"."php echo trans('validation.date_".$processedField."') ?".">";
           }
           if($type=='datetime')
           {
                $conditions[$processedField]['date']=true;
               $messages[$processedField]['date']="<?"."php echo trans('validation.date_".$processedField."') ?".">";
               
           }
           if($type=='maxlength')
           {
                $conditions[$processedField]['maxlength']=10;
               $messages[$processedField]['maxlength']="<?"."php echo trans('validation.maxlength_".$processedField."') ?".">";
           }
           if($type=='minlength')
           {
                $conditions[$processedField]['minlength']=2;
               $messages[$processedField]['minlength']="<?"."php echo trans('validation.minlength_".$processedField."') ?".">";
                             
           }
           if($type=='minval')
           {
                $conditions[$processedField]['min']=0;
               $messages[$processedField]['min']="<?"."php echo trans('validation.min_".$processedField."') ?".">";
               
           }
           if($type=='maxval')
           {
                $conditions[$processedField]['max']=100;
               $messages[$processedField]['max']="<?"."php echo trans('validation.max_".$processedField."') ?".">";
               
               
           }
           
       }
    }
    $rules['rules']=$conditions;

    $outputDesign.=json_encode($rules)."); })";
    $rules['messages']=$messages;
    $outputDesign2.=json_encode($rules)."); })";
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
<h1>J-Validation</h1>
<?php 
echo htmlentities('<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>');
echo htmlentities('<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>');
echo htmlentities('<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>');
?>
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
        $counter55=-1;
        foreach($rawFieldsArray as $index=>$rawField)
        {
            ++$counter55;
            $rawFieldWithUnderScore=str_replace(" ","_",trim($rawField));
            ?>
            <input type="text" name="fieldName[]" value="<?php echo $rawFieldWithUnderScore; ?>" />
            <select name="designType[<?php echo $index; ?>][]" multiple="">
                <option value="required" <?php if(in_array('required',$designTypes[$counter55])) { echo "selected='selected'" ;} ?> > Required </option>
                <option value="number"  <?php if(in_array('number',$designTypes[$counter55])) { echo "selected='selected'" ;} ?>  >  Number </option>
                <option value="email"  <?php if(in_array('email',$designTypes[$counter55])) { echo "selected='selected'" ;} ?>   > Email </option>
                <option value="date"  <?php if(in_array('date',$designTypes[$counter55])) { echo "selected='selected'" ;} ?>   >Date </option>
                <option value="time"  <?php if(in_array('time',$designTypes[$counter55])) { echo "selected='selected'" ;} ?>   >Time </option>
                <option value="datetime"  <?php if(in_array('datetime',$designTypes[$counter55])) { echo "selected='selected'" ;} ?>   >Datetime </option>
                 <option value="maxlength"  <?php if(in_array('maxlength',$designTypes[$counter55])) { echo "selected='selected'" ;} ?>   >Maxlength </option>
                 <option value="minlength"  <?php if(in_array('minlength',$designTypes[$counter55])) { echo "selected='selected'" ;} ?>   >Minlength </option>
                  <option value="minval"  <?php if(in_array('minval',$designTypes[$counter55])) { echo "selected='selected'" ;} ?>   >Minval </option>
                <option value="maxval"  <?php if(in_array('maxval',$designTypes[$counter55])) { echo "selected='selected'" ;} ?>    >Maxval </option>
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

<br/>
    Output Design with message::
<br/>
    <textarea name="result2" rows="20" cols="300"> <?php echo htmlentities($outputDesign2); ?> </textarea>
<br/>
    <input type="submit" value="generate" />
</form>
</body>
</html>

