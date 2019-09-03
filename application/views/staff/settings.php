     <section id="main-content">
          <section class="wrapper site-min-height">
			<div class="pull-left"><h3><?php /* ?><i class="fa fa-chevron-circle-right"></i><?php */ ?> SETTINGS</h3></div>
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
                            <?php echo form_open_multipart('portal/updatesettings','class="form-horizontal" id="myform"');?> 
                            <div class="row">
                                
                                <div class="col-sm-5 col-md-5 col-lg-5 ml-15">
                                        <?php /* ?><div class="form-group">
                                            <label>Userid</label>
                                            <input type="hidden" class="form-control" name='id' id='id' value="<?php  echo $details[0]->id; ?>">
                                            <input class="form-control" name='company' id='company' value="<?php echo $details[0]->username; ?>" <?php echo ($urole!=1)?'readonly="readonly"':""; ?>>
                                            <div class="text-right hidden" id="e-company"><span class="alert-danger">Required Field</span></div>
                                            <div class="text-right hidden" id="e-company-text-limit" ><span class="alert-danger">Maximum 50 characters allowed!</span></div>
                                        </div><?php */ ?>
                                      
                                        <div class="form-group">
                                            <label> First Name</label>
                                             <input class="form-control right" name='firstname' id='firstname' value="<?php echo $details[0]->first_name; ?>" <?php echo ($internal_user_external_user==2)?'readonly="readonly"':""; ?>>                                             
                                            <div class="text-right hidden" id="e-firstname" ><span class="alert-danger">Required Field</span></div>
                                            <div class="text-right hidden" id="e-firstname-text-limit" ><span class="alert-danger">Maximum 20 characters allowed!</span></div>
                                        </div>
                                       <div class="form-group">
                                            <label> Last Name</label>
                                             <input class="form-control right" name='lastname' id='lastname' value="<?php echo $details[0]->last_name; ?>" <?php echo ($internal_user_external_user==2)?'readonly="readonly"':""; ?>>                                            
                                            <div class="text-right hidden" id="e-lastname" ><span class="alert-danger">Required Field</span></div>
                                            <div class="text-right hidden" id="e-lastname-text-limit" ><span class="alert-danger">Maximum 20 characters allowed!</span></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Email ID</label>                                       
                                            <input type="text" name='emailid' id="emailid" class="form-control" value="<?php echo $details[0]->email; ?>" <?php echo ($internal_user_external_user==2)?'readonly="readonly"':""; ?>> 
                                            <div class="text-right hidden" id="e-emailid" ><span class="alert-danger">Required Field</span></div>
                                            <div class="text-right hidden" id="e-emailid-validate" ><span class="alert-danger">Please enter valid email</span></div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Business Email ID</label>                                       
                                            <input type="text" name='business_emailid' id="business_emailid" class="form-control" value="<?php echo $details[0]->business_email; ?>" <?php echo ($internal_user_external_user==2)?'readonly="readonly"':""; ?>> 
                                            <div class="text-right hidden" id="e-business_emailid" ><span class="alert-danger">Required Field</span></div>
                                            <div class="text-right hidden" id="e-business_emailid-validate" ><span class="alert-danger">Please enter valid email</span></div>
                                        </div>
                                        
                                        
                                    <div class="form-group">
                                            <label>Contact Number</label>                                       
                                            <input type="text" name='contactno' id="contactno" class="form-control" value="<?php echo $details[0]->phone; ?>" <?php echo ($internal_user_external_user==2)?'readonly="readonly"':""; ?>> 
                                            <div class="text-right hidden" id="e-contactno" ><span class="alert-danger">Required Field</span></div>
                                            <div class="text-right hidden" id="e-contactno-text-limit" ><span class="alert-danger">Maximum 20 characters allowed!</span></div>
                                            <div class="text-right hidden" id="e-contactno-patern" ><span class="alert-danger">Please enter valid phone number!</span></div>
                                    </div>
                                      
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="button" name="request" id="request">Reset Password</button> 
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
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                                
                                <div class="col-sm-6 col-md-6 col-lg-6 ml-15">
                                	<div class="row">
                                		<div class="col-sm-3 col-md-3 col-lg-3 ml-15">
                                			<!-- Profile photo -->
											<div class="form-group">
												<label>Profile Picture</label> 
												<div class="ml-15">
													<?php //if($internal_user_external_user!=2){ ?>
														<input type="file" name='files' id="upload" class="form-group  ml-15">
													<?php //} ?> 
												</div>
												
												<?php 
												$src = (isset($details[0]->userprofile) && !empty($details[0]->userprofile) && file_exists(FCPATH.$details[0]->userprofile))?base_url().$details[0]->userprofile:base_url()."user/user-default.png";
												?>
												
												<img data-toggle="modal" data-target="#myModal" src="<?php echo $src; ?>" style="width: 100px; height:100px; object-fit: contain;">
												<input type='hidden' name='path' id="path" value="<?php echo base_url(); ?><?php echo $details[0]->userprofile; ?>" />
											</div>
                                		</div>
                                		<div class="col-sm-6 col-md-6 col-lg-6 ml-15" style="display:none;">
                                			<!-- Skills -->
		                                    <div class="form-group">
			                                	<label>Skills</label>
			                                	<textarea name='skills' id="skills" class="form-control"><?php echo $details[0]->skills; ?></textarea>
		                                	</div>
                                		</div>
                                	</div>
                                	
                                	<?php if($urole!=1){ ?>
                                	<!-- Files -->
                                	<div class="form-group">
                                		<label>Upload Files</label>
                                		
                                		<div class="clearfix"></div>
                                		<?php 
                                			if(isset($contracts_files) && !empty($contracts_files)){
                                				foreach ($contracts_files as $eachFile){
                                					?>
                                					<p id="user_contracts_file_<?php echo $eachFile->id; ?>">
                                						<?php if($internal_user_external_user!=2){ ?><a class="btn btn-primary btn-xs" onclick="removeExistingFile('<?php echo $eachFile->id; ?>','user_contracts');"><i class="fa fa-trash-o "></i></a><?php } ?>
                                						<a href="<?php echo base_url(); ?><?php echo $eachFile->file; ?>" target="_blank"><?php echo (!empty($eachFile->file_name))?$eachFile->file_name:"View File"; ?></a>
                                					</p>
                                					<?php
                                				}
                                		?>
                                			
                                		<?php } ?>
                                		
                                		<div class="clearfix"></div>
                                		<div class="row" id="files_contracts_add1">
                                			<div class="col-lg-6">
	                                			<input type="text" placeholder="File Name" class="form-control" name="files_contracts_name[]" maxlength="255">
	                                		</div>
	                                		<div class="col-lg-6">
												<input type="file" name='files_contracts[]' class="pull-left">
												<a class="btn btn-danger btn-xs" onclick="removefile('contracts','1');"><i class="fa fa-trash-o "></i></a> 
	                                		</div>
                                		</div>
                                		
                                		<div id="add_files_contracts">                                            
                                        </div>
                                		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<input type="hidden" name="num_contracts" id="num_contracts" value="1" >
										<button type="button" class="btn btn-primary btn-xs" onclick="addMoreFiles('contracts')">Add More Files</button>
									</div>
                                	
                                	<!-- Contracts -->
                                	<?php /* ?><div class="form-group">
                                		<label>Contracts</label>
                                		
                                		<div class="clearfix"></div>
                                		<?php 
                                			if(isset($contracts_files) && !empty($contracts_files)){
                                				foreach ($contracts_files as $eachFile){
                                					?>
                                					<p id="user_contracts_file_<?php echo $eachFile->id; ?>">
                                						<?php if($internal_user_external_user!=2){ ?><a class="btn btn-primary btn-xs" onclick="removeExistingFile('<?php echo $eachFile->id; ?>','user_contracts');"><i class="fa fa-trash-o "></i></a><?php } ?>
                                						<a href="<?php echo base_url(); ?><?php echo $eachFile->file; ?>" target="_blank"><?php echo (!empty($eachFile->file_name))?$eachFile->file_name:"View File"; ?></a>
                                					</p>
                                					<?php
                                				}
                                		?>
                                			
                                		<?php } ?>
                                		
                                		<div class="clearfix"></div>
                                		<div class="row" id="files_contracts_add1">
                                			<div class="col-lg-6">
	                                			<input type="text" placeholder="File Name" class="form-control" name="files_contracts_name[]" maxlength="255">
	                                		</div>
	                                		<div class="col-lg-6">
												<input type="file" name='files_contracts[]' id="upload" class="pull-left">
												<a class="btn btn-danger btn-xs" onclick="removefile('contracts','1');"><i class="fa fa-trash-o "></i></a> 
	                                		</div>
                                		</div>
                                		
                                		<div id="add_files_contracts">                                            
                                        </div>
                                		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<input type="hidden" name="num_contracts" id="num_contracts" value="1" >
										<button type="button" class="btn btn-primary btn-xs" onclick="addMoreFiles('contracts')">Add More Contracts</button>
									</div>
									
									<!-- waivers -->
									<div class="clearfix"></div>
									<div style="height:50px">&nbsp;</div>
									<div class="clearfix"></div>
                                	<div class="form-group">
                                		<label>Waivers</label>
                                		
                                		<div class="clearfix"></div>
                                		<?php 
                                			if(isset($waivers_files) && !empty($waivers_files)){
                                				foreach ($waivers_files as $eachFile){
                                					?>
                                					<p id="user_waivers_file_<?php echo $eachFile->id; ?>">
                                						<?php if($internal_user_external_user!=2){ ?><a class="btn btn-primary btn-xs" onclick="removeExistingFile('<?php echo $eachFile->id; ?>','user_waivers');"><i class="fa fa-trash-o "></i></a><?php } ?>
                                						<a href="<?php echo base_url(); ?><?php echo $eachFile->file; ?>" target="_blank"><?php echo (!empty($eachFile->file_name))?$eachFile->file_name:"View File"; ?></a>
                                					</p>
                                					<?php
                                				}
                                		?>
                                			
                                		<?php } ?>
                                		
                                		<div class="clearfix"></div>
                                		<div class="row" id="files_waivers_add1">
                                			<div class="col-lg-6">
	                                			<input type="text" placeholder="File Name" class="form-control" name="files_waivers_name[]" maxlength="255">
	                                		</div>
	                                		<div class="col-lg-6">
												<input type="file" name='files_waivers[]' id="upload" class="pull-left">
												<a class="btn btn-danger btn-xs" onclick="removefile('waivers','1');"><i class="fa fa-trash-o "></i></a> 
	                                		</div>
                                		</div>
                                		
                                		<div id="add_files_waivers">                                            
                                        </div>
                                		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<input type="hidden" name="num_waivers" id="num_waivers" value="1" >
										<button type="button" class="btn btn-primary btn-xs" onclick="addMoreFiles('waivers')">Add More Waivers</button>
									</div>
									
									<!-- insurance -->
									<div class="clearfix"></div>
									<div style="height:50px">&nbsp;</div>
									<div class="clearfix"></div>
                                	<div class="form-group">
                                		<label>Insurance</label>
                                		
                                		<div class="clearfix"></div>
                                		<?php 
                                			if(isset($insurance_files) && !empty($insurance_files)){
                                				foreach ($insurance_files as $eachFile){
                                					?>
                                					<p id="user_insurance_file_<?php echo $eachFile->id; ?>">
                                						<?php if($internal_user_external_user!=2){ ?><a class="btn btn-primary btn-xs" onclick="removeExistingFile('<?php echo $eachFile->id; ?>','user_insurance');"><i class="fa fa-trash-o "></i></a><?php } ?>
                                						<a href="<?php echo base_url(); ?><?php echo $eachFile->file; ?>" target="_blank"><?php echo (!empty($eachFile->file_name))?$eachFile->file_name:"View File"; ?></a>
                                					</p>
                                					<?php
                                				}
                                		?>
                                			
                                		<?php } ?>
                                		
                                		<div class="clearfix"></div>
                                		<div class="row" id="files_insurance_add1">
                                			<div class="col-lg-6">
	                                			<input type="text" placeholder="File Name" class="form-control" name="files_insurance_name[]" maxlength="255">
	                                		</div>
	                                		<div class="col-lg-6">
												<input type="file" name='files_insurance[]' id="upload" class="pull-left">
												<a class="btn btn-danger btn-xs" onclick="removefile('insurance','1');"><i class="fa fa-trash-o "></i></a> 
	                                		</div>
                                		</div>
                                		
                                		<div id="add_files_insurance">                                            
                                        </div>
                                		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<input type="hidden" name="num_insurance" id="num_insurance" value="1" >
										<button type="button" class="btn btn-primary btn-xs" onclick="addMoreFiles('insurance')">Add More Insurance</button>
									</div>
									
									<!-- certifications -->
									<div class="clearfix"></div>
									<div style="height:50px">&nbsp;</div>
									<div class="clearfix"></div>
                                	<div class="form-group">
                                		<label>Certifications</label>
                                		
                                		<div class="clearfix"></div>
                                		<?php 
                                			if(isset($certifications_files) && !empty($certifications_files)){
                                				foreach ($certifications_files as $eachFile){
                                					?>
                                					<p id="user_certifications_file_<?php echo $eachFile->id; ?>">
                                						<?php if($internal_user_external_user!=2){ ?><a class="btn btn-primary btn-xs" onclick="removeExistingFile('<?php echo $eachFile->id; ?>','user_certifications');"><i class="fa fa-trash-o "></i></a><?php } ?>
                                						<a href="<?php echo base_url(); ?><?php echo $eachFile->file; ?>" target="_blank"><?php echo (!empty($eachFile->file_name))?$eachFile->file_name:"View File"; ?></a>
                                					</p>
                                					<?php
                                				}
                                		?>
                                			
                                		<?php } ?>
                                		
                                		<div class="clearfix"></div>
                                		<div class="row" id="files_certifications_add1">
                                			<div class="col-lg-6">
	                                			<input type="text" placeholder="File Name" class="form-control" name="files_certifications_name[]" maxlength="255">
	                                		</div>
	                                		<div class="col-lg-6">
												<input type="file" name='files_certifications[]' id="upload" class="pull-left">
												<a class="btn btn-danger btn-xs" onclick="removefile('certifications','1');"><i class="fa fa-trash-o "></i></a> 
	                                		</div>
                                		</div>
                                		
                                		<div id="add_files_certifications">                                            
                                        </div>
                                		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<input type="hidden" name="num_certifications" id="num_certifications" value="1" >
										<button type="button" class="btn btn-primary btn-xs" onclick="addMoreFiles('certifications')">Add More Certifications</button>
									</div>
									
									<!-- other -->
									<div class="clearfix"></div>
									<div style="height:50px">&nbsp;</div>
									<div class="clearfix"></div>
                                	<div class="form-group">
                                		<label>Other</label>
                                		
                                		<div class="clearfix"></div>
                                		<?php 
                                			if(isset($other_files) && !empty($other_files)){
                                				foreach ($other_files as $eachFile){
                                					?>
                                					<p id="user_other_file_<?php echo $eachFile->id; ?>">
                                						<?php if($internal_user_external_user!=2){ ?><a class="btn btn-primary btn-xs" onclick="removeExistingFile('<?php echo $eachFile->id; ?>','user_other');"><i class="fa fa-trash-o "></i></a><?php } ?>
                                						<a href="<?php echo base_url(); ?><?php echo $eachFile->file; ?>" target="_blank"><?php echo (!empty($eachFile->file_name))?$eachFile->file_name:"View File"; ?></a>
                                					</p>
                                					<?php
                                				}
                                		?>
                                			
                                		<?php } ?>
                                		
                                		<div class="clearfix"></div>
                                		<div class="row" id="files_other_add1">
                                			<div class="col-lg-6">
	                                			<input type="text" placeholder="File Name" class="form-control" name="files_other_name[]" maxlength="255">
	                                		</div>
	                                		<div class="col-lg-6">
												<input type="file" name='files_other[]' id="upload" class="pull-left">
												<a class="btn btn-danger btn-xs" onclick="removefile('other','1');"><i class="fa fa-trash-o "></i></a> 
	                                		</div>
                                		</div>
                                		
                                		<div id="add_files_other">                                            
                                        </div>
                                		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<input type="hidden" name="num_other" id="num_other" value="1" >
										<button type="button" class="btn btn-primary btn-xs" onclick="addMoreFiles('other')">Add More Other</button>
									</div><?php */ ?>
									<?php } ?>
									
                                </div>
                            </div>
                            <!-- /.row (nested) -->
                            
                            <div class="row">
                            	<div class="col-sm-5 col-md-5 col-lg-5 ml-15">
                            		<div class="form-group">
                                        <div class="pull-left"><button type="submit" name="submit" id="submit" class="btn btn-block btn-primary btn-sm">Submit</button></div>
                                        <div class="pull-left" style="margin-left: 10px;"><button type="reset" name="reset" id="reset"  class="btn btn-block btn-primary btn-sm">Reset</button></div>
                                    </div>
								</div>
                            </div>
                            
                            <?php echo form_close(); ?>
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
    <!-- JS Scripts-->
       
 <script type="text/javascript">
     $(document).ready(function () {
        
         $("#request").click(function(){
             var x = document.getElementById('resetpwd');
            x.classList.toggle("hidden");

            $("#password").focus();
        }); 
  
    });

function addMoreFiles(files_for){
    var addnum=$('#num_'+files_for).val();
    var adnum=parseInt(addnum)+parseInt(1);
    $('#num_'+files_for).val(adnum);
    $("#add_files_"+files_for).append('<div class="row" id="files_'+files_for+'_add'+adnum+'"><div class="col-lg-6"><input type="text" placeholder="File Name" class="form-control" name="files_'+files_for+'_name[]" maxlength="255"></div><div class="col-lg-6"><input type="file" name="files_'+files_for+'[]" class="pull-left"><a class="btn btn-danger btn-xs" onclick="removefile(\''+files_for+'\',\''+adnum+'\');"><i class="fa fa-trash-o "></i></a></div></div>');
}

function removefile(id,num){
	if(confirm("Are you sure you want to remove this file?")){
		$('div[id="files_'+id+'_add'+num+'"]').remove();

		// also minus the num for contracts
		$("#num_"+id).val(parseInt($("#num_"+id).val())-1);
		
		
	}
}

$("#submit").click(function() {   
    var validate="no";
    /*if($.trim($('#company').val())==""){ $('#e-company').removeClass("hidden"); validate="yes";} else{ $('#e-company').addClass("hidden"); }
    if($('#company').val().length>50){
		$('#e-company-text-limit').removeClass("hidden"); validate="yes";
	}else{ 
		$('#e-company-text-limit').addClass("hidden");
	}*/
     
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

    /*if($.trim($('#business_emailid').val())==""){ $('#e-business_emailid').removeClass("hidden");validate="yes";}  else{ $('#e-business_emailid').addClass("hidden");}
    if($.trim($('#business_emailid').val())!=""){
    	var filter = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
	    if (!filter.test($.trim($('#business_emailid').val()))) {
	    	$('#e-business_emailid-validate').removeClass("hidden"); validate="yes";
	    }else{
	    	$('#e-business_emailid-validate').addClass("hidden");
	    }
    }*/

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

         if($.trim($('#newpassword').val())!=""){
			// check if newpassword is entered, then check min 6 to 30 characters with combination of alphanumeric and special symbol combination
			//var filter = /^(?=.*?[a-z])(?=.*?[0-9])(?=.*?[^\w\s]).{6,30}$/;

			// password must be minimum 8 characters with one capital, one digit and one special character combination
        	var filter = /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[^\w\s]).{8,}$/;
        	
			if (!filter.test($.trim($('#newpassword').val()))) {
				$('#e-newpassword-limit').removeClass("hidden"); validate="yes";
			}else{
				$('#e-newpassword-limit').addClass("hidden");
			}
         }
        
    }
    if(validate=='yes'){ return false; }else{  
    	$('#myform').submit();
    }
});

function removeExistingFile(id,from_table){

	if(confirm("Are you sure you want to remove this file?")){
		// add loader to show process
	    add_loader();
	    
		var form_data = {id:id,from_table:from_table};
		 
		$.ajax({
	        url: "<?php echo site_url('portal/removeExistingUserFile'); ?>",
	        async: true,
	        type: 'POST',
	        data: form_data,
	        success: function(msg) {
	            alert(msg);
	         	// hide loader now
	      		remove_loader();

	      		// now remove that <p>tag
	      		$("#"+from_table+"_file_"+id).remove();
	        }

	    });
	}
}
</script>
         <!-- Custom Js -->
      
   
</body>
</html>
