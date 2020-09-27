<?php
include('./includes/session.php');
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet"/>
<link href="assets/global/css/components.min.css" rel="stylesheet"/>
<link href="assets/global/css/components-md.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>

<style type="text/css">
    #submit
    {
        background-color:#078528!important;
    }
    #cancel
        {
        background-color:#d9232e!important;
    }

/*    .progress {
        display: block;
        text-align: center;
        width: 0;
        height: 3px;
        background: red;
        transition: width .3s;
}
    .progress.hide {
        opacity: 0;
        transition: opacity 1.3s;
}*/

</style>
<ul class="page-breadcrumb breadcrumb">
    <li>
        <i class="fa fa-home"></i>
        <a onclick="refresh();">Home</a>
    </li>
    <li>
        <span>Generate Report LogBook</span>
    </li>
</ul>
<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-file"></i>
                    <span class="caption-subject  bold uppercase">Generate Report LogBook </span>
                </div>
            </div>
            <div class="panel body">
                <form action="" class="form-horizontal" id="frm" name="frmlogsheet" method="post">
                    <div class="form-group form-md-line-input">
                            <div class="col-md-12" style="padding:0 !important">
                                <div class="col-md-4">
                                    <div class="row form-group form-md-line-input">
                                        <label class="col-md-3" style='width: 60px'>Period:</label>
                                        <div class="col-md-7">
                                            <select id='DateRange' name='DateRange' class="form-control mandatory"  onchange="GetDateInterval()">
                                                <option value='' hidden>Select</option>
                                                <option value='Daily'>Daily</option>
                                                <option value='Weekly'>Weekly</option>
                                                <option value='Monthly'>Monthly</option>
                                                <option value='Custom'>Custom</option>
                                            </select>
                                            <div class="form-control-focus"></div>
                                        </div>
                                    </div>
                                </div>

                                <div id="From_To" style="display:none;">
                                <div class="col-md-4">
                                    <div class="row form-group form-md-line-input">
                                        <label class="col-md-3 form-label">From:</label>
                                        <div class="col-md-8">
                                            <input type='text' id="dateFrom" name='dateFrom' autocomplete="off" class="date form-control mandatory" onChange="GetDateInterval()" />
                                            <div class="form-control-focus"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row form-group form-md-line-input" >
                                        <label class="col-md-3 form-label">To :</label>
                                        <div class="col-md-8">
                                            <input type='text' id="dateTo" name='dateTo' autocomplete="off"  class="date form-control mandatory"/>
                                            <div class="form-control-focus"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                                <div class="col-md-4" id="to" style="display:none;">
                                    <div class="row form-group form-md-line-input">
                                        <label class="col-sm-3 form-label">Date:</label>
                                        <div class="col-sm-8">
                                            <input type='text' class="date form-control mandatory" id="date1" name='date1' autocomplete="off"/>
                                    <div class="form-control-focus"></div>
                                </div>
                            </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12" >
                            <div class="col-md-12">
                            	<center>
                                    
                                    <button id="cancel" type="button" class="btn red-mint" 
                                    onclick="refresh();"><i class="fa fa-close"></i>Cancel
                                   </button>

                                    <button id="submit" type="button" name="logsubmit"
                                    class="btn green-meadow" onclick="downloadfile()">
                                    <i class="fa fa-check"></i> Download</button> 
                                    <span id="logsubmit"> </span>

                            </center>
                            <div id="load" align="center" ></div>
                            
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">



    $(document).ready(function () {

        $('#dateFrom').datetimepicker({
            format: 'd-M-Y',
            formatDate: 'd-M-Y',
            timepicker: false,
            maxDate: 0
        });


        $('#dateTo').datetimepicker({
            format: 'd-M-Y',
            formatDate: 'd-M-Y',
            timepicker: false,
            maxDate: 0
        });

        $('#date1').datetimepicker({
            format: 'd-M-Y',
            formatDate: 'd-M-Y',
            timepicker: false,
            maxDate: 0
        });


    });

                var month='';

                function GetDateInterval() {
                
                month=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
                var vall= $("#dateFrom").val();
                var string = vall.split('-');
                var dateonly = string[2];
                var month11 = string[1];
                var year = string[0];
                var dd = getMonth(month11);

                var seltype=$("#DateRange").val(); 
                var today = new Date(vall);
                //alert(today);
                var myDates = new Date(today); 

                if(seltype=='Weekly')
                {                   

                    //debugger;
                    document.getElementById("From_To").style.display = "block";
                    document.getElementById("to").style.display = "none"; 
                    today.setDate(today.getDate() +6);

                    var m = month[today.getMonth()];
 
                    if($("#dateFrom").val()){

                     var dNow1 = new Date();
   
         			if (Date.parse(today) >=Date.parse(dNow1)) {

             			today=dNow1;
                        var m = month[today.getMonth()];
             		}

       				var datestring = ("0" + today.getDate()).slice(-2) + "-" + m + "-" + today.getFullYear();

                    document.getElementById('dateTo').value = datestring;
                    document.getElementById('dateTo').readOnly = true;

                    $('#dateTo').datetimepicker({
                    format: 'd-M-Y',
                    formatDate: 'd-M-Y',
                    timepicker: false,
                    maxDate: today
                });


            }

            }

                else if(seltype=='Custom')
                {



                	document.getElementById("From_To").style.display = "block";
                    document.getElementById("to").style.display = "none"; 
                    document.getElementById('dateTo').readOnly = false;

                    myDates.setDate(myDates.getDate() +30);

                    var m = month[myDates.getMonth()];

                    if($("#dateFrom").val()){
                	var dNow1 = new Date();

                	 if (Date.parse(myDates) >=Date.parse(dNow1)) {

             			myDates=dNow1;

             			var m = month[myDates.getMonth()];

       				 }

                	var datestring = ("0" + myDates.getDate()).slice(-2) + "-" + m + "-" + myDates.getFullYear();


                	document.getElementById('dateTo').value = datestring;
                    //document.getElementById('dateTo').readOnly = true;

                    $('#dateTo').datetimepicker({
                    format: 'd-M-Y',
                    formatDate: 'd-M-Y',
                    timepicker: false,
                    maxDate: myDates
                });

                }  
             }   



                else if (seltype=='Monthly') {

                    document.getElementById("From_To").style.display = "block";
                    document.getElementById("to").style.display = "none";     

                    var last = new Date(new Date(new Date().setMonth(dd)).setDate(0)).getDate();

                    var lastdayofmonth=myDates.setDate(last);



                    var lastDayOfMonth = new Date(today.getFullYear(), today.getMonth()+1, 1);
                    var months = lastDayOfMonth.getUTCMonth() + 1; //months from 1-12
                    var day = lastDayOfMonth.getUTCDate();
                    var year = lastDayOfMonth.getUTCFullYear();
                    var m = month[months-1];                
                    newdate = day + "-" + m + "-" + year;

                    // lastDayOfMonth11 = new Date(today.getFullYear(), today.getMonth(), 1);
                    // console.log(lastDayOfMonth11);
                    // var months11 = lastDayOfMonth.getUTCMonth() + 1; //months from 1-12
                    // var day11 = lastDayOfMonth.getUTCDate();
                    // var year11 = lastDayOfMonth.getUTCFullYear();
                    //var m11 = months11[months11-1];   
                    // newdate11 = day11 + "-" + months11 + "-" + year11;

                    var dNow1 = new Date();

                    var currentdate1 = 1 + '-' +(dNow1.getMonth()+1) + '-' + dNow1.getFullYear();

                    $('#dateFrom').datetimepicker({
                    format: 'd-M-Y',
                    formatDate: 'd-M-Y',
                    timepicker: false,
                    maxDate: currentdate1
                });




                        if($("#dateFrom").val()){
                        document.getElementById('dateTo').value =  newdate; 
                        document.getElementById('dateTo').readOnly = true;
                }

            }





            else {

              document.getElementById("to").style.display = "block";
              document.getElementById("From_To").style.display = "none";  
              myDates.setDate(myDates.getDate());
            }
        }





            function downloadfile() {
        
                $('#load').show();
                $('#load').html('<img src="assets/img/load.gif">');


                var daterange = document.getElementById("DateRange").value;
                var urlstr='';
                var a = document.getElementById('dailyurl');


                if (daterange == 'Daily') {

                var date1 = $('#date1').val();
                var string = date1.split('-');
                var dateonly = string[2];
                var month1 = string[1];
                var year = string[0];
                var dd = getMonth(month1);

                if(dd<10){dd= 0+""+dd;}else{dd=dd; }
                
                var concat_date1=dateonly.concat(dd,year);
                var action = 'getreport';
                var dataString = "action="+action+"&concat_date1="+concat_date1;

                toastr.success('Downloading In progess');

                $.ajax({



                type: 'POST',
                url: "ajax/exportLogbookReport.php",
                data: dataString,
                cache: false,

                success: function (result) {


                   urlstr = result;

                    if (result==1)
                    {
                        toastr.error('no data available');
                    }

                    else{


                    $('#load').hide();   
                    $('#dailyurl').remove();
                    $('#logsubmit').append('<a id="dailyurl" href="' + urlstr + '" download class="btn green-meadow" style="display:none">Download</a>');
                    document.getElementById('dailyurl').click();
                }



                    }


                });  
            }

                else  {


                var datefrom = $('#dateFrom').val();

                var string_from = datefrom.split('-');
                var dateonly_from = string_from[2];
                var month_from = string_from[1];
                var year_from = string_from[0];
                var dd_from = getMonth(month_from);

                if(dd_from<10){dd_from= 0+""+dd_from;}else{dd_from=dd_from; }
                var concat_date_from=dateonly_from.concat(dd_from,year_from);
                //console.log(concat_date_from);

                var dateto = $('#dateTo').val();

                var string_to = dateto.split('-');
                var dateonly_to = string_to[2];
                var month_to = string_to[1];
                var year_to = string_to[0];
                var dd_to = getMonth(month_to);

                if(dd_to<10){dd_to= 0+""+dd_to;}else{dd_to=dd_to; }
                var concat_date_to=dateonly_to.concat(dd_to,year_to);


                toastr.success('Downloading In progess');

                       // debugger;
                        // var oReq = new XMLHttpRequest();
                        // oReq.addEventListener("progress", updateProgress);
                        // oReq.addEventListener("load", transferComplete);
                        // oReq.addEventListener("error", transferFailed);
                        // oReq.addEventListener("abort", transferCanceled);

                        //  var url111="ajax/exportLogbookReport.php";

                        //   oReq.open('POST',url111);
                        //   oReq.send();

                    $.ajax({

                        type: 'POST',
                        cache: false,
                        url: "ajax/exportLogbookReport.php?action=monthlydatadownload&datefrom=" + concat_date_from+"&dateto="+concat_date_to,
                        contentType: false,
                        processData: false,

                        xhrFields: {
onprogress: function (e) {
if (e.lengthComputable) {
console.log(e.loaded / e.total * 100 + '%');
}
}
},

                        success: function (result)
                        {
                           

                            urlstr = result;
                            
                            $('#load').hide();  
                            $('#dailyurl').remove();
                            $('#logsubmit').append('<a id="dailyurl" href="' + urlstr + '" download class="btn green-meadow" style="display:none">Download</a>');
                             document.getElementById('dailyurl').click();


                    
                }
            });
        }
    }

            function getMonth(month) {
                d = new Date().toString().split(" ")
                d[1] = month
                d = new Date(d.join(' ')).getMonth()+1
                if(!isNaN(d)) {
                  return d
                }
                return -1;
            }




function updateProgress (oEvent) {

        if (oEvent.lengthComputable) {
         var percentComplete = oEvent.loaded / oEvent.total * 100;
         console.log("percentComplete");

  } 
  else {
    console.log("loading");
  }
}

function transferComplete(evt) {
  console.log("The transfer is complete.");
}

function transferFailed(evt) {
  console.log("An error occurred while transferring the file.");
}

function transferCanceled(evt) {
  console.log("The transfer has been canceled by the user.");
}







</script>