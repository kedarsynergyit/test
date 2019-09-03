     <section id="main-content">
          <section class="wrapper site-min-height">
           <h3>
               <i class="fa fa-chevron-circle-right"></i> SETTINGS
                  </h3>
                  <p class="text-left"><a href="dashboard" ><span class="badge bg-theme">Back</span></a></p>
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
                                
                                <div class="col-lg-6 ml-15">
                                        <div class="form-group">
                                            <label>Userid</label>
                                            <input type="hidden" class="form-control" name='id' id='id' value="<?php  echo $details[0]->id; ?>">
                                            <input class="form-control" name='company' id='company' value="<?php echo $details[0]->username; ?>" <?php echo ($urole!=1)?'readonly="readonly"':""; ?>>
                                            <div class="text-right hidden" id="e-company"><span class="alert-danger">Required Field</span></div>
                                        </div>
                                      
                                        <div class="form-group">
                                            <label> First Name</label>
                                             <input class="form-control right" name='firstname' id='firstname' value="<?php echo $details[0]->first_name; ?>" <?php echo ($internal_user_external_user==2)?'readonly="readonly"':""; ?>>                                             
                                            <div class="text-right hidden" id="e-firstname" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                       <div class="form-group">
                                            <label> Last Name</label>
                                             <input class="form-control right" name='lastname' id='lastname' value="<?php echo $details[0]->last_name; ?>" <?php echo ($internal_user_external_user==2)?'readonly="readonly"':""; ?>>                                            
                                            <div class="text-right hidden" id="e-lastname" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Email ID</label>                                       
                                            <input type="text" name='emailid' id="emailid" class="form-control" value="<?php echo $details[0]->email; ?>" <?php echo ($internal_user_external_user==2)?'readonly="readonly"':""; ?>> 
                                            <div class="text-right hidden" id="e-emailid" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                    <div class="form-group">
                                            <label>Contact Number</label>                                       
                                            <input type="text" name='contactno' id="contactno" class="form-control" value="<?php echo $details[0]->phone; ?>" <?php echo ($internal_user_external_user==2)?'readonly="readonly"':""; ?>> 
                                            <div class="text-right hidden" id="e-contactno" ><span class="alert-danger">Required Field</span></div>
                                    </div>
                                      
                                    <div class="form-group">
                                        <span class="btn   badge bg-theme" name="request" id="request">Reset Password</span> 
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
                                        </div>
                                        <div class="form-group">
                                            <label>Re-enter new Password</label>                                       
                                            <input type="password" name='repassword' id="repassword" class="form-control" value=""> 
                                             <div class="text-right hidden" id="e-repassword" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                         <div class="text-center hidden" id="e-correct" ><span class="alert-danger">New password and Re-entered password should be same</span></div>
										  <div class="text-center hidden" id="e-same" ><span class="alert-danger">old password and new password should not be same</span></div>

                                    </div>
                                     
                                    <div  class="text-center">
                                        <button type="submit" name="submit" id="submit" class="btn btn-round btn-success btn-sm">Submit</button>
                                        <button type="reset" name="reset" id="reset"  class="btn btn-round btn-warning btn-sm">Reset</button>
                                    </div>
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                                <div class="col-lg-4 ml-15">
                                    <label>Profile Picture</label> 
                                      <div class="ml-15">
                                       <?php if($internal_user_external_user!=2){ ?>
                                       <input type="file" name='files' id="upload" class="form-group  ml-15">
                                       <?php } ?>
                                      </div>
                                      <img data-toggle="modal" data-target="#myModal" src="<?php echo base_url(); ?><?php echo $details[0]->userprofile; ?>"  width="100" height="100">
                                      <input type='hidden' name='path' id="path" value="<?php echo base_url(); ?><?php echo $details[0]->userprofile; ?>" />
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                               
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
    if($.trim($('#company').val())==""){ $('#e-company').removeClass("hidden"); validate="yes";} else{ $('#e-company').addClass("hidden"); } 
    if($.trim($('#firstname').val())==""){ $('#e-firstname').removeClass("hidden");validate="yes";}  else{ $('#e-firstname').addClass("hidden");} 
    if($.trim($('#lastname').val())==""){ $('#e-lastname').removeClass("hidden");validate="yes";}  else{ $('#e-lastname').addClass("hidden");} 
    if($.trim($('#emailid').val())==""){ $('#e-emailid').removeClass("hidden");validate="yes";}  else{ $('#e-emailid').addClass("hidden");}
      if($.trim($('#emailid').val())==""){ $('#e-emailid').removeClass("hidden");validate="yes";}  else{ $('#e-emailid').addClass("hidden");}
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
        
    }
    if(validate=='yes'){ return false; }else{  return true;
    $('#myform').submit();
    }
});   


</script>
         <!-- Custom Js -->
      
   
</body>
</html>
