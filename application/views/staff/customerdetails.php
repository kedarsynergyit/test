     <section id="main-content">
          <section class="wrapper site-min-height">
			<div class="pull-left"><h3><?php /* ?><i class="fa fa-chevron-circle-right"></i><?php */ ?> Customer Details</h3></div>
			<div class="pull-right"><h3><a href="customers" class="btn btn-primary btn-sm btn-block">Back</a></h3></div>
           <div id="page-inner"> 
              <div class="row">
                <div class="col-lg-12">
                       <?php /* ?><div class="text-center">
                            <div id="infoSuccess"><?php echo $this->session->flashdata('success');?></div>
                             <div id="infoError"><?php echo $this->session->flashdata('failed');?></div>
                        </div><?php */ ?>
                    <div class="panel panel-default">
                       <div class="panel-heading">
                          <?php //echo $details[0]->username; ?> 
                        </div>
                       
                        <div class="panel-body">
                            <?php echo form_open_multipart('portal/update_customer','class="form-horizontal" id="myform"');?>
                            
                            <?php 
                            if($this->session->flashdata('error_message')) {
				   			?>
				   				<br /><center><span class="error_flash"><?php echo $this->session->flashdata('error_message'); ?></span></center>
				   			<?php 
                            }
							?>
                             
                            <div class="row">
                                
                                <div class="col-sm-5 col-md-5 col-lg-5 ml-15">
                                		<input type="hidden" class="form-control" name='id' id='id' value="<?php echo $details[0]->customerid; ?>">
										<input type='hidden' name='password' id="password" value="<?php echo $details[0]->password; ?>" />

                                        <?php /* ?><div class="form-group">
                                            <label>Company Name</label>
                                            <input class="form-control" name='company' id='company' value="<?php echo $details[0]->companyname; ?>">
                                            <div class="text-right hidden" id="e-company"><span class="alert-danger">Required Field</span></div>
                                            <div class="text-right hidden" id="e-company-text-limit"><span class="alert-danger">Maximum 100 characters allowed!</span></div>
                                        </div><?php */ ?>
                                        
                                        <div class="form-group">
                                            <label>Company</label>                                       
                                             <?php  echo form_dropdown('fk_company_id', $company,$details[0]->fk_company_id,'class="form-control" id="fk_company_id"'); ?>
                                            <div class="text-right hidden" id="e-fk_company_id" ><span class="alert-danger">Required Field</span></div>
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
                                            <label>Status</label>                                       
                                            <select name="status" id="status" class="form-control">
                                                <option <?php if($details[0]->status==1) { ?>selected="selected"<?php } ?> value="1">Active</option>
                                                <option <?php if($details[0]->status==2) { ?>selected="selected"<?php } ?> value="2">In-Active</option>
                                                <option <?php if($details[0]->status==3) { ?>selected="selected"<?php } ?> value="3">Deleted</option>
                                            </select>
                                            <div class="text-right hidden" id="e-status" ><span class="alert-danger">Required Field</span></div>
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
                                            <div class="text-right hidden" id="e-contactno-patern" ><span class="alert-danger">Please enter valid phone number!</span></div>
                                        </div>
                                      <div class="form-group">
                                            <label>Account Manager</label>                                       
                                             <?php  echo form_dropdown('accountmanager', $accountmanager,$details[0]->accountmanagerid,'class="form-control" id="accountmanager"'); ?>
                                            <div class="text-right hidden" id="e-accountmanager" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                      
                                      <?php /* ?><div class="form-group">
                                            <label>Member:</label>              
                                            <?php  echo form_multiselect('members[]', $customer_users,$already_added_customer_users,'class="form-control" id="members"'); ?>
                                      </div><?php */ ?>
                                      
                                      <div class="form-group">
                                        <button type="button" class="btn btn-sm btn-primary" name="request" id="request">Reset Password</button> 
                                        </div>
                                    <div id="resetpwd" class="hidden" >
                                         
                                        <div class="form-group">
                                            <label>New Password</label>                                       
                                            <input type="password" name='newpassword' id="newpassword" class="form-control" value=""> 
                                             <div class="text-right hidden" id="e-newpassword" ><span class="alert-danger">Required Field</span></div>
                                             <div class="text-right hidden" id="e-newpassword-limit" ><span class="alert-danger">Password Must be between 6 to 30 Characters, with alphanumeric and special symbols combination</span></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Re-enter new Password</label>                                       
                                            <input type="password" name='repassword' id="repassword" class="form-control" value=""> 
                                             <div class="text-right hidden" id="e-repassword" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                         <div class="text-center hidden" id="e-correct" ><span class="alert-danger">New password and Re-entered password should be same</span></div>
										  <div class="text-center hidden" id="e-same" ><span class="alert-danger">old password and new password should not be same</span></div>

                                    </div>
                                 
                                       
                                       
                                     
                                    <div  class="form-group">
                                        <div class="pull-left"><button type="submit" name="submit" id="submit" class="btn btn-primary btn-sm btn-block">Submit</button></div>
                                        <div class="pull-left" style="margin-left: 10px;"><button type="reset" name="reset" id="reset" class="btn btn-primary btn-sm btn-block">Reset</button></div>
                                    </div>
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                                
                                <!-- /.col-lg-6 (nested) -->
                                <div class="col-sm-6 col-md-6 col-lg-6 ml-15">
                                	<div class="form-group">
										<label>Profile Picture</label> 
										<div class="ml-15">
											<input type="file" name='files' id="upload" class="form-group  ml-15"> 
										</div>
										<?php 
										$src = (isset($details[0]->profile_picture) && !empty($details[0]->profile_picture) && file_exists(FCPATH.$details[0]->profile_picture))?base_url().$details[0]->profile_picture:base_url()."user/user-default.png";
										?>
										<img data-toggle="modal" data-target="#myModal" src="<?php echo $src; ?>"  width="100" height="100">
										<input type='hidden' name='path' id="path" value="<?php echo base_url(); ?><?php echo $details[0]->profile_picture; ?>" />
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
          
			
		</section><!-- /wrapper -->
      </section><!-- /MAIN CONTENT -->

<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times</button>
          </div>
            <div align="center" class="modal-body center">
                <img class="img-responsive" src="" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

         <!-- /. PAGE WRAPPER  -->
     <!-- /. WRAPPER  -->
   
 <script type="text/javascript">
     $(document).ready(function () {
        
         $("#request").click(function(){
             var x = document.getElementById('resetpwd');
            x.classList.toggle("hidden");
        }); 
  
    });
$("#submit").click(function() {   
    var validate="no";

    /*if($.trim($('#company').val())==""){ $('#e-company').removeClass("hidden"); validate="yes";} else{ $('#e-company').addClass("hidden"); }
    if($('#company').val().length>100){
		$('#e-company-text-limit').removeClass("hidden"); validate="yes";
	}else{ 
		$('#e-company-text-limit').addClass("hidden");
	}*/

	if($.trim($('#fk_company_id').val())==""){ $('#e-fk_company_id').removeClass("hidden");validate="yes";}  else{ $('#e-fk_company_id').addClass("hidden");}
     
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

    //if($.trim($('#business_emailid').val())==""){ $('#e-business_emailid').removeClass("hidden");validate="yes";}  else{ $('#e-business_emailid').addClass("hidden");}
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

    if($.trim($('#accountmanager').val())==""){ $('#e-accountmanager').removeClass("hidden");validate="yes";}  else{ $('#e-accountmanager').addClass("hidden");}

    if(($.trim($('#newpassword').val())!="")||($.trim($('#repassword').val())!=""))
    { 
        if($.trim($('#newpassword').val())==""){ $('#e-newpassword').removeClass("hidden");validate="yes";}  else{ $('#e-newpassword').addClass("hidden");}
    if($.trim($('#repassword').val())==""){ $('#e-repassword').removeClass("hidden");validate="yes";}  else{ $('#e-repassword').addClass("hidden");}

	 // check if newpassword is entered, then check min 6 to 30 characters with combination of alphanumeric and special symbol combination
	    var filter = /^(?=.*?[a-z])(?=.*?[0-9])(?=.*?[^\w\s]).{6,30}$/;///^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[^\w\s]).{8,}$/;
	    if (!filter.test($.trim($('#newpassword').val()))) {
	    	$('#e-newpassword-limit').removeClass("hidden"); validate="yes";
	    }else{
	    	$('#e-newpassword-limit').addClass("hidden");
	    }
     
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
