<?php
include_once '../includes/connect.php';
include '../includes/Function.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);

require_once '../PHPExcel-develop/Classes/PHPExcel/IOFactory.php';

require_once '../PHPExcel-develop/Classes/PHPExcel.php';


$action = getRequest('action');
$Date1 = getRequest('concat_date1');
//echo $Date1; 
$Date_Daily_From = date('Ymd000000', strtotime($Date1));
//echo $Date_Daily_From;
$Date_Daily_To = date('Ymd235959', strtotime($Date1));


foreach (glob("Report_Upload/*.xls") as $filename) {
      unlink($filename);    
      }


if ($action == 'getreport') {

     $query = "SELECT DISTINCT td.Record_Key,asm.Asset_Name,

            am.Activity_Name,td.Scan_Type,td.Task_Scheduled_Date,

            td.Task_Start_At,LOWER(um.Employee_Name) as Employee_Name,

            guser.Group_Name,td.Remarks,td.GeoLoc, td.Task_Status, td.Auto_Id

            FROM pun_task_details td

            LEFT JOIN pun_activity_frequency af ON td.Activity_Frequency_Id=af.Auto_Id

            LEFT JOIN pun_asset_activity_linking aal ON aal.Auto_Id=af.Asset_Activity_Linking_Id

            LEFT JOIN pun_asset_smart_place_master asm ON aal.Asset_Id=asm.Auto_Id

            LEFT JOIN pun_activity_master am ON aal.Activity_Id=am.Auto_Id

            LEFT JOIN pun_asset_activity_assigned_to ato ON aal.Auto_Id=ato.Asset_Activity_Linking_Id

            LEFT JOIN " . $GLOBALS['master_db'] . ".ker_user_master um ON um.auto_id=td.assigned_to_user_id

            LEFT JOIN " . $GLOBALS['master_db'] . ".pun_user_group guser ON td.Assigned_To_User_Group_Id=guser.Auto_Id

            WHERE  td.Task_Status IN ('completed', 'unplanned','delayed')

            AND td.Scheduled_Date >='" .$Date_Daily_From. "'AND td.Scheduled_Date <='" .$Date_Daily_To. "'";

          $result = $GLOBALS['connect']->rawQuery($query);

          if($result) {

          $row = count($result);

          $spreadsheet = new PHPExcel();
          $spreadsheet->setActiveSheetIndex(0);
          $spreadsheet->getActiveSheet()->setTitle('logbook Report');
          //print_r($spreadsheet);
          $rowCount = 1;
          $i = 0;
          $index = 1;
          while ($i < $row) {
          $paraindex = 1;
          $AssetName = $result[$i]['Asset_Name'];
          $ActivityName = $result[$i]['Activity_Name'];
          $TaskScheduledDate = $result[$i]['Task_Scheduled_Date'];
          $TaskStartAt = $result[$i]['Task_Start_At'];
          $EmployeeName = $result[$i]['Employee_Name'];
          $Remarks = $result[$i]['Remarks'];

        $spreadsheet->getActiveSheet()->SetCellValue('A' . $rowCount, 'Sr. No');
        $spreadsheet->getActiveSheet()->SetCellValue('B' . $rowCount, 'Asset Name');
        $spreadsheet->getActiveSheet()->SetCellValue('C' . $rowCount, 'Activity Name');
        $spreadsheet->getActiveSheet()->SetCellValue('D' . $rowCount, 'Task Scheduled Date');
        $spreadsheet->getActiveSheet()->SetCellValue('E' . $rowCount, 'Task Start At');
        $spreadsheet->getActiveSheet()->SetCellValue('F' . $rowCount, 'Employee Name');
        $spreadsheet->getActiveSheet()->SetCellValue('G' . $rowCount, 'Remarks');
        $spreadsheet->getActiveSheet()->getStyle('A' . $rowCount . ':G' . $rowCount . '')->getFont()->setBold(true);
        $rowCount++;
        //print_r($spreadsheet);
        $spreadsheet->getActiveSheet()->SetCellValue('A' . $rowCount, $index++);
        $spreadsheet->getActiveSheet()->SetCellValue('B' . $rowCount, $AssetName);
        $spreadsheet->getActiveSheet()->SetCellValue('C' . $rowCount, $ActivityName);
        $spreadsheet->getActiveSheet()->SetCellValue('D' . $rowCount, $TaskScheduledDate);
        $spreadsheet->getActiveSheet()->SetCellValue('E' . $rowCount, $TaskStartAt);
        $spreadsheet->getActiveSheet()->SetCellValue('F' . $rowCount, $EmployeeName);
        $spreadsheet->getActiveSheet()->SetCellValue('G' . $rowCount, $Remarks);
        $rowCount++;

        $spreadsheet->getActiveSheet()->SetCellValue('A' . $rowCount, 'Sr.No');
        $spreadsheet->getActiveSheet()->SetCellValue('B' . $rowCount, 'Parameter Name');
        $spreadsheet->getActiveSheet()->SetCellValue('C' . $rowCount, 'UOM');
        $spreadsheet->getActiveSheet()->SetCellValue('D' . $rowCount, 'Reading');
        $spreadsheet->getActiveSheet()->SetCellValue('E' . $rowCount, 'Warning Range Min Value');
        $spreadsheet->getActiveSheet()->SetCellValue('F' . $rowCount, 'Warning Range Max Value');
        $spreadsheet->getActiveSheet()->SetCellValue('G' . $rowCount, 'Status');
        $spreadsheet->getActiveSheet()->getStyle('A' . $rowCount . ':G' . $rowCount . '')->getFont()->setBold(true);
        $rowCount ++;

       $meterresult = $GLOBALS['connect']->rawQuery('SELECT  ROUND(mr.Reading,4) as Reading, mr.UOM, fs.Field_Label, fs.Auto_Id
                 FROM pun_meter_reading mr 
                 LEFT JOIN pun_form_structure fs on mr.Form_Structure_Id=fs.Auto_Id
                 LEFT JOIN pun_task_details td ON mr.Task_Id=td.Auto_Id
                 WHERE  mr.Task_Id = "' . $result[$i]['Auto_Id'] . '"  ORDER BY fs.Record_Key ASC');
       //print_r($meterresult);

        $datapostingresult = $GLOBALS['connect']->rawQuery('SELECT  fs.Auto_Id, fs.Field_Label, tdp.Value,fs.UOM, CONCAT(pv.Field_Limit_From," - ",pv.Field_Limit_To) AS Field_Limit,
                    CONCAT(pv.Threshold_From," - ",pv.Threshold_To) AS Warning_Limit,
                   IF(CAST(tdp.Value AS DECIMAL(9,2)) < pv.Threshold_From, "LOW",IF( CAST(tdp.Value AS DECIMAL(9,2)) > pv.Threshold_To, "HIGH", "")) AS Status
                   FROM pun_task_data_posting tdp 
                   LEFT JOIN pun_form_structure fs on tdp.Form_Structure_Id=fs.Auto_Id
                   LEFT JOIN pun_task_details td ON tdp.Task_Id=td.Auto_Id
                   LEFT JOIN pun_parameter_validation pv ON 
                   (pv.Activity_Frequency_Id=td.Activity_Frequency_Id AND pv.Form_Structure_Id=tdp.Form_Structure_Id)
                    WHERE  tdp.Task_Id = "' . $result[$i]['Auto_Id'] . '"  ORDER BY fs.Record_Key ASC');
        //print_r($datapostingresult);

         $para_result = $GLOBALS['connect']->rawQuery("SELECT  param.Form_Structure_Id, param.Field_Limit_From, param.Field_Limit_To,
                  param.Threshold_From, param.Threshold_To
                  FROM pun_task_details task LEFT JOIN pun_parameter_validation param on task.Activity_Frequency_Id = param.Activity_Frequency_Id
                  INNER JOIN pun_task_data_posting tdp ON tdp.Task_Id=task.Auto_Id
                  WHERE  task.Auto_Id = '" . $result[$i]['Auto_Id'] . "' ");

  // print_r($para_result);

        if ($para_result) {
            foreach ($meterresult as $record) {
                $spreadsheet->getActiveSheet()->SetCellValue('A' . $rowCount, $paraindex++);
                $spreadsheet->getActiveSheet()->SetCellValue('B' . $rowCount, $record['Field_Label']);
                $spreadsheet->getActiveSheet()->SetCellValue('C' . $rowCount, $record['UOM']);
                $spreadsheet->getActiveSheet()->SetCellValue('D' . $rowCount, $record['Reading']);
                $from = '';
                $to = '';

                foreach ($para_result as $param) {
                    if ($param['Form_Structure_Id'] == $record['Auto_Id']) {
                        $from = $param['Threshold_From'];
                        $to = $param['Threshold_To'];
                        break;
                    }
                }
                $spreadsheet->getActiveSheet()->SetCellValue('E' . $rowCount, $from);
                $spreadsheet->getActiveSheet()->SetCellValue('F' . $rowCount, $to);
                $spreadsheet->getActiveSheet()->SetCellValue('G' . $rowCount, '');
                $rowCount++;
            }
        } 



        else {
            foreach ($meterresult as $record) {
                $spreadsheet->getActiveSheet()->SetCellValue('A' . $rowCount, $paraindex++);
                $spreadsheet->getActiveSheet()->SetCellValue('B' . $rowCount, $record['Field_Label']);
                $spreadsheet->getActiveSheet()->SetCellValue('C' . $rowCount, $record['UOM']);
                $spreadsheet->getActiveSheet()->SetCellValue('D' . $rowCount, $record['Reading']);
                $spreadsheet->getActiveSheet()->SetCellValue('E' . $rowCount, '');
                $spreadsheet->getActiveSheet()->SetCellValue('F' . $rowCount, '');
                $spreadsheet->getActiveSheet()->SetCellValue('G' . $rowCount, '');
                $rowCount ++;
            }
        }

        if ($para_result) {
            foreach ($datapostingresult as $recorddata) {
                $spreadsheet->getActiveSheet()->SetCellValue('A' . $rowCount, $paraindex++);
                $spreadsheet->getActiveSheet()->SetCellValue('B' . $rowCount, $recorddata['Field_Label']);
                $spreadsheet->getActiveSheet()->SetCellValue('C' . $rowCount, $recorddata['UOM']);
                $spreadsheet->getActiveSheet()->SetCellValue('D' . $rowCount, $recorddata['Value']);
                $from = '';
                $to = '';

                foreach ($para_result as $param) {
                    if ($param['Form_Structure_Id'] == $recorddata['Auto_Id']) {
                        $from = $param['Threshold_From'];
                        $to = $param['Threshold_To'];
                        break;
                    }
                }
                $spreadsheet->getActiveSheet()->SetCellValue('E' . $rowCount, $from);
                $spreadsheet->getActiveSheet()->SetCellValue('F' . $rowCount, $to);
                $spreadsheet->getActiveSheet()->SetCellValue('G' . $rowCount, $recorddata['Status']);
                $rowCount ++;
            }
        } 

        else {
            foreach ($datapostingresult as $recorddata) {
                $spreadsheet->getActiveSheet()->SetCellValue('A' . $rowCount, $paraindex++);
                $spreadsheet->getActiveSheet()->SetCellValue('B' . $rowCount, $recorddata['Field_Label']);
                $spreadsheet->getActiveSheet()->SetCellValue('C' . $rowCount, $recorddata['UOM']);
                $spreadsheet->getActiveSheet()->SetCellValue('D' . $rowCount, $recorddata['Value']);
                $spreadsheet->getActiveSheet()->SetCellValue('E' . $rowCount, '');
                $spreadsheet->getActiveSheet()->SetCellValue('F' . $rowCount, '');
                $spreadsheet->getActiveSheet()->SetCellValue('G' . $rowCount, '');
                $rowCount ++;
            }
        }
        $i++;

    

    
}
    $spreadsheet->getActiveSheet()->getStyle('A1:' .
        $spreadsheet->getActiveSheet()->getHighestColumn() .
        $spreadsheet->getActiveSheet()->getHighestRow()
    )->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(25);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(25);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);



    $filename_daily='Report_Upload/Logbook_'.$Date1.'.xls';
    $objWriter = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel5');
    $objWriter->save($filename_daily);
    $newfilename='ajax/'.$filename_daily;
    echo $newfilename; 
   

}
 else 
 {
  echo 1 ;
 }

}   




else {


      $DateFrom=$_REQUEST['datefrom'];
      $time  = strtotime($DateFrom);
      $day   = date('d',$time);
      $month = date('m',$time);
      $year  = date('Y',$time);

      $Date_To=$_REQUEST['dateto'];
      $step = '+1 day';
      $format = 'Ymd';
      $dates = [];
      $inputFileNames=[];
      $sitename=$_SESSION['login_sitename'];

      if($sitename=="Divyasree")
      {
      $filePath='../reports/Divyasree/logbook';
      }

      else if($sitename=="Aerocity")
      {
      $filePath='../reports/Aerocity/logbook';
      }


      else if($sitename=="Atmaram")
      {
      $filePath='../reports/Atmaram/logbook';
      }

      else if($sitename=="Centre Court Gurgaon")
      {
      $filePath='../reports/Gurgaon/logbook';
      }

      else if($sitename=="Lowerparel")
      {
      $filePath='../reports/Lowerparel/logbook';
      }

      else if($sitename=="Pune")
      {
      $filePath='../reports/Pune/logbook';
      }

      $current = strtotime($DateFrom);
      $last = strtotime($Date_To);
      $i=0;
      while ($current <= $last) {
        $dates[] = date($format, $current);
        $current = strtotime($step, $current);


        $inputFileNames[]=$filePath.'/'.'Logbook_'.$dates[$i].'.xls';


      $i++;

      }



      $spreadsheet1 = PHPExcel_IOFactory::load($inputFileNames[0]);


      for($i=1; $i<count($inputFileNames); $i++)
      {
        $spreadsheet2 = PHPExcel_IOFactory::load($inputFileNames[$i]);


        foreach ($spreadsheet2->getSheetNames() as $sheetName) {

            $findEndDataRow = $spreadsheet2->getActiveSheet()->getHighestRow();
            $findEndDataColumn = $spreadsheet2->getActiveSheet()->getHighestColumn();
            $findEndData = $findEndDataColumn . $findEndDataRow;

            $beeData = $spreadsheet2->getActiveSheet()->rangeToArray('A2:' . $findEndData);
            $appendStartRow = $spreadsheet1->getActiveSheet()->getHighestRow() + 1;
            $spreadsheet1->getActiveSheet()->fromArray($beeData, null, 'A' . $appendStartRow);

        }

      }


      $file='Report_Upload/Logbook_'.$year.''.$month.'.xls';
      $writer = PHPExcel_IOFactory::createWriter($spreadsheet1, 'Excel5');
      $writer->save($file);
      $newfile='ajax/'.$file;
      echo $newfile;
  }   

?>

