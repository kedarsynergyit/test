     <section id="main-content">
          <section class="wrapper site-min-height">
			<div class="pull-left"><h3><i class="fa fa-chevron-circle-right"></i> SETTINGS</h3></div>
			<div class="pull-right"><h3><a class="btn btn-primary btn-sm btn-block" href="dashboard">Back</a></h3></div>
           <div id="page-inner"> 
              <div class="row">
                <div class="col-lg-12">
                       <div class="text-center">
                            <div id="infoSuccess"><?php echo $this->session->flashdata('success');?></div>
                             <div id="infoError"><?php echo $this->session->flashdata('failed');?></div>
                        </div>
                    <div class="panel panel-default">
                     
                        <div class="panel-heading">
                            Your Profile Details
                        </div>
                        <div class="panel-body">
                            <?php echo form_open_multipart('customerportal/updatesettings','class="form-horizontal" id="myform"');?> 
                            <div class="row">
                                
                                <div class="col-sm-5 col-md-5 col-lg-5 ml-15">
                                        <div class="form-group">
                                            <label>Company Name</label>
                                            <input type="hidden" class="form-control" name='id' id='id' value="<?php  echo $details[0]->customerid; ?>">
                                            <input class="form-control" name='company' id='company' value="<?php echo $details[0]->companyname; ?>">
                                            <div class="text-right hidden" id="e-company"><span class="alert-danger">Required Field</span></div>
                                            <div class="text-right hidden" id="e-company-text-limit"><span class="alert-danger">Maximum 100 characters allowed!</span></div>
                                        </div>
                                      
                                        <div class="form-group">
                                            <label>Contact First Name</label>
                                             <input class="form-control right" name='firstname' id='firstname' value="<?php echo $details[0]->first_name; ?>">                                             
                                            <div class="text-right hidden" id="e-firstname" ><span class="alert-danger">Required Field</span></div>
                                            <div class="text-right hidden" id="e-firstname-text-limit" ><span class="alert-danger">Maximum 20 characters allowed!</span></div>
                                        </div>
                                       <div class="form-group">
                                            <label>Contact Last Name</label>
                                             <input class="form-control right" name='lastname' id='lastname' value="<?php echo $details[0]->last_name; ?>">                                            
                                            <div class="text-right hidden" id="e-lastname" ><span class="alert-danger">Required Field</span></div>
                                            <div class="text-right hidden" id="e-lastname-text-limit" ><span class="alert-danger">Maximum 20 characters allowed!</span></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Email ID</label>                                       
                                            <input type="text" name='emailid' id="emailid" class="form-control" value="<?php echo $details[0]->emailid; ?>"> 
                                            <div class="text-right hidden" id="e-emailid" ><span class="alert-danger">Required Field</span></div>
                                            <div class="text-right hidden" id="e-emailid-validate" ><span class="alert-danger">Please enter valid email</span></div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Business Email ID</label>                                       
                                            <input type="text" name='business_emailid' id="business_emailid" class="form-control" value="<?php echo $details[0]->business_email; ?>"> 
                                            <div class="text-right hidden" id="e-business_emailid" ><span class="alert-danger">Required Field</span></div>
                                            <div class="text-right hidden" id="e-business_emailid-validate" ><span class="alert-danger">Please enter valid email</span></div>
                                        </div>
                                        
                                    <div class="form-group">
                                            <label>Contact Number</label>                                       
                                            <input type="text" name='contactno' id="contactno" class="form-control" value="<?php echo $details[0]->contactno; ?>"> 
                                            <div class="text-right hidden" id="e-contactno" ><span class="alert-danger">Required Field</span></div>
                                            <div class="text-right hidden" id="e-contactno-text-limit" ><span class="alert-danger">Maximum 20 characters allowed!</span></div>
                                            <div class="text-right hidden" id="e-contactno-patern" ><span class="alert-danger">Please enter valid phone number!</span></div>
                                        </div>
                                      
                                    <div class="form-group">
										<span class="btn btn-sm btn-primary" name="request" id="request">Reset Password</span> 
									</div>
                                    <div id="resetpwd" class="hidden" >
                                         <div class="form-group">
                                            <label>Current Password</label>                                       
                                            <input type="password" name='password' id="password" class="form-control" value=""> 
                                             <div class="text-right hidden" id="e-password" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        <div class="form-group">
                                            <label>New Password</label>                                       
                                            <input type="password" name='newpassword' id="newpassword" class="form-control" value=""> 
                                             <div class="text-right hidden" id="e-newpassword" ><span class="alert-danger">Required Field</span></div>
                                             <div class="text-right hidden" id="e-newpassword-limit" ><span class="alert-danger">password must be minimum 8 characters with one capital, one digit and one special character combination</span></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Re-enter new Password</label>                                       
                                            <input type="password" name='repassword' id="repassword" class="form-control" value=""> 
                                             <div class="text-right hidden" id="e-repassword" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                         <div class="text-center hidden" id="e-correct" ><span class="alert-danger">New password and Re-entered password should be same</span></div>
										  <div class="text-center hidden" id="e-same" ><span class="alert-danger">old password and new password should not be same</span></div>

                                    </div>
                                     
                                    <div class="form-group">
                                        <div class="pull-left"><button type="submit" name="submit" id="submit" class="btn btn-block btn-primary btn-sm">Submit</button></div>
                                        <div class="pull-left" style="margin-left: 10px;"><button type="reset" name="reset" id="reset"  class="btn btn-block btn-primary btn-sm">Reset</button></div>
                                    </div>
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                                
                                <div class="col-sm-1 col-md-1 col-lg-1 ml-15">&nbsp;</div>
                                
                                <div class="col-sm-4 col-md-4 col-lg-4 ml-15">
                                	<div class="row">
                                		<!-- Profile photo -->
										<div class="form-group">
											<label>Profile Picture</label> 
											<div class="ml-15">
												<?php //if($internal_user_external_user!=2){ ?>
													<input type="file" name='files' id="upload" class="form-group  ml-15">
												<?php //} ?> 
											</div>
											
											<?php 
											$src = (isset($details[0]->profile_picture) && !empty($details[0]->profile_picture) && file_exists(FCPATH.$details[0]->profile_picture))?base_url().$details[0]->profile_picture:base_url()."user/user-default.png";
											?>
											
											<img data-toggle="modal" data-target="#myModal" src="<?php echo $src; ?>" style="width: 100px; height:100px; object-fit: contain;">
											<input type='hidden' name='path' id="path" value="<?php echo base_url().$details[0]->profile_picture; ?>" />
										</div>
                                	</div>
                                </div>
                               
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
			
			</div>
             <!-- /. PAGE INNER  -->
          
			
		</section><! --/wrapper -->
      </section><!-- /MAIN CONTENT -->

         <!-- /. PAGE WRAPPER  -->
     <!-- /. WRAPPER  -->
    

 <script type="text/javascript">
     $(document).ready(function () {
        
         $("#request").click(function(){
             var x = document.getElementById('resetpwd');
            x.classList.toggle("hidden");
            $("#password").focus();
        }); 
  
    });
$("#submit").click(function() {   
    var validate="no";

    if($.trim($('#company').val())==""){ $('#e-company').removeClass("hidden"); validate="yes";} else{ $('#e-company').addClass("hidden"); }
	if($('#company').val().length>100){
		$('#e-company-text-limit').removeClass("hidden"); validate="yes";
	}else{ 
		$('#e-company-text-limit').addClass("hidden");
	}
     
    if($.trim($('#firstname').val())==""){ $('#e-firstname').removeClass("hidden");validate="yes";}  else{ $('#e-firstname').addClass("hidden");}

    if($('#firstname').val().length>20){
		$('#e-firstname-text-limit').removeClass("hidden"); validate="yes";
	}else{ 
		$('#e-firstname-text-limit').addClass("hidden");
	}
     
    if($.trim($('#lastname').val())==""){ $('#e-lastname').removeClass("hidden");validate="yes";}  else{ $('#e-lastname').addClass("hidden");}
    if($('#lastname').val().length>20){
		$('#e-lastname-text-limit').removeClass("hidden"); validate="yes";
	}else{ 
		$('#e-lastname-text-limit').addClass("hidden");
	}
     
    if($.trim($('#emailid').val())==""){ $('#e-emailid').removeClass("hidden");validate="yes";}  else{ $('#e-emailid').addClass("hidden");}
    if($.trim($('#emailid').val())!=""){
    	var filter = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
	    if (!filter.test($.trim($('#emailid').val()))) {
	    	$('#e-emailid-validate').removeClass("hidden"); validate="yes";
	    }else{
	    	$('#e-emailid-validate').addClass("hidden");
	    }
    }

    if($.trim($('#business_emailid').val())==""){ $('#e-business_emailid').removeClass("hidden");validate="yes";}  else{ $('#e-business_emailid').addClass("hidden");}
    if($.trim($('#business_emailid').val())!=""){
    	var filter = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
	    if (!filter.test($.trim($('#business_emailid').val()))) {
	    	$('#e-business_emailid-validate').removeClass("hidden"); validate="yes";
	    }else{
	    	$('#e-business_emailid-validate').addClass("hidden");
	    }
    }

    if($.trim($('#contactno').val())==""){ $('#e-contactno').removeClass("hidden");validate="yes";}  else{ $('#e-contactno').addClass("hidden");}
    if($('#contactno').val().length>20){
		$('#e-contactno-text-limit').removeClass("hidden"); validate="yes";
	}else{ 
		$('#e-contactno-text-limit').addClass("hidden");
	}

	// now check contact number is having numeric, +.() values only
	if($.trim($('#contactno').val())!=""){
		var filter = /\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/;///^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;
	    if (!filter.test($.trim($('#contactno').val()))) {
	    	$('#e-contactno-patern').removeClass("hidden"); validate="yes";
	    }else{
	    	$('#e-contactno-patern').addClass("hidden");
	    }
	}

    if(($.trim($('#password').val())!="")||($.trim($('#newpassword').val())!="")||($.trim($('#repassword').val())!=""))
    {

        // check if newpassword is entered, then check min 6 to 30 characters with combination of alphanumeric and special symbol combination
        //var filter = /^(?=.*?[a-z])(?=.*?[0-9])(?=.*?[^\w\s]).{6,30}$/;

        // password must be minimum 8 characters with one capital, one digit and one special character combination
        var filter = /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[^\w\s]).{8,}$/;
        
        if (!filter.test($.trim($('#newpassword').val()))) {
	    	$('#e-newpassword-limit').removeClass("hidden"); validate="yes";
	    }else{
	    	$('#e-newpassword-limit').addClass("hidden");
	    }
     
         if($.trim($('#password').val())==""){ $('#e-password').removeClass("hidden");validate="yes";}  else{ $('#e-password').addClass("hidden");}
		    if($.trim($('#password').val())==$.trim($('#newpassword').val()))
         {
               
              $('#e-same').removeClass("hidden");validate="yes";}  else{ $('#e-same').addClass("hidden");
         }
         if($.trim($('#newpassword').val())==""){ $('#e-newpassword').removeClass("hidden");validate="yes";}  else{ $('#e-newpassword').addClass("hidden");}
         if($.trim($('#repassword').val())==""){ $('#e-repassword').removeClass("hidden");validate="yes";}  else{ $('#e-repassword').addClass("hidden");}
         if(($('#newpassword').val())!=($('#repassword').val()))
         {
               
              $('#e-correct').removeClass("hidden");validate="yes";}  else{ $('#e-correct').addClass("hidden");
         }
        
    }
    if(validate=='yes'){ return false; }else{  return true;
    $('#myform').submit();
    }
});   


</script>
         <!-- Custom Js -->
      
   
</body>
</html>
