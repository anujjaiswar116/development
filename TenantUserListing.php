<?php

include 'includes/session.php';
include 'includes/connect.php';
date_default_timezone_set('Asia/Kolkata');
@session_start();
if (isset($_SESSION['login_db'])) {
	
    ?>

<style>
.modal1{
 margin-top:5%;

}
#modal-default{
width: 100%;
}
.btn-success,.btn-success:hover {
    color: #fff;
    background-color: #5cb85c;
    border-color: #4cae4c;
}


</style>
<head>
    <ul class="page-breadcrumb breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a onclick="refresh();">Home</a>
            
        </li>
        <li>
            <span>Tenant User Listing</span>
        </li>
    </ul>
  </head>

  <div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-file"></i>
                    <span class="caption-subject  bold uppercase">Tenant User Listing </span>
                </div>
            </div>
            
            <div class="panel body">
              <form  class="form-horizontal" id="tenantform" name="tenantform" method="post">
                <div class="form-group form-md-line-input">
                  <div class="col-md-12" style="padding:0 !important">
                    <div class="col-md-3">
                      <select class="form-control" id="tenant" name="tenant" onchange="TenantList(this.value);">
                       </select>
                    </div>
                    <div class="col-md-3">
                    	<button type="button" class="btn btn green" name="addnewuser" onclick="AddUser();">Add New User
                    		<i class="fa fa-plus"></i>
                    	</button>
                    </div>
                  </div>
                  <div id="grp-modal" class="modal1" tabindex="-1" role="dialog"> </div>
                  <div class="col-md-12" style="padding:0 !important">
                    <table class="table table-striped table-bordered" width='100%' id="TenantList">
                      <thead>
                        <tr>
                        <th>Sr No.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Email Verified</th>
                        <th>Account Activation Request</th>
                        <th>Action</th>
                      </tr>
                      </thead>
                      <tbody>
                      	
                      </tbody>
                    </table>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>


  <div class="modal fade modelr" id="modal-default">
        <div class="modal-sm modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" style="color:white !important;margin-top:10px !important;opacity:1 !important;width:15px !important;height:15px !important;" aria-hidden="true" name="close"></button>
                    
<!--                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->
                <div class="modal-body">
                </div>
            </div>
          </div>
    </div>



<script type="text/javascript">


	$(document).ready(function () {
		getTenants();

	});	



  function getTenants() {

    $.ajax({

      type: 'POST',
      cache: false,
      url:'controller/contr_user.php?action=loadtenant',
      contentType: false,
      processData: false,

                success: function (data) {

                	var d = JSON.parse(data);
                	var len = d.length;

                	$('#tenant').children().remove().end().append('<option value="" hidden>Select Company</option>');


                	for (var o = 0; o < len; o++)
                	{
                    	$('#tenant').append("<option value=" + d[o].auto_id + ">" + d[o].tenant_name + "</option>");
                	}
                }
            }); 
         }



         function TenantList(id) {
    
    	if ( $.fn.DataTable.isDataTable('#TenantList')) 
        {
          $('#TenantList').DataTable().destroy();
        }
            var dNow = new Date();
            var currentdate = dNow.getFullYear() + '' + (dNow.getMonth() + 1) + '' + dNow.getDate() + '' + dNow.getHours() + '' + dNow.getMinutes() + '' + dNow.getSeconds();
            var filenm = "Tenant User Listing" + currentdate;
            // Setup - add a text input to each footer cell
            var table = $('#TenantList').DataTable({
                "autoWidth": false,
                "lengthChange": false,
                "paging": true,
                "pageLength": 10,
                "ordering": false,
                "language": {
                    "paginate": {
                        "previous": "<",
                        "next": ">",
                    },
                    "zeroRecords": "No Records Found"
                },
                "pagingType": "simple_numbers",
                "dom": 'lBrtip',
                "ajax": {
                    url: 'ajax/geTenantListing.php?action='+id,
                    type: 'POST',
                },
                buttons: [{
                        extend: 'collection',
                        text: 'Export',
                        buttons: [
                            {
                                extend: 'excel',
                                title: filenm
                            },
                            {
                                extend: 'csv',
                                title: filenm
                            },
                            {
                                extend: 'pdf',
                                title: filenm,
                                extend: 'pdfHtml5',
                                pageSize: 'LEGAL'
                            },
                            {
                                extend: 'print',
                                title: filenm
                            },
                        ]
                    }],
            });

            // Filter event handler
            table.columns().eq(0).each(function (t) {
                $('input', table.column(t).footer()).on('keyup change', function () {
                    table.column(t).search(this.value.replace(/(;|,)\s?/g, "|"), true, false).draw();
                });
            });

}


		function AddUser() {  
            $('.modal-body').load('AddUser.php', function () {
                $('#modal-default').modal({show: true});
               
            });
        }
		
		function approveuser(id) 
		{
			if (confirm('Are you sure you want Approve this Employee?')) 
			{
				$.ajax({
				  type: 'POST',
				  cache: false,
				  url:'controller/contr_user.php?action=approveuser&id='+id,
				  contentType: false,
				  processData: false,
				  success: function (data) 
				  {
					 if(data == 1)
					 {
						$('#'+id).empty();
						$('#status'+id).empty();
						$('#status'+id).append('<span style="color:green;font-size: 12px;font-weight: 600;">Approve</span>');
						$('#'+id).append('<button type="button" class="btn btn-danger" onclick="blockuser('+id+')"><span class="fa fa-ban"></span> Block</button>');
					 }
				  }
				});
			}
		}
		
		function blockuser(id) 
		{
			if (confirm('Are you sure you want Block this Employee?')) 
			{
			$.ajax({
			  type: 'POST',
			  cache: false,
			  url:'controller/contr_user.php?action=blockuser&id='+id,
			  contentType: false,
			  processData: false,
			  success: function (data) 
			  {
				 if(data == 1)
				 {
					 $('#'+id).empty();
					 $('#status'+id).empty();
					 $('#status'+id).append('<span style="color:red;font-size: 12px;font-weight: 600;">Block</span>');
					$('#'+id).append('<button type="button" class="btn btn-success" onclick="approveuser('+id+')"><span class="fa fa-check"></span> Approve</button>');
				 }  
			  }
			});
			}
		}
		


</script>
                       
 <?php } ?>                       
