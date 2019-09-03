     <section id="main-content">
          <section class="wrapper site-min-height">
           <h3>
               <i class="fa fa-chevron-circle-right"></i> User Details
                  </h3>
                  <p class="text-left"><a href="users" ><span class="badge bg-theme">Back</span></a></p>
           <div id="page-inner"> 
              <div class="row">
                <div class="col-lg-12">
                       <div class="text-center">
                            <div id="infoSuccess"><?php echo $this->session->flashdata('success');?></div>
                             <div id="infoError"><?php echo $this->session->flashdata('failed');?></div>
                        </div>
                    <div class="panel panel-default">
                     
                        <div class="panel-heading">
                           <?php  echo $details[0]->employeeid; ?> (<?php  echo $details[0]->username; ?>  )
                        </div>
                        <div class="panel-body">
                            <?php echo form_open_multipart('portal/update_user','class="form-horizontal" id="myform"');?> 
                            <div class="row">
                                
                                <div class="col-sm-5 col-md-5 col-lg-5 ml-15">
                                       <input type='hidden' name='id' id="id" value="<?php echo $details[0]->id; ?>" />
                                      <input type='hidden' name='password' id="password" value="<?php echo $details[0]->password; ?>" />
                                      	
                                      	<div class="form-group">
                                            <label>Userid</label>
                                            <input class="form-control" name='username' id='username' value="<?php echo $details[0]->username; ?>">
                                            <div class="text-right hidden" id="e-username"><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label> First Name</label>
                                             <input class="form-control right" name='firstname' id='firstname' value="<?php echo $details[0]->first_name; ?>">                                             
                                            <div class="text-right hidden" id="e-firstname" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                       <div class="form-group">
                                            <label> Last Name</label>
                                             <input class="form-control right" name='lastname' id='lastname' value="<?php echo $details[0]->last_name; ?>">                                            
                                            <div class="text-right hidden" id="e-lastname" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Email ID</label>                                       
                                            <input type="text" name='emailid' id="emailid" class="form-control" value="<?php echo $details[0]->email; ?>"> 
                                            <div class="text-right hidden" id="e-emailid" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                    <div class="form-group">
                                            <label>Contact Number</label>                                       
                                            <input type="text" name='contactno' id="contactno" class="form-control" value="<?php echo $details[0]->phone; ?>"> 
                                            <div class="text-right hidden" id="e-contactno" ><span class="alert-danger">Required Field</span></div>
                                    </div>
                                       <div class="form-group">
                                            <label>Role</label>                                       
                                            <select name="role" id="role" class="form-control">
                                                <option <?php if($details[0]->role==2) { ?>selected="selected"<?php } ?> value="2">User</option>
                                                <option <?php if($details[0]->role==1) { ?>selected="selected"<?php } ?> value="1" >Admin</option>
                                            </select>
                                            <div class="text-right hidden" id="e-role" ><span class="alert-danger">Required Field</span></div>
                                    </div>
                                     <div class="form-group">
                                            <label>Status</label>                                       
                                            <select name="status" id="status" class="form-control">
                                                <option <?php if($details[0]->status==1) { ?>selected="selected"<?php } ?> value="1">Active</option>
                                                <option <?php if($details[0]->status==2) { ?>selected="selected"<?php } ?> value="2">In-Active</option>
                                            </select>
                                            <div class="text-right hidden" id="e-status" ><span class="alert-danger">Required Field</span></div>
                                    </div>
                                           <div class="form-group">
                                            <label>Internal/External Users</label>                                       
                                            <select name="inexuser" id="inexuser" class="form-control">
                                                <option <?php if($details[0]->internal_user_external_user==1) { ?>selected="selected"<?php } ?> value="1">Internal</option>
                                                <option <?php if($details[0]->internal_user_external_user==2) { ?>selected="selected"<?php } ?>value="2">External</option>
                                            </select>
                                            <div class="text-right hidden" id="e-inexuser" ><span class="alert-danger">Required Field</span></div>
                                    </div>
                                    <div class="form-group">
                                        <span class="btn   badge bg-theme" name="request" id="request">Reset Password</span> 
                                        </div>
                                     <div id="resetpwd" class="hidden" >
                                         
                                        <div class="form-group">
                                            <label>New Password</label>                                       
                                            <input type="password" name='newpassword' id="newpassword" class="form-control" value=""> 
                                             <div class="text-right hidden" id="e-newpassword" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Re-enter new Password</label>                                       
                                            <input type="password" name='repassword' id="repassword" class="form-control" value=""> 
                                             <div class="text-right hidden" id="e-repassword" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                         <div class="text-center hidden" id="e-correct" ><span class="alert-danger">New password and Re-entered password should be same</span></div>
										  <div class="text-center hidden" id="e-same" ><span class="alert-danger">old password and new password should not be same</span></div>

                                    </div>
                                     
                            
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                                <div class="col-sm-6 col-md-6 col-lg-6 ml-15">
                                	<div class="form-group">
										<label>Profile Picture</label> 
										<div class="ml-15">
											<input type="file" name='files' id="upload" class="form-group  ml-15"> 
										</div>
										<img data-toggle="modal" data-target="#myModal" src="<?php echo base_url(); ?><?php echo $details[0]->userprofile; ?>"  width="100" height="100">
										<input type='hidden' name='path' id="path" value="<?php echo base_url(); ?><?php echo $details[0]->userprofile; ?>" />
                                	</div>
                                	
                                	<!-- Contracts -->
                                	<div class="form-group">
                                		<label>Contracts</label>
                                		<div class="clearfix"></div>
                                		<?php 
                                			if(isset($contracts_files) && !empty($contracts_files)){
                                				foreach ($contracts_files as $eachFile){
                                					?>
                                					<p>
                                						<a href="<?php echo base_url(); ?><?php echo $eachFile->file; ?>" target="_blank"><?php echo (!empty($eachFile->file_name))?$eachFile->file_name:"View File"; ?></a>
                                					</p>
                                					<?php
                                				}
                                			} ?>
									</div>
									<div class="clearfix"></div>
									
									<!-- Waivers -->
                                	<div class="form-group">
                                		<label>Waivers</label>
                                		<div class="clearfix"></div>
                                		<?php 
                                			if(isset($waivers_files) && !empty($waivers_files)){
                                				foreach ($waivers_files as $eachFile){
                                					?>
                                					<p>
                                						<a href="<?php echo base_url(); ?><?php echo $eachFile->file; ?>" target="_blank"><?php echo (!empty($eachFile->file_name))?$eachFile->file_name:"View File"; ?></a>
                                					</p>
                                					<?php
                                				}
                                			} ?>
									</div>
									<div class="clearfix"></div>
									
									<!-- Insurance -->
                                	<div class="form-group">
                                		<label>Insurance</label>
                                		<div class="clearfix"></div>
                                		<?php 
                                			if(isset($insurance_files) && !empty($insurance_files)){
                                				foreach ($insurance_files as $eachFile){
                                					?>
                                					<p>
                                						<a href="<?php echo base_url(); ?><?php echo $eachFile->file; ?>" target="_blank"><?php echo (!empty($eachFile->file_name))?$eachFile->file_name:"View File"; ?></a>
                                					</p>
                                					<?php
                                				}
                                			} ?>
									</div>
									<div class="clearfix"></div>
									
									<!-- Certifications -->
                                	<div class="form-group">
                                		<label>Certifications</label>
                                		<div class="clearfix"></div>
                                		<?php 
                                			if(isset($certifications_files) && !empty($certifications_files)){
                                				foreach ($certifications_files as $eachFile){
                                					?>
                                					<p>
                                						<a href="<?php echo base_url(); ?><?php echo $eachFile->file; ?>" target="_blank"><?php echo (!empty($eachFile->file_name))?$eachFile->file_name:"View File"; ?></a>
                                					</p>
                                					<?php
                                				}
                                			} ?>
									</div>
									<div class="clearfix"></div>
                                	
                                	<!-- Other -->
                                	<div class="form-group">
                                		<label>Other</label>
                                		<div class="clearfix"></div>
                                		<?php 
                                			if(isset($other_files) && !empty($other_files)){
                                				foreach ($other_files as $eachFile){
                                					?>
                                					<p>
                                						<a href="<?php echo base_url(); ?><?php echo $eachFile->file; ?>" target="_blank"><?php echo (!empty($eachFile->file_name))?$eachFile->file_name:"View File"; ?></a>
                                					</p>
                                					<?php
                                				}
                                			} ?>
									</div>
									<div class="clearfix"></div>
                                	
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                               
                            </div>
                            <!-- /.row (nested) -->
                                    <div  class="text-center">
                                        <button type="submit" name="submit" id="submit" class="btn btn-round btn-success btn-sm">Submit</button>
                                        <button type="reset" name="reset" id="reset"  class="btn btn-round btn-warning btn-sm">Cancel</button>
                                    </div>
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
    <!-- JS Scripts-->
       <script src="<?php echo base_url(); ?>assets/js/jquery.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery-1.8.3.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
     <!-- DATA TABLE SCRIPTS -->
    <script src="<?php echo base_url(); ?>assets/js/dataTables/jquery.dataTables.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/dataTables/dataTables.bootstrap.js"></script>
 <script type="text/javascript">
     $(document).ready(function () {
        
         $("#request").click(function(){
             var x = document.getElementById('resetpwd');
            x.classList.toggle("hidden");
        }); 
  
    });
$("#submit").click(function() {   
    var validate="no";
    if($.trim($('#username').val())==""){ $('#e-username').removeClass("hidden");validate="yes";}  else{ $('#e-username').addClass("hidden");}
    if($.trim($('#firstname').val())==""){ $('#e-firstname').removeClass("hidden");validate="yes";}  else{ $('#e-firstname').addClass("hidden");} 
    if($.trim($('#lastname').val())==""){ $('#e-lastname').removeClass("hidden");validate="yes";}  else{ $('#e-lastname').addClass("hidden");} 
    if($.trim($('#emailid').val())==""){ $('#e-emailid').removeClass("hidden");validate="yes";}  else{ $('#e-emailid').addClass("hidden");}
      if($.trim($('#contactno').val())==""){ $('#e-contactno').removeClass("hidden");validate="yes";}  else{ $('#e-contactno').addClass("hidden");}
    if(($.trim($('#newpassword').val())!="")||($.trim($('#repassword').val())!=""))
    {
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
$("#reset").click(function() { 
$(location).attr('href', 'users');
});

</script>
         <!-- Custom Js -->
      
    <script>
 
 $('img').on('click', function () {
        var image = $(this).attr('src');
        $('#myModal').on('show.bs.modal', function () {
            $(".img-responsive").attr("src", image);
        });
    });
</script>