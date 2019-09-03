     <section id="main-content">
          <section class="wrapper site-min-height">
			<div class="pull-left"><h3><?php /* ?><i class="fa fa-chevron-circle-right"></i><?php */ ?> Add Project</h3></div>
			<div class="pull-right"><h3><a class="btn btn-primary btn-sm btn-block" href="projects" >Back</a></h3></div>
           <div id="page-inner"> 
              <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                      
                        <div class="panel-body">
                            <?php echo form_open_multipart('portal/insert_project','class="form-horizontal" id="myform"');?> 
                            <div class="row">
                                
                                <div class="col-lg-6 ml-15">
                                        <div class="form-group">
                                            <label>Project Title</label>
                                           
                                            <input class="form-control" name='title' id='title'>
                                            <div class="text-right hidden" id="e-title"><span class="alert-danger">Required Field</span></div>
                                            <div class="text-right hidden" id="e-title-text-limit"><span class="alert-danger">Maximum 50 Characters allowed</span></div>
                                        </div>                                      
                                        <div class="form-group">
                                            <label>Customer</label>
                                            <?php echo form_dropdown('customer', $customer,'',' id="customer"  class="form-control " '); ?>
                                            <div class="text-right hidden" id="e-customer" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Start Date</label>
                                            <input type="text" name="dob"  class="datefieldwidth" id="dob" placeholder="Date">&nbsp;<i class="fa fa-calendar" id="dobi"></i>            
                                            <div class="text-right hidden" id="e-dob" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        <div class="form-group">
                                            <label >Completed Expected Date</label>
                                            <input type="text" name="doi" class="datefieldwidth" id="doi" placeholder="Date">&nbsp;<i class="fa fa-calendar" id="doii"></i>              
                                            <div class="text-right hidden" id="e-doi" ><span class="alert-danger">Required Field</span></div>              
                                        </div>
                                       <div class="form-group">
                                            <label >Account Manager :</label>              
                                            <span id="accmgrselect"> <?php  echo form_dropdown('accountmanager', $account_manager,'','class="form-control" id="accountmanager"'); ?></span>
                                            <div class="text-right hidden" id="e-accountmanager" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label >Primary Tech :</label>              
                                            <span id="primarytechselect"> <?php  echo form_dropdown('primary_tech', $primary_tech,'','class="form-control" id="primary_tech"'); ?></span>
                                            <div class="text-right hidden" id="e-primary_tech" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        
                                       <?php /* ?><div class="form-group">
                                            <label >Project Manager :</label>              
                                            <?php  echo form_dropdown('projectmanager', $project_manager,'','class="form-control" id="projectmanager"'); ?>
                                            <div class="text-right hidden" id="e-projectmanager" ><span class="alert-danger">Required Field</span></div>
                                        </div><?php */ ?>
                                        <div class="form-group">
                                            <label >Tech Lead (Optional):</label>              
                                            <?php  echo form_multiselect('developer[]', $developer,array(),'class="form-control" id="developer"'); ?>
                                            <div class="text-right hidden" id="e-developer" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        <div class="form-group">
                                            <label >Status :</label>              
                                            <select name="status" id="project_status" class="form-control">
                                            	<option value="1" <?php echo ($status==1)?'selected':''; ?>>Open</option>
                                            	<option value="2" <?php echo ($status==2)?'selected':''; ?>>Closed</option>
                                            </select>
                                        </div>
                                        <?php /* ?><div class="form-group">
                                            <label >Status :</label>              
                                            <?php  echo form_dropdown('status', $status,'0','class="form-control" id="status"'); ?>
                                            <div class="text-right hidden" id="e-status" ><span class="alert-danger">Required Field</span></div>
                                        </div><?php */ ?>
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
                                            <button type="button" class="btn btn-default btn-xs" onclick="addMoreFiles()">Add More</button>
                                        </div>
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea class="form-control" rows="3" name="desc" id="desc"></textarea>
                                            <label class="textarea-character-limit pull-right"><span id="txtarea_character_limit">150</span> characters left</label><br />
                            				<input type="hidden" value="150" id="txt_character_limit_hidden">
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
 
function addMoreFiles(){
    var addnum=$('#num').val();
    var adnum=parseInt(addnum)+parseInt(1);
    $('#num').val(adnum);
    $("#addfiles").append('<div class="form-group ml-15" id=fileadd'+adnum+'><input type="file" name="files[]"  class="pull-left"><button class="btn btn-danger btn-xs" onclick="removefile('+adnum+')"><i class="fa fa-trash-o "></i></button></div>');
}
$("#submit").click(function() {   
    var validate="no";
    if($('#title').val()==""){ $('#e-title').removeClass("hidden"); validate="yes";} else{$('#e-title').addClass("hidden");}
    if($('#title').val()!=""){
    	if($('#title').val().length>50){
    		$('#e-title-text-limit').removeClass("hidden"); validate="yes";
    	}else{ 
    		$('#e-title-text-limit').addClass("hidden");
    	}
    }
     
    if($('#customer').val()==""){ $('#e-customer').removeClass("hidden");validate="yes";}  else{$('#e-customer').addClass("hidden");} 
    if($('#dob').val()==""){ $('#e-dob').removeClass("hidden");validate="yes";}  else{$('#e-dob').addClass("hidden");} 
    if($('#doi').val()==""){ $('#e-doi').removeClass("hidden");validate="yes";}  else{$('#e-doi').addClass("hidden");} 
    if($('#accountmanager').val()==""){ $('#e-accountmanager').removeClass("hidden");validate="yes";}  else{$('#e-accountmanager').addClass("hidden");}
    if($('#primary_tech').val()==""){ $('#e-primary_tech').removeClass("hidden");validate="yes";}  else{$('#e-primary_tech').addClass("hidden");} 
    if($('#projectmanager').val()==""){ $('#e-projectmanager').removeClass("hidden");validate="yes";}  else{$('#e-projectmanager').addClass("hidden");} 
    //if($('#developer').val()==""){ $('#e-developer').removeClass("hidden");validate="yes";}  else{$('#e-developer').addClass("hidden");} 
     //if($('#status').val()==""){ $('#e-status').removeClass("hidden");validate="yes";}  else{$('#e-status').addClass("hidden");} 
    if($('#desc').val()==""){ $('#e-desc').removeClass("hidden");validate="yes";}  else{$('#e-desc').addClass("hidden");}
    if(validate=='yes'){ return false; }else{  return true;
    $('#myform').submit();
    }
});   
function removefile(eleId)
{
    $( "div" ).remove( "#fileadd"+eleId );
}
$("#customer").change(function() {   
      var customer=$("#customer").val();
       
   var form_data = {     
     customer:customer
       
          }; 
         $.ajax({
        url: "<?php echo site_url('portal/getaccountmanger'); ?>",
        type: 'POST',
        data: form_data,
        success: function(msg) {
            $('#accmgrselect').html(msg);
        }

    });
});  

$(document).ready(function () {
	var maxLength = $("#txt_character_limit_hidden").val();
    $('#desc').keyup(function() {
		var textlen = maxLength - $(this).val().length;
		if (textlen < 0) {
			$(this).val($(this).val().substring(0, maxLength));
		} else {
    		$("#txtarea_character_limit").html(textlen);
		}
    });
});
    </script>
         <!-- Custom Js -->

       
    
    
