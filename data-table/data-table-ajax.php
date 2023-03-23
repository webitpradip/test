<?php
    ob_start();
?>
$response = array();
$search = $this->input->post('search');
$q = isset($search['value']) ? $search['value'] : '';
$start = $this->input->post('start');
$length = $this->input->post('length');
$draw = $_POST["draw"];

//==========================================


$where = '';
$start = !$start ? 0 : $start;
$length = !$length ? 10 : $length;
$data = [];
$where = '';
if ($q != '') {
    $q = $this->security->xss_clean($q);
    $columns = ['food_table.item_name', 'food_table.food_desc'];
    foreach ($columns as $column) {
        if (!empty($where)) {
            $where .= " or ";
        }
        $where .= $column . " like '%" . $q . "%'";
    }
    $this->db->where($where);
}

$list = $this->db->get('foods')->result_array();

// echo "<pre>";
//     echo $this->db->last_query();
//     die();

$counter = $start;
foreach ($list as $row => $val) {
    $sub_array = array();
    $sub_array[] = ++$counter;

    $sub_array[] = '<img src="' . base_url() . 'uploads/food/thumbnail/' . $val['food_pic'] . '" class="img-thumbnail" width="50" height="35" />';

    $sub_array[] = $val['item_name'];
    $sub_array[] = substr($val['food_desc'], 0, 30);
    // $sub_array[] = substr($val['food_type'],0,30);
    // $sub_array[] = $val['name'];
    // $sub_array[] = $val['purpose'];


    $url = base_url('foods/status/' . $val['id'] . '/' . $val['status']);
    $edit_url = base_url('foods/edit/' . $val['id']);
    $del_url = base_url('foods/delete/' . $val['id']);
    // $sub_array[] = '<a href="'.$url.'" class="badge '.($val['status']==1)?"badge-success":"badge-danger".' change-status" >'.($val['status']==1)?'Active':'Pending'.'</a>'; 
    $sub_array[] = '<a href="' . $edit_url . '" class="btn btn-primary btn-sm text-white"><i class="mdi mdi-pencil"></i>Edit</a>
				<a href="' . $del_url . '" class="btn btn-danger btn-sm text-white delete-data"><i class="mdi mdi-delete"></i>Delete</a>';
    $data[] = $sub_array;
}


$this->db->from('food_table');
$this->db->join('food_menu', 'food_menu.id=food_table.food_menu_id', 'inner');
$this->db->join('business', 'business.user_id=food_table.biz_user_id', 'left');
$this->db->select('food_table.item_name,food_table.food_desc,food_table.food_type,food_table.food_pic,food_table.main_price,food_menu.menu_item_name,food_menu.purpose,business.name');
$totalNoOfRecords = $this->db->count_all_results();

$output = array(
    "draw" => intval($draw),
    "recordsTotal" => $totalNoOfRecords,
    "recordsFiltered" => $totalNoOfRecords,
    "data" => $data
);
return json_encode($output);
<?php
$var= ob_get_clean();
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
<h6>Data Table</h6>
<textarea rows="30" cols="180">
    <?php echo htmlspecialchars($var); ?>
</textarea>

<h6>Javascript</h6>
<textarea rows="30" cols="180">
    if($('#foods-listing').length>0) {
		$('#foods-listing').DataTable({
			"aLengthMenu": [
				[5, 10, 15, -1],
				[5, 10, 15, "All"]
			],
			"processing":true,  
			"serverSide":true,  
			"order":[],  
			"ajax":{  
			    url:"base_url('foods/search')",  
			    type:"POST"  
			},  
			"columnDefs":[  
			    {  
			         "targets":[0, 3, 4],  
			         "orderable":false,  
			    },  
			]
		});
	}
</textarea>
</body>
</html>
