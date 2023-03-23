<?php
$fields = '';
$rules = '';
$filters = '';
$table = '';
$tableBody = '';
$dataTable = '';
$result = '';
$labelClass = '';
$inputClass = '';
$inputDesign = '';
$outputClass = '';
$outputDesign = '';
$store2 = '';
$viewWrapper = '';
$view = '';

$checkbox           = file_get_contents(realpath("./public/laravel/checkbox.txt"));
$date               = file_get_contents(realpath("./public/laravel/date.txt"));
$datetime           = file_get_contents(realpath("./public/laravel/datetime.txt"));
$radio              = file_get_contents(realpath("./public/laravel/radio.txt"));
$select             = file_get_contents(realpath("./public/laravel/select.txt"));
$text               = file_get_contents(realpath("./public/laravel/text.txt"));
$textarea           = file_get_contents(realpath("./public/laravel/textarea.txt"));
$label              = file_get_contents(realpath("./public/laravel/label.txt"));
$file               = file_get_contents(realpath("./public/laravel/file.txt"));
$dtController       = file_get_contents(realpath("./public/laravel/datatable-controller.txt"));
$store       = file_get_contents(realpath("./public/laravel/store.txt"));
$update =  file_get_contents(realpath("./public/laravel/update.txt"));
$dataTableText = file_get_contents(realpath("./public/laravel/datatable.txt"));
$controller = '';

$action  = "<a href=\"route('users.edit',[$" . "record->id])\"" . "><i fa fa-edit></i></a>" . PHP_EOL;
$action .= "<a data-id=" . "$" . "record->id" . " class='delete'  href=\"javascript:void(0);\"" . "><i fa fa-trash-alt></i></a>" . PHP_EOL;
$action .= "if(" . "$" . "record->is_active=='1'){" . PHP_EOL;
$action .= "<a data-id=" . "$" . "record->id" . " class='inactivate' href=\"javascript:void(0);\"" . "><i fa fa-times-circle></i></a>" . PHP_EOL;
$action .= "}else{" . PHP_EOL;
$action .= "<a data-id=" . "$" . "record->id" . " class='activate' href=\"javascript:void(0);\"" . "><i fa fa-check></i></a>" . PHP_EOL;
$action .= "}" . PHP_EOL;

if ($_POST) {

    $viewWrapper        = $_POST['viewWrapper'];
    $wrapper            = $_POST['wrapper'];
    $wrapper2           = str_replace(array("\r\n", "\r", "\n", "\\r", "\\n", "\\r\\n", PHP_EOL), "newline", $wrapper);
    $wrappers           = explode("newline", $wrapper2);
    $type = isset($_POST['designType']) ? $_POST['designType'] : [];


    $fields = $_POST['fields'];
    $fieldsArr = explode(",", $fields);
    foreach ($fieldsArr as $index => $field) {
        $labelText = ucwords(str_replace("_", " ", $field));

        $tmp1 = str_replace('labelC', $labelText, $viewWrapper);
        $tmp1 = str_replace('valueC', "{" . "{" . "$" . "model->" . $field . "}}", $tmp1);
        $view .= $tmp1;

        $inputType = $type[$index];
        $rules .= "'" . $field . "'=>'required'," . PHP_EOL;
        $filters .= "'" . $field . "'=>'trim|escape|strip_tags'," . PHP_EOL;
        $table .= "<th>" . str_replace('id', '', str_replace('_', ' ', $field)) . "</th>" . PHP_EOL;
        $tableBody .= "<td>" . "{{" . "$" . "model" . "->" . trim($field) . "}}</td>" . PHP_EOL;
        $dataTable .= "{" . "data:" . $field . "}," . PHP_EOL;
        $controller .= "'" . $field . "' =>" . "$" . "record->" . $field . ",";
        $store2 .= "'" . $field . "',";


        $inputType = $type[$index];
        $class = $_POST['inputClass'] . " " . $inputType;
        $inputClass = $_POST['inputClass'];
        $labelClass = $_POST['labelClass'];

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

            $wr = ' ' . str_replace('name1', $field, $wr);
            if (strpos($wr, "<label")) {
                $label1 = str_replace('name1', $field, $label);
                // $label1 = str_replace('id1', $field, $label1);
                $label1 = str_replace('value1', $labelText, $label1);
                $label1 = str_replace('class1', $labelClass, $label1);
                $result .= $label1 . PHP_EOL;
            } else if (strpos($wr, "<input")) {
                $inpt = str_replace(['name1', 'id1'], $field, $outputDesign);
                $inpt = str_replace('value1', '!empty($' . $field . ')?$' . $field . ':null', $inpt);
                $inpt = str_replace('class1', $inputClass, $inpt);
                $result .= $inpt . PHP_EOL;
            } else {
                $result .= $wr . PHP_EOL;
            }
        }
    }

    $rules = '[' . PHP_EOL . $rules . ']';
    $filters = '[' . PHP_EOL . $filters . ']';
    $table = "<tr>" . $table . "<td>Action</td>" . "</tr>";
    $tableBody = "<tr>" . $tableBody . "</tr>";
    $controller .= 'action=>' . $action;
    $dtController = str_replace('controllerC', $controller, $dtController);
    $store2 = substr($store2, 0, strlen($store2) - 1);
    $store = str_replace('fieldsC', $store2, $store);
    $update = str_replace('updateC', $store2, $update);
    $dataTableText = str_replace('dataC', $dataTable, $dataTableText);
}
?>
<html>

<head>
    <title></title>
</head>

<body>
    <?php require_once 'menu.php' ?>
    <br /><br />
    <form method="post">
        <input type="submit" value="Show" />
        Fields:<br />
        <textarea name="fields" rows="10" cols="100"><?php echo $fields;  ?></textarea>
        <br />
        Wrapper:<br />
        <textarea name="wrapper" rows="10" cols="100"><?php echo $wrapper;  ?>
<?php if (empty(trim($wrapper))) {  ?>
<div class="form-group">
<label for="exampleInputEmail1">Email address</label>
<input type="email" name="email" class="form-control" id="exampleInputEmail1"  placeholder="Enter email">
<p class="error"> {{ $errors->first('name1') }} </p>
</div>
<?php
}
?>
        </textarea>
        <br />
        View Wrapper:<br />
        <textarea name="viewWrapper" rows="10" cols="100"><?php echo $viewWrapper;  ?>
<?php if (empty(trim($viewWrapper))) {  ?>
<div class="col-sm-6">
labelC
</div>
<div class="col-sm-6">
valueC
</div>
<?php
}
?>
</textarea>
        <br />
        Input Class :<br /> <input type="text" name="inputClass" value="<?php echo $inputClass ?>" />
        <br />
        Label Class :<br /> <input type="text" name="labelClass" value="<?php echo $labelClass ?>" />
        <br /><br />
        Field Type:
        <br />

        <?php
        foreach ($fieldsArr as $index => $rawField) {

            $rawFieldWithUnderScore = str_replace(" ", "_", trim($rawField));
        ?>
            <input type="text" name="fieldName[]" value="<?php echo $rawFieldWithUnderScore; ?>" />
            <select name="designType[]">
                <option <?php if (isset($type[$index]) && $type[$index] == "text") {
                            echo "selected='selected'";
                        } ?> value="text">Text</option>
                <option <?php if (isset($type[$index]) && $type[$index] == "textarea") {
                            echo "selected='selected'";
                        } ?> value="textarea">TextArea</option>
                <option <?php if (isset($type[$index]) && $type[$index] == "select") {
                            echo "selected='selected'";
                        } ?> value="select">Select</option>
                <option <?php if (isset($type[$index]) && $type[$index] == "radio") {
                            echo "selected='selected'";
                        } ?> value="radio">Radio</option>
                <option <?php if (isset($type[$index]) && $type[$index] == "checkbox") {
                            echo "selected='selected'";
                        } ?> value="checkbox">Checkbox</option>
                <option <?php if (isset($type[$index]) && $type[$index] == "file") {
                            echo "selected='selected'";
                        } ?> value="file">File</option>
            </select>
            <br />
        <?php
        }
        ?>
        <br />

        Rules:<br />
        <textarea name="rules" rows="10" cols="100"><?php echo $rules;  ?></textarea>
        <br />
        filters:<br />
        <textarea name="filters" rows="10" cols="100"><?php echo $filters;  ?></textarea>
        <br />
        table:<br />
        <textarea name="table" rows="10" cols="100"><?php echo $table;  ?></textarea>
        <br />
        table body:<br />
        <textarea name="tableBody" rows="10" cols="100"><?php echo $tableBody;  ?></textarea>
        <br />
        Data Table:<br />
        <textarea name="tableData" rows="10" cols="100"><?php echo $dataTableText;  ?></textarea>
        <br />
        Design:<br />
        <textarea name="design" rows="10" cols="100"><?php echo $result;  ?></textarea>
        <br />

        Controller Index:<br />
        <textarea name="controllerIndex" rows="10" cols="100"><?php echo $dtController;  ?></textarea>
        <br />
        Store:<br />
        <textarea name="store" rows="10" cols="100"><?php echo $store;  ?></textarea>
        <br />
        Update:<br />
        <textarea name="update" rows="10" cols="100"><?php echo $update;  ?></textarea>
        <br />
        View:<br />
        <textarea name="view" rows="10" cols="100"><?php echo $view;  ?></textarea>
        <br />
    </form>
</body>

</html>