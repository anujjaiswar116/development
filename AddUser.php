<?php
include 'includes/session.php';
include 'includes/connect.php';
date_default_timezone_set('Asia/Kolkata');
@session_start();
if (isset($_SESSION['login_db'])) {
    ?>
    <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/ionicons.min.css">
    <link rel="stylesheet" href="assets/css/blue.css">
    <link rel="stylesheet" href="assets/css/components.min.css">
    <link rel="stylesheet" href="assets/css/components-md.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <style>
        .fa{
            margin-top: 10px;
        }
    </style>

    <div class="row" id="adduserform">
        <div class="col-md-12">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="fa fa-file"></i>
                        <span class="caption-subject  bold uppercase">Add New User </span>
                    </div>
                </div>
                <div class="panel body">
                    <div class="register-box-body">
                    <form id="regform" method="post" action="" name="regform">
                    	 <div class="form-group has-feedback">
                    	 	<select class="form-control" id="Company" name="Company">
                    	 		<!-- <option value="">Select Company</option> -->
                       </select>
                    </div>

                    <div class="form-group has-feedback">
                            <input type="text" id="name" name="name" class="form-control txtOnly" required  placeholder="Full name"  required="required">
                            <span class="fa fa-user form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                            <input type="email" class="form-control " id="mailid" name="mailid" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" placeholder="Email"  required="required">
                            <span class="fa fa-envelope form-control-feedback"></span>
                    </div>
                        <div class="form-group has-feedback">
                            <input type="tel" class="form-control txtboxToFilter" id="contact" name="contact" placeholder="Mobile No"   pattern="[6-9]{1}[0-9]{9}"  maxlength="10">
                            <span class="fa fa-inbox form-control-feedback"></span>
                        </div>
                        <div class="form-group has-feedback">
                            <input type="password" class="form-control" required name="pwd" id="pwd" placeholder="Password"  required="required">
                            <span class="fa fa-lock form-control-feedback"></span>
                        </div>
                        <div class="form-group has-feedback">
                            <input type="password" class="form-control" id="cpwd" name="cpwd" required placeholder="Confirm Password"  required="required">
                            <span class="fa fa-log-in form-control-feedback"></span>
                        </div>
                        <div class="form-group">
                        	<center>
                        	<label for="admin">Admin</label>
                            <input type="radio" name="section" id="admin" value="1" required> 
                            <label for="emp">Employee</label>
                            <input type="radio" name="section" id="emp" value="0" required>
                            </center>
                        </div>

                        <div class="form-group">
                        	<center>
                               <button type="submit" id="regbtn" class="btn green-meadow">
                               <i class="fa fa-check"></i>Save
                               </button>
                               <button type="reset" id="cancel" class="btn red-mint"> 
                               	<i class="fa fa-close"> </i> Cancel
                           </button>
                           </center>
                        </div>
                    </form>                    
                </div>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript">


 $(document).ready(function () {
        
		getTenants11();
  });


	  function getTenants11() {

    	$.ajax({

      	type: 'POST',
      	cache: false,
      	url:'controller/contr_user.php?action=loadtenant1',
      	contentType: false,
      	processData: false,

                success: function (data) {

           			//alert(data);
                	var d = JSON.parse(data);
                	//alert(d);
                	var len = d.length;

                	$('#Company').children().remove().end().append('<option value="" hidden>Select Company</option>');


                	for (var o = 0; o < len; o++)
                	{
                    	$('#Company').append("<option value=" + d[o].auto_id + ">" + d[o].tenant_name + "</option>");
                	}
                }
            }); 
         }


    $("#regform").on('submit', (function (e) {
        event.preventDefault();


            var pwd = document.getElementById('pwd').value;
            var pwd_confirm = document.getElementById('cpwd').value;
			var regName = /^[a-zA-Z]+ [a-zA-Z]+$/;
var name = document.getElementById('name').value;
var mailid = document.getElementById('mailid').value;
if(!regName.test(name))
{
	var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
	if(mailid.match(mailformat))
	{
		var mobile = document.getElementById("contact").value;
        var pattern = /^[6-9][0-9]{9}$/;
        if (pattern.test(mobile)) 
		{
                if(pwd==pwd_confirm)
                {

        	$.ajax({
            type: 'POST',
            cache: false,
            data: new FormData(this),
            url: "controller/contr_user.php?&action=adduser",
            contentType: false,
            processData: false,
            success: function (result)
            {
            
                if (result == 1) {
               
                    toastr.success("Saved Successfully");
                   // window.location.href = 'TenantUserListing.php'; 
                    

                }
                else
                {
                	toastr.error("Something went wrong");
                }

                
            }

        })
    }
        else{
        toastr.error("password mismatch");
        }
	}
	else{
	 toastr.error("It is not valid mobile number");
}
}
else{
	 toastr.error("You have entered an invalid email address!");
}

}
else{
        toastr.error("Invalid name given");
        }

}));




</script>



<?php } ?>