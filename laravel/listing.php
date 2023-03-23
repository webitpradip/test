<?php
    function dashesToCamelCase($string, $capitalizeFirstCharacter = false,$delimeter="-")
    {
        $str = str_replace('-', '', ucwords($string, $delimeter));
        if (!$capitalizeFirstCharacter){
            $str = lcfirst($str);
        }
        return $str;
    }
    $rawFieldsArray = [];
    $outputDesign = '';
    $result = '';
    $eol=PHP_EOL;
    if($_POST) {
        $rawFields = $_POST['rawFields'];
        $rawFieldsArray = explode(",", $rawFields);
        $fieldName = isset($_POST['fieldName']) ? $_POST['fieldName'] : [];
        $outputDesign = '';
        $class = '';
        $type           = isset($_POST['designType']) ? $_POST['designType'] : [];
        $modelName      = $_POST['modelName'];
        $smallModelName = strtolower($modelName);
        $searchString   = '';

        $relations='';
        $counter=0;
        foreach ($rawFieldsArray as $index => $rawField) {

            $rawFieldWithUnderScore = str_replace(" ", "_", trim($rawField));
            $fldArr = explode("_",$rawFieldWithUnderScore);
            if(in_array('id',$fldArr)){
                $string=implode('_',array_slice($fldArr,0,-1));
                if(empty($relations)){
                    $relations.='with("'.str_replace("_","",dashesToCamelCase($string,false,'_')).'")';
                }else{
                    $relations.='->with("'.str_replace("_","",dashesToCamelCase($string,false,'_')).'")';
                }

            }else{
                ++$counter;
                if($counter==1){
                    $searchString.='$q->where("'.$rawFieldWithUnderScore.'","like","%".$data["attributes"]["searchKeyword"]."%");'.$eol;
                }else{
                    $searchString.='$q->orWhere("'.$rawFieldWithUnderScore.'","like","%".$data["attributes"]["searchKeyword"]."%");'.$eol;
                }

            }
        }

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
        }

        $output = 'public function index(Request $request)'.$eol;
        $output.= '{'.$eol;
        $output .= '$'.$modelName.' ="";'.$eol;
        $output.= '$orderByField = "id";'.$eol;
        $output.= '$orderByDir = "asc";'.$eol;
        $output.= '$rel = false;'.$eol;
        $output.= '$sch = false;'.$eol;
        $output.= '$noOfPages = config("settings.noOfPagesForPaginate");'.$eol;
        $output.= 'if (isset($request->orderByField) && !empty($request->orderByField)) {'.$eol;
        $output.= '$orderByField = $request->orderByField;'.$eol;
        $output.= '}'.$eol;
        $output.= 'if (isset($request->orderByDir) && !empty($request->orderByDir)) {'.$eol;
        $output.= '$orderByDir = $request->orderByDir;'.$eol;
        $output.= '}'.$eol;
        $output.= '$attributes = ['.$eol;
        $output.= '"orderByField"	=> $orderByField,'.$eol;
        $output.= '"orderByDir" 	=> $orderByDir,'.$eol;
        $output.= '];'.$eol;
        $output.= '$data["attributes"]     = $attributes;'.$eol;
        $output.= '$data["attributes"]["searchKeyword"] = $request->input("searchKeyword");'.$eol;
        if(!empty($relations)) {
            $output.= '$rel = true;'.$eol;
            $output .= '$'.$modelName.' = \\App\\Models\\'.$modelName.'::'.$relations.';'.$eol;
        }
        if(!empty($relations)) {
            $output .= 'if(!empty($data["attributes"]["searchKeyword"])){'.$eol;
            $output .= '$'.$modelName.'->where(function($q) use($data){'.$eol;
            $output .= $searchString.$eol;
            $output .= '});'.$eol;
            $output .= '}'.$eol;
        }else{
            $output .= 'if(!empty($data["attributes"]["searchKeyword"])){'.$eol;
            $output.= '$sch = false;'.$eol;
            $output .= '$'.$modelName.' = \\App\\Models\\'.$modelName.'::'.'where(function($q) use($data){'.$eol;
            $output .= $searchString.$eol;
            $output .= '});'.$eol;
            $output .= '}'.$eol;
        }


        $output.= 'if($rel || $sch){'.$eol;
        $output.= '$'.$modelName.'=$'.$modelName.'->orderBy($orderByField,$orderByDir)->paginate($noOfPages);'.$eol;
        $output.='}else{'.$eol;
        $output.='$'.$modelName.' = \\App\\Models\\'.$modelName.'::'.'orderBy($orderByField,$orderByDir)->paginate($noOfPages);'.$eol;
        $output.='}'.$eol;


        $output.= '$data["'.$modelName.'s"] ='.'$'.$modelName.';'.$eol;
        $output.= 'return view("'.strtolower($modelName).'.list", $data);'.$eol;
        $output.= '}'.$eol;
        $result=$output;

        $design='{!! Form::open(array(\'route\' => \''.$smallModelName.'s.index\',\'method\' => \'get\',\'name\'=>\'frmSearch2\',\'id\'=>\'frmSearch2\')) !!}';
        $design.= '<div class="box box-default" style="height: 58px;">'.$eol;
        $design.= '<div class="box-body">'.$eol;
        $design.= '<div class="row">'.$eol;
        $design.= '<div class="col-md-4">'.$eol;
        $design.= '<div class="form-group">'.$eol;
        $design.= '<input type="text" class="form-control"  name="searchKeyword" id="searchKeyword" placeholder="Search" minlength="1" maxlength="100" value="{{$attributes[\'searchKeyword\']}}">'.$eol;
        $design.= '</div>'.$eol;
        $design.= '</div>'.$eol;
        $design.= '<div class="col-md-4">'.$eol;
        $design.= '<div class="form-group">'.$eol;
        $design.= '<button type="submit" class="btn btn-default butten_style red_button">Submit</button>'.$eol;
        $design.= '<button type="button" class="btn btn-default butten_style red_button" id="btnReset"><img src="{{asset(\Config::get(\'constants.resetIcon\'))}}" title="Reset"></button>'.$eol;
        $design.= '</div>'.$eol;
        $design.= '</div>'.$eol;
        $design.= '</div>'.$eol;
        $design.= '</div>'.$eol;
        $design.= '</div>'.$eol;
        $design.= '{!! Form::close() !!}'.$eol;
        $design.= '<div class="box box-default">'.$eol;
        $design.= '<!-- /.box-header -->';
        $design.= '<div class="box-body">'.$eol;
        $design.= '<div class="flash-message validation-error" id="message" style="display: none;">'.$eol;
        $design.= '<p class="alert alert-danger"></p>'.$eol;
        $design.= '</div>'.$eol;
        $design.= '';
        $design.= '<div class="table-responsive no-padding">'.$eol;
        $design.= '<form id="frmSearch">'.$eol;
        $design.= '<input type="hidden" name="orderByField" id="orderByField" value="{{$attributes[\'orderByField\']}}">'.$eol;
        $design.= '<input type="hidden" name="orderByDir" id="orderByDir" value="{{$attributes[\'orderByDir\']}}">'.$eol;
        $design.= '<table class="table table-bordered table-hover">'.$eol;
        $design.= '<tr>'.$eol;

        foreach ($rawFieldsArray as $index => $rawField) {
            $rawFieldWithUnderScore = str_replace(" ", "_", trim($rawField));
            $design .= '<th>'.ucfirst(str_replace("_"," ",$rawField)).$eol;
            $design .= '<a href="javascript:;" class="pull-right" onClick="sortColumn(\''.$rawFieldWithUnderScore.'\',\'{{$attributes[\'orderByDir\']}}\');" >'.$eol;
            $design .= '@if($' . 'attributes[\'orderByField\'] == \''.$rawFieldWithUnderScore.'\')'.$eol;
            $design .= '<i class="fa fa-sort-{{$attributes[\'orderByDir\']}}" aria-hidden="true"></i>'.$eol;
            $design .= '@else'.$eol;
            $design .= '<i class="fa fa-sort sort-disable" aria-hidden="true"></i>'.$eol;
            $design .= '@endif'.$eol;
            $design .= '</a>'.$eol;
            $design .= '';
            $design .= '</th>'.$eol;
        }
        $design.= '<th>Action</th>'.$eol;
        $design.= '</tr>'.$eol;

        $design .= '@if(!empty($' . $modelName . 's))'.$eol;
        $design .= '@foreach($' . $modelName . 's as $' . $modelName . ')'.$eol;
        $design .= '<tr>'.$eol;
        foreach ($rawFieldsArray as $index => $rawField) {
            $rawFieldWithUnderScore = str_replace(" ", "_", trim($rawField));
            $design .= '<td>{{$'.$modelName.'->'.$rawFieldWithUnderScore.'}}</td>'.$eol;
        }

        $design .= '<td align="center">'.$eol;
        $design .= '<a href="{{route(\''.$smallModelName.'s.edit\',[\'id\'=>encryptC($'.$modelName.'->id)])}}"> <i class="fa fa-pencil-square-o" aria-hidden="true"></i> </a> &nbsp;&nbsp;&nbsp;'.$eol;
        $design .= '<a href="{{route(\''.$smallModelName.'s.index\')}}"> <i class="fa fa-tasks" aria-hidden="true"></i>  </a>'.$eol;
        $design .= '</td>'.$eol;
        $design .= '</tr>'.$eol;
        $design .= '@endforeach'.$eol;
        $design .= '@else'.$eol;
        $design .= '<tr><td colspan="5">No record found!!</td></tr>'.$eol;
        $design .= '@endif'.$eol;


        $design.= '</table>'.$eol;
        $design.= '</form>'.$eol;
        $design.= '</div>'.$eol;
        $design.= '<div class="pagination_area">'.$eol;
        $design.= '<div class="row">'.$eol;
        $design.= '@if(!empty($users))'.$eol;
        $design.= '<div class="col-sm-5 paging_info">'.$eol;
        $design.= 'Showing {{$'.$modelName.'s->firstItem()}} to {{$'.$modelName.'s->lastItem()}} of {{$'.$modelName.'s->total()}}  entries'.$eol;
        $design.= '</div>'.$eol;
        $design.= '<div class="col-sm-7 paging_numbers">'.$eol;
        $design.= '{{ $'.$modelName.'s->appends($_GET)->links() }}'.$eol;
        $design.= '</div>'.$eol;
        $design.= '@endif'.$eol;
        $design.= '</div>'.$eol;
        $design.= '</div>'.$eol;
        $design.= '';
        $design.= '</div>'.$eol;
        $design.= '<!-- /.box-body -->';
        $design.= '</div>'.$eol;

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
<h6>Laravel Listing</h6>
<form method="post">
    Fields::
    <br/>
    <textarea name="rawFields" rows="20" cols="300"><?php getPostValue('rawFields')?></textarea>
    <br/>
    <br/>
    <br/>
    Input Class
    <br/>
    <input type="text" name="inputClass" value="<?php getPostValue('inputClass')?>"/>
    <br/>
    Main Model Name
    <br/>
    <input type="text" name="modelName" value="<?php getPostValue('modelName')?>" />
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
    Output Code::
    <br/>
    <textarea name="result" rows="20" cols="300"> <?php echo htmlentities($result); ?> </textarea>
    <br/>
    Models::
    <br/>
    <textarea name="design" rows="20" cols="300"><?php echo htmlentities($design);  ?></textarea>
    <br/>
    <input type="submit" value="generate" />
</form>
</body>
</html>






