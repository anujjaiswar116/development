<?php

if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
include_once '../includes/connect.php';
include '../includes/Function.php';
// ". $GLOBALS['master_db'] . ".
if (isset($_REQUEST['action'])) {
$action = $_REQUEST['action'];
$query = "SELECT id,name,mailid,activation_request,verification_flag FROM helpdk_users where tenant_id='".$action."' ";
$result = $GLOBALS['connect_main']->rawQuery($query);
//print_r($result);
$count = count($result);
$i = 1;
$data = array();
foreach($result as $row) {

    if($row['verification_flag']=='0'){
        $status="No";

    }else{

        $status="Yes";
    }
	
	if(strtolower($row['activation_request'])=='pending')
	{
		$button = '<button type="button" class="btn btn-success" onclick="approveuser('.$row['id'].')"><span class="fa fa-check"></span> Approve </button>';
	}
	else if(strtolower($row['activation_request'])=='approve')
	{
		$button = '<button type="button" class="btn btn-danger" onclick="blockuser('.$row['id'].')"><span class="fa fa-ban"></span> Block</button>';
	}
	else
	{
		$button = '<button type="button" class="btn btn-success" onclick="approveuser('.$row['id'].')"><span class="fa fa-check"> Approve</span></button>';
	}
	
	if(strtolower($row['activation_request'])=='pending')
	{
		$stat = '<span style="color:black;font-size: 12px;font-weight: 600;text-transform: capitalize;">'.$row['activation_request'].' </span>';	
	}
	else if(strtolower($row['activation_request'])=='approve')
	{
		$stat = '<span style="color:green;font-size: 12px;font-weight: 600;text-transform: capitalize;">'.$row['activation_request'].' </span>';
	}
	else
	{
		$stat = '<span style="color:red;font-size: 12px;font-weight: 600;text-transform: capitalize;">'.$row['activation_request'].' </span>';
	}

             $array = array();
            $array[] ='<span class="app_view">' . $i . '</span>';
            $array[] = '<span class="app_view">' . $row['name'] . '</span>';
            $array[] = '<span class="app_view">' . $row['mailid'] . '</span>';
            $array[] = '<span class="app_view">' . $status .'</span>';
            $array[] = '<span class="app_view"><div id="status'.$row['id'].'">' . $stat .'</div></span>';
            if($_SESSION['login_userfor']=="SM"){
				
             $array[] = '<span class="app_view"><div id="'.$row['id'].'">'. $button .'</div></span>';
			}
			else
			{
				$array[] = '<span class="app_view"><div id="'.$row['id'].'"></div></span>';
			}

$i++;
 $data[] = $array;
 //print_r($array);


}

//     $order_id = $result[$i]["Auto_Id"];
//     $q1 = "SELECT iim.Item_Name,iim.UOM,iid.Quantity,iid.Unit_Rate,ipo.Total_Price,ipo.igst,ipo.cgst,ipo.sgst,ipo.Order_Status,iid.`Total`,iid.`GST_Rate`,iid.`Total_With_GST` FROM pun_inventory_po_item_details iid 
//     left join pun_inventory_purchase_order ipo on ipo.Auto_Id = iid.Purchase_Order_Id
//     left join pun_inventory_item_master iim on iim.Auto_ID = iid.Item_Id
//     where ipo.Auto_Id='$order_id' AND iid.Record_Status !='D' ";
//     $result1 = $GLOBALS['connect']->rawQuery($q1);
//     foreach ($result1 as $item) {
//        $item_details[]=$item['Item_Name'];

//     }
//     $item_name=implode(',',$item_details);
//     $indx = $i + 1;

// if(strlen($item_name)>=50)
//         {
//         $array[] = '<span class="app_view">'.mb_substr($item_name, 0, 50).'<span style="display:none;" class="content'.$indx.'">'.mb_substr($item_name,50, 5000).'<a class="show_content'.$indx.'" data-content="toggle-text" onclick="readless('.$indx.');"> Read less</a> </span></span> <a class="show_hide'.$indx.'" data-content="toggle-text" onclick="readmore('.$indx.');"> Read More</a> </span>';
//         }else{
//             $array[] =$item_name;
//         }
//     $response['data'][] = array(
//         '<span class="app_view">' . $indx . '</span>',
//         '<span class="app_view">' . $result[$i]['Po_No'] . '</span>',
// 		'<span class="app_view">' . $result[$i]['Employee_Name'] . '</span>',
//         $array,
// 		'<span class="app_view">' . $result[$i]['Created_At'] . '</span>',
//         '<span class="app_view">' . $result[$i]['Company_Name'] . '</span>',
// 		'<span class="app_view">' . $result[$i]['Total_Price'] . '</span>',
//         '<span class="app_view">' .$status. '</span>',
		
		
//         '<a title="View GRN" onClick="ShowGRN(\'' . $order_id . '\')" class="btn btn-circle btn-danger" style="padding:2.5px 5.5px;color:black;"><i class="fa fa-file-text-o" ></i></a>'
//     );
//     $item_details=[];
//     $array=[];
//     $i++;
// }


}

echo json_encode(array('data'=>$data));

?>
