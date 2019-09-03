     <section id="main-content">
          <section class="wrapper site-min-height">
           <h3>
               <i class="fa fa-chevron-circle-right"></i>  Workorder Details
                  </h3>
                  <p class="pull-left"><a href="workorders" ><span class="badge bg-theme">Back</span></a></p>
           <div id="page-inner"> 
              <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                       <div class="panel-heading">
                          <?php  echo "Workorder Number : ".$details['workorder_number']; ?> 
                       </div>
                       
                        <div class="panel-body">
                            <?php echo form_open_multipart('portal/update_workorder','class="form-horizontal" id="myform"');?> 
                            <div class="row">
                                
                                <div class="col-lg-6 ml-15">
                                        <div class="form-group">
                                            <label>Customer Name</label>
                                            <?php /* ?><div><?php echo $details['companyname']; ?></div><?php */ ?>
                                            
                            				<?php /* ?><?php echo form_dropdown('fk_customer_id', $customer_list,$details['fk_customer_id'],' id="fk_customer_id"  class="form-control" '); ?>
                                            <div class="text-right hidden" id="e-fk_customer_id" ><span class="alert-danger">Please select customer</span></div><?php */ ?>
                                            
                                            <input type="text" class="form-control" name="customer_name" id="customer_name" value="<?php echo $details['customer_name']; ?>">
                                            <div class="text-right hidden" id="e-customer_name" ><span class="alert-danger">Please enter customer name</span></div>
                                            
                                            <input type="hidden" class="form-control" name='id_workorder' id='id_workorder' value="<?php echo $details['id_workorder']; ?>">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Assigned To</label>
                                            <div><?php echo $details['first_name']." ".$details['last_name']; ?></div>
                                        </div>
                                        
                                        <?php 
                                        if($logged_in_userid!="1001" && $internal_user_external_user==1){
                                        	// show a dropdown to make the status cancelled 
                                        ?>
                                        <div class="form-group">
	                                            <label>Mark as Cancelled</label>                                       
	                                            <select name="workorder_status" id="workorder_status" class="form-control">
	                                                <option selected="selected" value="0">- select -</option>
	                                                <option value="5">Cancelled</option>
	                                            </select>
	                                            <div class="text-right hidden" id="e-role" ><span class="alert-danger">Required Field</span></div>
	                                    </div>
                                        <?php } ?>
                                        
                                        <?php
                                        if(isset($details['files']) && !empty($details['files'])){
                                        	?>
                                        	<div class="form-group">
                                            	<label>Files</label>
                                            	<div>
		                                        	<?php
		                                        	foreach ($details['files'] as $each_file){
		                                        		?>
		                                        		<i  class="fa fa-file fa-3" aria-hidden="true"></i>
		                                        		<a target="_blank" href="<?php echo base_url()."workorderfiles/".$each_file; ?>">
		                                        			<b>Download <?php echo strtoupper($each_file); ?> File</b>
		                                        		</a><br />
		                                        		<?php
		                                        	}
		                                        	?>
                                        		</div>
                                        	</div>
                                        	<?php
                                        }
                                        ?>
                                        
										<?php
										// if its internal user, then allow to upload files 
										if($internal_user_external_user==1){
										?>
										<div class="ml-15" id="addfiles">                                            
                                        </div>
                                        
										<div class="form-group">  
                                            <input type="hidden" name="num" id="num" value="0" >
                                            <button type="button" class="btn btn-default btn-xs" onclick="addMoreFiles()">Add More Files</button>
                                        </div>
										<?php
										} 
										?>
										
										<!-- Signed W/O -->
										<div class="form-group">
                                            <label>Signed Workorder</label>
                                            <?php
                                            	if(isset($details['signed_workorder_file']) && !empty($details['signed_workorder_file'])){
                                            		?>
                                            		<div class="ml-15" id="wo_signed_div">
                                            			<?php if($internal_user_external_user==1){ ?>
                                            			<a class="btn btn-primary btn-xs" onclick="removeWoFiles('wo_signed','<?php echo $details['id_workorder']; ?>');">
															<i class="fa fa-trash-o "></i>
														</a>
														<?php }else{ ?>
															<a class="btn btn-primary btn-xs" href="<?php echo base_url('workorderfiles/signed_wo/'.$details['signed_workorder_file']); ?>" target="_blank">
																<i class="fa fa-file-text "></i>
															</a>
														<?php } ?>
	                                            		<a href="<?php echo base_url('workorderfiles/signed_wo/'.$details['signed_workorder_file']); ?>" target="_blank">
															View File
														</a>
													</div>
                                            		<?php
                                            	}
                                            ?>
                                            <div class="ml-15" id="wo_signed_file_div" style="display:<?php echo (isset($details['signed_workorder_file']) && !empty($details['signed_workorder_file']))?'none':'inline-block'; ?>;">
                                            	<input type="file" name="signed_wo_file"  class="pull-left">
                                        	</div>
                                        </div>
                                        
                                        <!-- Tech Invoice -->
										<div class="form-group">
                                            <label>Tech Invoice</label>
                                            <?php
                                            	if(isset($details['tech_invoice_file']) && !empty($details['tech_invoice_file'])){
                                            		?>
                                            		<div class="ml-15" id="wo_tech_invoice_div">
                                            			<?php if($internal_user_external_user==1){ ?>
                                            			<a class="btn btn-primary btn-xs" onclick="removeWoFiles('wo_tech_invoice','<?php echo $details['id_workorder']; ?>');">
															<i class="fa fa-trash-o "></i>
														</a>
														<?php }else{ ?>
															<a class="btn btn-primary btn-xs" href="<?php echo base_url('workorderfiles/tech_invoice/'.$details['tech_invoice_file']); ?>" target="_blank">
																<i class="fa fa-file-text "></i>
															</a>
														<?php } ?>
	                                            		<a href="<?php echo base_url('workorderfiles/tech_invoice/'.$details['tech_invoice_file']); ?>" target="_blank">
															View File
														</a>
													</div>
                                            		<?php
                                            	}
                                            ?>
                                            <div class="ml-15" id="wo_tech_invoice_file_div" style="display:<?php echo (isset($details['tech_invoice_file']) && !empty($details['tech_invoice_file']))?'none':'inline-block'; ?>;">
	                                        	<input type="file" name="tech_invoice_file"  class="pull-left">
                                        	</div>
                                        </div>
                                        
                                        <!-- Added Info -->
										<div class="form-group">
                                            <label>Added Info</label>
                                            <?php
                                            	if(isset($details['added_info_file']) && !empty($details['added_info_file'])){
                                            		?>
                                            		<div class="ml-15" id="wo_added_info_div">
                                            			<?php if($internal_user_external_user==1){ ?>
                                            			<a class="btn btn-primary btn-xs" onclick="removeWoFiles('wo_added_info','<?php echo $details['id_workorder']; ?>');">
															<i class="fa fa-trash-o "></i>
														</a>
														<?php }else{ ?>
															<a class="btn btn-primary btn-xs" href="<?php echo base_url('workorderfiles/added_info/'.$details['added_info_file']); ?>" target="_blank">
																<i class="fa fa-file-text "></i>
															</a>
														<?php } ?>
	                                            		<a href="<?php echo base_url('workorderfiles/added_info/'.$details['added_info_file']); ?>" target="_blank">
															View File
														</a>
													</div>
                                            		<?php
                                            	}
                                            ?>
                                            <div class="ml-15" id="wo_added_info_file_div" style="display:<?php echo (isset($details['added_info_file']) && !empty($details['added_info_file']))?'none':'inline-block'; ?>;">
	                                        	<input type="file" name="added_info_file"  class="pull-left">
                                        	</div>
                                        </div>
                                      
                                      <div  class="text-center">
                                        <button type="submit" name="submit" id="submit" class="btn btn-round btn-success btn-sm">Submit</button>
                                        <button type="reset" name="reset" id="reset"  class="btn btn-round btn-warning btn-sm">Reset</button>
                                    </div>
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                                
                                <!-- /.col-lg-6 (nested) -->
                               
                            </div>
                            <?php echo form_close(); ?>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                        
                        <!-- comment form -->
						<div class="panel-body hidden" id="addcomm" >   
                         	<?php echo form_open_multipart('portal/addworkordercomments','class="form-horizontal" id="my_form_id"');?> 
							<div class="col-lg-12 ml-15">
								<div class="form-group">
									<label>Comment</label>
									<input type="hidden" value="<?php echo  $details['id_workorder']; ?>"  id="fk_workorder_id" name="fk_workorder_id">
                                	<textarea class="form-control" rows="4" name="comment" id="comment"></textarea>
								</div>
							</div>
                            <div class="text-center">
                                <button type="submit" name="submits" id="submits" class="btn btn-round btn-success btn-sm">Submit</button>
                            </div>
                            <?php echo form_close(); ?>
                        </div>
                                <!-- / comment form -->
                        
                    </div>
                    <!-- /.panel -->
                    
                    <!-- DISPLAYING COMMENTS -->
                    <div class="panel panel-default">
						<div class="panel-heading">Comments <span class="pull-right badge bg-theme"  id="addcomment"><i class="fa fa-plus"></i>  Add Comment</span></div>
						<div class="panel-body">
							<?php  if(!empty($comment)){ foreach($comment as $row){ ?>
							<div class=" ds">
								<div class="desc">                
									<div class="" style=" margin: 0 10px 0 20px;">
										<div class="pull-left">
											<p>
												<span class="badge bg-theme"><i class="fa fa-comment"></i></span> &nbsp;<muted><?php echo $row['commented_by']; ?></muted>
											</p>
                                        </div>                       
                                        <div class="pull-right"><p><muted><?php echo date('Y-m-d  H:i A',strtotime($row['created_on'])); ?></muted></p></div>
									</div> 
									<br/><br/>
                              
									<div class="ml-15"> <?php  echo $row['comments']; ?></div>  
                               
								</div>                        
							</div>
                             <?php } }else{ echo "No comments available!"; } ?>
						</div>
					</div>
                    <!-- /DISPLAYING COMMENTS -->
                    
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
	$("#addcomment").click(function(){
	    var x = document.getElementById('addcomm');
		x.classList.toggle("hidden");
	  
		$("#my_form_id").on("submit",function(){
			add_loader();
		});			   

	});
});

function addMoreFiles(){
    var addnum=$('#num').val();
    var adnum=parseInt(addnum)+parseInt(1);
    $('#num').val(adnum);
    $("#addfiles").append('<div class="form-group ml-15" id=fileadd'+adnum+'><input type="file" name="files[]"  class="pull-left"><button class="btn btn-danger btn-xs" onclick="removefile('+adnum+')"><i class="fa fa-trash-o "></i></button></div>');
}
$("#submit").click(function() {   
	var validate="no";
	//if($('#fk_customer_id').val()==""){ $('#e-fk_customer_id').removeClass("hidden");validate="yes";}  else{$('#e-fk_customer_id').addClass("hidden");}
	if($('#customer_name').val()==""){ $('#e-customer_name').removeClass("hidden");validate="yes";}  else{$('#e-customer_name').addClass("hidden");}
	if(validate=='yes'){ return false; }else{  return true;
    $('#myform').submit();
    }
});   
function removefile(eleId)
{
    $( "div" ).remove( "#fileadd"+eleId );
}
function removeWoFiles(table,id_workorder){
	if(confirm("Are you sure you want to remove this file?")){
		// add loader to show process
	    add_loader();
	    
		var form_data = {id_workorder:id_workorder,table:table};
		 
		$.ajax({
	        url: "<?php echo site_url('portal/removeWoFiles'); ?>",
	        async: true,
	        type: 'POST',
	        data: form_data,
	        success: function(msg) {
	            alert(msg);
	         	// hide loader now
	      		remove_loader();

	      		// now remove the file link and show select file
	      		$("#"+table+"_div").remove();
	      		$("#"+table+"_file_div").show();
	        }
	    });
	}
}
</script>
<!-- Custom Js -->
</body>
</html>
