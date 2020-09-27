<?php
@session_start();
include('../includes/connect.php');
class Model_Users {

        public function addNewUser($name,$mailid,$contact,$pwd,$cpwd,$Company,$Site_Id,$section) {
        $insert_data ="INSERT INTO helpdk_users (`id`,`name`,`mailid`,`contact`,
        	`user_password`,`tenant_id`,`Site_Id`,`tenant _admin`)
        	VALUES (NULL,'$name','$mailid','$contact','$pwd','$Company','$Site_Id',
        	'$section')";
     	$result = $GLOBALS['connect_main']->rawQuery($insert_data);
  	 	return 1; 


    }
	
	public function approveuser($id)
	{
		$sql="UPDATE `helpdk_users` SET `activation_request` = 'approve' WHERE `id` =$id";
		$result = $GLOBALS['connect_main']->rawQuery($sql);
			return 1;
		
	}
	
		public function blockuser($id)
	{
		$sql="UPDATE `helpdk_users` SET `activation_request` = 'block' WHERE `id` =$id";
		$result = $GLOBALS['connect_main']->rawQuery($sql);
			return 1;
		
	}
}
?>