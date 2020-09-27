<?php
@session_start();
include('../includes/connect.php');
class Model_Tenants {

    public function getAllTenants() {
        $getdata = "SELECT auto_id,tenant_name FROM helpdk_tenant_master 
                    ORDER BY tenant_name";
        $result = $GLOBALS['connect_main']->rawQuery($getdata);
        return $result;

      }

       public function getAllTenants1() {
        $getdata = "SELECT auto_id,tenant_name FROM helpdk_tenant_master 
                    ORDER BY tenant_name";
        $result = $GLOBALS['connect_main']->rawQuery($getdata);
        return $result;

      }


    }
?>