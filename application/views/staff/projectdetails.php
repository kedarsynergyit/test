     <section id="main-content">
          <section class="wrapper site-min-height">
           <div class="pull-left"><h3><?php /* ?><i class="fa fa-chevron-circle-right"></i><?php */ ?> Project details</h3></div>
           <div class="pull-right"><h3><a class="btn btn-primary btn-sm btn-block" href="projects" >Back</a></h3></div>
           <div id="page-inner"> 
              <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                      <div class="panel-heading">
                           <?php  echo $company; ?> 
                        </div>
                        <div class="panel-body">
                            <?php echo form_open_multipart('portal/update_project','class="form-horizontal" id="myform"');?> 
                            <div class="row">
                                
                                <div class="col-lg-6 ml-15">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <input type='hidden' name='id' id="id" value="<?php echo $details[0]->id; ?>" />
                                            <input class="form-control" name='title' id='title' value="<?php echo $details[0]->title; ?>" >
                                            <div class="text-right hidden" id="e-title"><span class="alert-danger">Required Field</span></div>
                                        </div>                                      
                                        
                                        <div class="form-group">
                                            <label>Start Date</label>
                                            <input type="text" name="dob"  class="datefieldwidth" id="dob" placeholder="Date"  value="<?php echo $details[0]->created_on; ?>" >&nbsp;<i class="fa fa-calendar" id="dobi"></i>            
                                            <div class="text-right hidden" id="e-dob" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        <div class="form-group">
                                            <label >Completed Expected Date</label>
                                            <input type="text" name="doi" class="datefieldwidth" id="doi" placeholder="Date"  value="<?php echo $details[0]->end_date; ?>" >&nbsp;<i class="fa fa-calendar" id="doii"></i>              
                                            <div class="text-right hidden" id="e-doi" ><span class="alert-danger">Required Field</span></div>              
                                        </div>
                                       
                                       <?php /* ?><div class="form-group">
                                            <label >Project Manager :</label>              
                                            <?php  echo form_dropdown('projectmanager', $project_manager,$details[0]->projectmanager,'class="form-control" id="projectmanager"'); ?>
                                            <div class="text-right hidden" id="e-projectmanager" ><span class="alert-danger">Required Field</span></div>
                                        </div><?php */ ?>
                                        
                                        <div class="form-group">
                                            <label >Primary Tech :</label>              
                                            <span id="primarytechselect"> <?php  echo form_dropdown('primary_tech', $primary_tech,$details[0]->primary_tech,'class="form-control" id="primary_tech"'); ?></span>
                                            <div class="text-right hidden" id="e-primary_tech" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label >Tech Lead (Optional):</label> 
                                            <?php 
                                            	$opt= explode(',', $details[0]->developer);
                                            	$opt = array_filter($opt, function($value){ return $value !== ''; }); 
                                            ?>
                                            <?php  echo form_multiselect('developer[]', $developer,$opt,'class="form-control" id="developer"'); ?>
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
                                            <?php  echo form_dropdown('status', $status,$details[0]->status,'class="form-control" id="status"'); ?>
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
                                      <?php  if(!empty($images)) { ?>
                                   <div class="table-responsive">
                                       <table class="table table-striped  table-hover"  >
                                        <tr>
                                              <?php $i=1; foreach ( $images as $img){ ?>
											   
                                                     
                                            <td class="text-center imagetd" style="border-top:0px;"> 
											  <?php   $endexdt= pathinfo($img->image_path, PATHINFO_EXTENSION);
                                                     if($endexdt=='jpg'||$endexdt=='jpeg'||$endexdt=='png'||$endexdt=='gif'){ ?>
											<img data-toggle="modal" data-target="#myModal" src="<?php echo base_url(); ?><?php echo $img->image_path; ?>"  width="100" height="100">
											<?php } else { ?>
										<i  class="fa fa-file fa-3" aria-hidden="true"></i> <a target="_blank" href="<?php echo base_url().$img->image_path; ?>"><b>Download <?php echo strtoupper($endexdt); ?> File</b></a><?php } ?>
											</td>
                                          <?php $i++; if($i==6){$i=1; echo '</tr>';} } if($i==5) { echo '</tr>'; } ?>
                                        </tr>
                                    </table>
                                   </div>
                                    <?php } ?>
                               
                                   
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea class="form-control" rows="3" name="desc" id="desc"><?php echo $details[0]->details; ?></textarea>
                                            <div class="text-right hidden" id="e-desc"><span class="alert-danger">Required Field</span></div>
                                        </div>
                                              <?php  $uid= $this->session->userdata('sid');$urole= $this->session->userdata('srole');
											  if(($details[0]->accountmanager==$uid)||($details[0]->projectmanager==$uid)||($urole==1)){ ?>                              
                                        
                                        <div class="form-group">
											<div class="pull-left"><button type="submit" name="submit" id="submit" class="btn btn-primary btn-sm btn-block">Submit</button></div>
											<div class="pull-left" style="margin-left: 10px;"><button type="reset" name="reset" id="reset"  class="btn btn-block btn-primary btn-sm">Cancel</button></div>
                                        </div>
											  <?php } ?>
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
$("#reset").click(function() { 
$(location).attr('href', 'projects');
});
    </script>
         <!-- Custom Js -->

       
    
    
