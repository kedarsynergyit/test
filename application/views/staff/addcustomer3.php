     <section id="main-content">
          <section class="wrapper site-min-height">
           <h3>
               <i class="fa fa-chevron-circle-right"></i> Add Customer
                  </h3>
                  <p class="text-left"><a href="customers" ><span class="badge bg-theme">Back</span></a></p>
           <div id="page-inner"> 
              <div class="row">
                <div class="col-lg-12">
                       <div class="text-center">
                            <div id="infoSuccess"><?php echo $this->session->flashdata('success');?></div>
                             <div id="infoError"><?php echo $this->session->flashdata('failed');?></div>
                        </div>
                    <div class="panel panel-default">
                     
                       
                        <div class="panel-body">
                            <?php echo form_open_multipart('portal/insert_customer','class="form-horizontal" id="myform"');?> 
                            <div class="row">
                                
                                <div class="col-lg-6 ml-15">
                                        <div class="form-group">
                                            <label>Company Name</label>
                                            <input type="hidden" class="form-control" name='id' id='id' value="">
                                            <input class="form-control" name='company' id='company' value="">
                                            <div class="text-right hidden" id="e-company"><span class="alert-danger">Required Field</span></div>
                                        </div>
                                      
                                        <div class="form-group">
                                            <label>Contact First Name</label>
                                             <input class="form-control right" name='firstname' id='firstname' value="">                                             
                                            <div class="text-right hidden" id="e-firstname" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                       <div class="form-group">
                                            <label>Contact Last Name</label>
                                             <input class="form-control right" name='lastname' id='lastname' value="">                                            
                                            <div class="text-right hidden" id="e-lastname" ><span class="alert-danger">Required Field</span></div>
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
                                                                                  
                                    <div class="form-group">
                                            <label>Status</label>                                       
                                            <select name="status" id="status" class="form-control">
                                                <option selected="selected" value="1">Active</option>
                                                <option value="2">In-Active</option>
                                            </select>
                                            <div class="text-right hidden" id="e-status" ><span class="alert-danger">Required Field</span></div>
                                    </div>
                                        <div class="form-group">
                                            <label>Email ID</label>                                       
                                            <input type="text" name='emailid' id="emailid" class="form-control" value=""> 
                                            <div class="text-right hidden" id="e-emailid" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                    <div class="form-group">
                                            <label>Contact Number</label>                                       
                                            <input type="text" name='contactno' id="contactno" class="form-control" value=""> 
                                            <div class="text-right hidden" id="e-contactno" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                      <div class="form-group">
                                            <label>Account Manager</label>                                       
                                             <?php  echo form_dropdown('accountmanager', $accountmanager,'','class="form-control" id="accountmanager"'); ?>
                                            <div class="text-right hidden" id="e-accountmanager" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                    
                                 
                                       
                                       
                                     
                                    <div  class="text-center">
                                        <button type="submit" name="submit" id="submit" class="btn btn-round btn-success btn-sm">Submit</button>
                                        <button type="reset" name="reset" id="reset"  class="btn btn-round btn-warning btn-sm">Reset</button>
                                    </div>
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                                
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
    if($.trim($('#contactno').val())==""){ $('#e-contactno').removeClass("hidden");validate="yes";}  else{ $('#e-contactno').addClass("hidden");}
    if($.trim($('#newpassword').val())==""){ $('#e-newpassword').removeClass("hidden");validate="yes";}  else{ $('#e-newpassword').addClass("hidden");}
    if($.trim($('#repassword').val())==""){ $('#e-repassword').removeClass("hidden");validate="yes";}  else{ $('#e-repassword').addClass("hidden");}
      if($.trim($('#accountmanager').val())==""){ $('#e-accountmanager').removeClass("hidden");validate="yes";}  else{ $('#e-accountmanager').addClass("hidden");}

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
      
   
</body>
</html>
