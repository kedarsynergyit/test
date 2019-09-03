<?php
$sessionrole=$this->session->userdata('srole');
$internal_external_user=$this->session->userdata('internal_user_external_user'); 
?>
     <section id="main-content">
          <section class="wrapper site-min-height">
           <div class="pull-left"><h3><i class="fa fa-chevron-circle-right"></i> Add User</h3></div>
			<div class="pull-right"><h3><a class="btn btn-primary btn-sm btn-block" href="users">Back</a></h3></div>
           <div id="page-inner"> 
              <div class="row">
                <div class="col-lg-12">
                       <div class="text-center">
                            <div id="infoSuccess"><?php echo $this->session->flashdata('success');?></div>
                             <div id="infoError"><?php echo $this->session->flashdata('failed');?></div>
                        </div>
                    <div class="panel panel-default">
                     
                       
                        <div class="panel-body">
                            <?php echo form_open_multipart('portal/insert_user','class="form-horizontal" id="myform"');?> 
                            <div class="row">
                                
                                <div class="col-lg-6 ml-15">
                                          <div class="form-group">
                                            <label> User ID</label>
                                             <input class="form-control right" name='userid' id='userid' value="">                                             
                                            <div class="text-right hidden" id="e-userid" ><span class="alert-danger">Required Field</span></div>
                                            <div class="text-right hidden" id="e-userid-text-limit" ><span class="alert-danger">Maximum 50 characters allowed!</span></div>
                                            <div class="text-right hidden" id="e-userid-already-exists"><span class="alert-danger">User ID already exists!</span></div>
                                        </div>
                                      
                                        <div class="form-group">
                                            <label> First Name</label>
                                             <input class="form-control right" name='firstname' id='firstname' value="">                                             
                                            <div class="text-right hidden" id="e-firstname" ><span class="alert-danger">Required Field</span></div>
                                            <div class="text-right hidden" id="e-firstname-text-limit" ><span class="alert-danger">Maximum 20 characters allowed!</span></div>
                                        </div>
                                       <div class="form-group">
                                            <label> Last Name</label>
                                             <input class="form-control right" name='lastname' id='lastname' value="">                                            
                                            <div class="text-right hidden" id="e-lastname" ><span class="alert-danger">Required Field</span></div>
                                            <div class="text-right hidden" id="e-lastname-text-limit" ><span class="alert-danger">Maximum 20 characters allowed!</span></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Email ID</label>                                       
                                            <input type="text" name='emailid' id="emailid" class="form-control" value=""> 
                                            <div class="text-right hidden" id="e-emailid" ><span class="alert-danger">Required Field</span></div>
                                            <div class="text-right hidden" id="e-emailid-validate" ><span class="alert-danger">Please enter valid email</span></div>
                                        </div>
                                    <div class="form-group">
                                            <label>Contact Number</label>                                       
                                            <input type="text" name='contactno' id="contactno" class="form-control" value=""> 
                                            <div class="text-right hidden" id="e-contactno" ><span class="alert-danger">Required Field</span></div>
                                            <div class="text-right hidden" id="e-contactno-text-limit" ><span class="alert-danger">Maximum 20 characters allowed!</span></div>
                                            <div class="text-right hidden" id="e-contactno-patern" ><span class="alert-danger">Please enter valid phone number!</span></div>
                                    </div>
                                    <?php /*if($internal_external_user==1 && $sessionrole==2){ //don't show the Role to select ?>
                                    	<input type="hidden" name="role" id="role" value="2" />
                                    <?php }else{ // only visible to admin ?>
                                    <div class="form-group">
                                            <label>Role</label>                                       
                                            <select name="role" id="role" class="form-control">
                                                <option selected="selected" value="2">User</option>
                                                <option value="1">Admin</option>
                                            </select>
                                            <div class="text-right hidden" id="e-role" ><span class="alert-danger">Required Field</span></div>
                                    </div>
                                    <?php }*/ ?>
                                    
                                    <!-- there will be always user role 2 (user). -->
                                    <input type="hidden" name="role" id="role" value="2" />
                                    
                                     <div class="form-group">
                                            <label>Status</label>                                       
                                            <select name="status" id="status" class="form-control">
                                                <option selected="selected" value="1">Active</option>
                                                <option value="2">In-Active</option>
                                            </select>
                                            <div class="text-right hidden" id="e-status" ><span class="alert-danger">Required Field</span></div>
                                    </div>
                                    <?php if($internal_external_user==1 && $sessionrole==2){ //don't show the Internal External User type to select ?>
                                    	<input type="hidden" name="inexuser" id="inexuser" value="2" />
                                    <?php }else{ // only visible to admin ?>
                                           <div class="form-group">
                                            <label>Internal/External Users</label>                                       
                                            <select name="inexuser" id="inexuser" class="form-control">
                                                <option value="1">Internal</option>
                                                <option selected="selected" value="2">External</option>
                                            </select>
                                            <div class="text-right hidden" id="e-inexuser" ><span class="alert-danger">Required Field</span></div>
                                    </div>
                                    <?php } ?>
                                        <div class="form-group">
                                            <label> Password</label>                                       
                                            <input type="password" name='newpassword' id="newpassword" class="form-control" value=""> 
                                             <div class="text-right hidden" id="e-newpassword" ><span class="alert-danger">Required Field</span></div>
                                             <div class="text-right hidden" id="e-newpassword-limit" ><span class="alert-danger">password must be minimum 8 characters with one capital, one digit and one special character combination</span></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Re-enter Password</label>                                       
                                            <input type="password" name='repassword' id="repassword" class="form-control" value=""> 
                                             <div class="text-right hidden" id="e-repassword" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                         <div class="text-center hidden" id="e-correct" ><span class="alert-danger">Password and Re-entered password should be same</span></div>
										  <div class="text-center hidden" id="e-same" ><span class="alert-danger">old password and new password should not be same</span></div>

                                    <div class="form-group">
                                      <label>Profile Picture</label> 
                                      <div class="ml-15">
                                       <input type="file" name='files[]' id="upload" class="form-group  ml-15"> 
                                      </div>
                                    </div>
                                    <div class="form-group">
										<div class="pull-left"><button type="submit" name="submit" id="submit" class="btn btn-primary btn-sm btn-block">Submit</button></div>
										<div class="pull-left" style="margin-left: 10px;"><button type="reset" class="btn btn-primary btn-sm btn-block">Reset</button></div>
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

		// check for duplicate company name
		$("#userid").blur(function(){
 			var userid = $(this).val();

 			var form_data = {userid:userid}; 
 			$.ajax({
 				url: "<?php echo site_url('portal/checkduplicateuserid'); ?>",
 				type: 'POST',
 				data: form_data,
 				success: function(msg) {
 					if(msg==1){
 						$('#e-userid-already-exists').removeClass("hidden"); validate="yes";
 					}else{
 						$('#e-userid-already-exists').addClass("hidden");
 					}
 				}
 			});
 		});
 		
    });
$("#submit").click(function() {   
    var validate="no";

    if($.trim($('#userid').val())==""){ $('#e-userid').removeClass("hidden"); validate="yes";} else{ $('#e-userid').addClass("hidden"); }
    if($('#userid').val().length>50){
		$('#e-userid-text-limit').removeClass("hidden"); validate="yes";
	}else{ 
		$('#e-userid-text-limit').addClass("hidden");
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
    
    if($.trim($('#newpassword').val())==""){ $('#e-newpassword').removeClass("hidden");validate="yes";}  else{ $('#e-newpassword').addClass("hidden");}
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
    
    if($.trim($('#repassword').val())==""){ $('#e-repassword').removeClass("hidden");validate="yes";}  else{ $('#e-repassword').addClass("hidden");}
    if(($.trim($('#newpassword').val())!="")||($.trim($('#repassword').val())!=""))
    {
     
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
      
    <script>
 
 $('img').on('click', function () {
        var image = $(this).attr('src');
        $('#myModal').on('show.bs.modal', function () {
            $(".img-responsive").attr("src", image);
        });
    });
</script>