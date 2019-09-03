     <section id="main-content">
          <section class="wrapper site-min-height">
           <div class="pull-left"><h3><?php /* ?><i class="fa fa-chevron-circle-right"></i><?php */ ?> Add Ticket</h3></div>
			<div class="pull-right"><h3><a class="btn btn-primary btn-sm btn-block" href="javascript:history.back();">Back</a></h3></div>
           <div id="page-inner"> 
              <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Add Ticket
                        </div>
                        <div class="panel-body">
                            <?php echo form_open_multipart('portal/insert_task','class="form-horizontal" id="myform"');?> 
                            <div class="row">
                                
                                <div class="col-lg-6 ml-15">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <?php /* ?><input type="hidden" class="form-control" name='projectid' id='projectid' value="<?php echo $this->input->get('id'); ?>">
                                            <input type="hidden" class="form-control" name='customerid' id='customerid' value="<?php echo $customerid; ?>">
											<input type="hidden" class="form-control" name='accountmanager' id='accountmanager' value="<?php echo $accountmanager; ?>">
											<input type="hidden" class="form-control" name='projectmanager' id='projectmanager' value="<?php echo $projectmanager; ?>"><?php */ ?>
                                            <input class="form-control" name='title' id='title'>
                                            <div class="text-right hidden" id="e-title"><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Category</label>
                                            <?php echo form_dropdown('fk_category_id', $categories,'',' id="fk_category_id" class="form-control" '); ?>
                                            <div class="text-right hidden" id="e-fk_category_id" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Project</label>
                                            <?php echo form_dropdown('projectid', $project,$get_project_id,' id="projectid"  class="form-control" onChange="updateTechnician(this);" '); ?>
                                            <div class="text-right hidden" id="e-projectid" ><span class="alert-danger">Required Field</span></div>
                                        </div>                                      
                                        <div class="form-group">
                                            <label>Priority</label>
                                            <?php echo form_dropdown('priority', $priority,'',' id="priority"  class="form-control " '); ?>
                                            <div class="text-right hidden" id="e-priority" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Expected To Start </label>
                                            <input type="text" name="dob"  class="datefieldwidth" id="dob" placeholder="Date" value="<?php echo date("m/d/Y"); ?>">&nbsp;<i class="fa fa-calendar" id="dobi"></i>            
                                            <div class="text-right hidden" id="e-priority" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        <div class="form-group">
                                            <label >Expected To Complete</label>
                                            <input type="text" name="doi" class="datefieldwidth" id="doi" placeholder="Date" value="<?php echo date("m/d/Y"); ?>">&nbsp;<i class="fa fa-calendar" id="doii"></i>              
                                            <div class="text-right hidden" id="e-priority" ><span class="alert-danger">Required Field</span></div>              
                                        </div>
                                        <div class="form-group">
                                            <label >Assign Techs :</label>              
                                            <div id="technician_dropdown_div"><?php  echo form_dropdown('developer', $developer,'','class="form-control" id="developer"'); ?></div>
                                            <div class="text-right hidden" id="e-priority" ><span class="alert-danger">Required Field</span></div>
                                        </div>         
                                        <div class="form-group">
                                            <label >Status:</label>      
                                            <?php  echo form_dropdown('status', $status,'','class="form-control" id="statuss"'); ?>
                                           <div class="text-right hidden" id="e-status" ><span class="alert-danger">Required Field</span></div>                          
                                        </div> 
										<?php if($internal_user_external_user==1){ ?>
                                        <div class="form-group">
                                            <label>File input</label>
											<div class="ml-15" id="fileadd0">
											<input type="file" name='files[]' id="upload" class="pull-left">
	                                            <button class="btn btn-danger btn-xs" onclick="removefile(0)"><i class="fa fa-trash-o "></i></button> 
	                                        </div>
                                        </div>
                                        
										
                                        <div class="ml-15" id="addfiles">                                            
                                        </div>
                                    
                                        <div class="form-group">  
                                            <input type="hidden" name="num" id="num" value="1" >
                                            <button type="button" class="btn btn-primary btn-xs" onclick="addMoreFiles()">Add More</button>
                                        </div>
										<?php } ?>
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea class="form-control" rows="3" name="desc" id="desc"></textarea>
                                            <div class="text-right hidden" id="e-desc"><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Show Customers</label><br/>
                                            <input type="radio" name="cust_show" id="cust_show" value="0"  checked="checked">Show &nbsp;
                                            <input type="radio" name="cust_show" id="cust_show" value="1" >Don't Show
                                            <div class="text-right hidden" id="e-desc"><span class="alert-danger">Required Field</span></div>
                                        </div>                                     
                                       <div class="form-group">
                                            <div class="pull-left"><button type="submit" name="submit" id="submit" class="btn btn-primary btn-sm btn-block">Submit</button></div>
                                            <div class="pull-left" style="margin-left: 10px;"><button type="reset" class="btn btn-primary btn-sm btn-block">Reset</button></div>
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

			
        <script>
        $(document).ready(function () {
            
            $("#title").focus();
        });
        
function addMoreFiles(){
    var addnum=$('#num').val();
    var adnum=parseInt(addnum)+parseInt(1);
    $('#num').val(adnum);
    $("#addfiles").append('<div class="form-group ml-15" id=fileadd'+adnum+'><input type="file" name="files[]"  class="pull-left"><button class="btn btn-danger btn-xs" onclick="removefile('+adnum+')"><i class="fa fa-trash-o "></i></button></div>');
}
$("#submit").click(function() {   
    var validate="no";
    if($('#title').val()==""){ $('#e-title').removeClass("hidden"); validate="yes";} else{$('#e-title').addClass("hidden");}
    if($('#fk_category_id').val()==""){ $('#e-fk_category_id').removeClass("hidden"); validate="yes";} else{$('#e-fk_category_id').addClass("hidden");}
    if($('#projectid').val()==""){ $('#e-projectid').removeClass("hidden");validate="yes";}  else{$('#e-projectid').addClass("hidden");} 
    if($('#priority').val()==""){ $('#e-priority').removeClass("hidden");validate="yes";}  else{$('#e-priority').addClass("hidden");}
    if($('#statuss').val()==""){ $('#e-status').removeClass("hidden");validate="yes";}  else{$('#e-status').addClass("hidden");} 
    if($('#desc').val()==""){ $('#e-desc').removeClass("hidden");validate="yes";}  else{$('#e-desc').addClass("hidden");}
    if(validate=='yes'){ return false; }else{  return true;
    $('#myform').submit();
    }
});   
function removefile(eleId)
{
    $( "div" ).remove( "#fileadd"+eleId );
}

function updateTechnician(project){
	// add loader to show process
    add_loader();
    
	var project_id = project.value;
	if(project_id!=""){
		 var form_data = {     
				project_id: project_id
			}; 
			$.ajax({
	        url: "<?php echo site_url('portal/updatetechniciandropdown'); ?>",
	        type: 'POST',
	        async: true,
	        data: form_data,
	        success: function(msg) {
	            $('#technician_dropdown_div').html(msg);

	         	// hide loader now
	      		remove_loader();
	        }

	    });
	}
}
   
    </script>
         <!-- Custom Js -->

       
    
    

</body>
</html>
