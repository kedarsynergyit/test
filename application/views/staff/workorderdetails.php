     <section id="main-content">
          <section class="wrapper site-min-height">
			<div class="pull-left"><h3><?php /* ?><i class="fa fa-chevron-circle-right"></i><?php */ ?> Workorder Details</h3></div>
			<div class="pull-right"><h3><a class="btn btn-primary btn-sm btn-block" href="workorders">Back</a></h3></div>
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
                                            <button type="button" class="btn btn-primary btn-xs" onclick="addMoreFiles()">Add More Files</button>
                                        </div>
										<?php
										} 
										?>
										
										<!-- Signed W/O -->
										<div class="form-group">
                                            <label>Signed Workorder</label>
                                            <?php
                                            	if(isset($details['signed_workorder_file']) && !empty($details['signed_workorder_file'])){
                                            		foreach ($details['signed_workorder_file'] as $id=>$value){
                                            		?>
                                            		<div class="ml-15" id="wo_signed_div<?php echo $id; ?>">
                                            			<?php if($internal_user_external_user==1){ ?>
                                            			<a class="btn btn-primary btn-xs" onclick="removeWoFiles('wo_signed','<?php echo $details['id_workorder']; ?>','<?php echo $id; ?>');">
															<i class="fa fa-trash-o "></i>
														</a>
														<?php }else{ ?>
															<a class="btn btn-primary btn-xs" href="<?php echo base_url('workorderfiles/signed_wo/'.$value); ?>" target="_blank">
																<i class="fa fa-file-text "></i>
															</a>
														<?php } ?>
	                                            		<a href="<?php echo base_url('workorderfiles/signed_wo/'.$value); ?>" target="_blank">
															View File
														</a>
													</div>
                                            		<?php
                                            		}
                                            	}
                                            ?>
                                            <?php /* ?><div class="ml-15" id="wo_signed_file_div" style="display:<?php echo (isset($details['signed_workorder_file']) && !empty($details['signed_workorder_file']))?'none':'inline-block'; ?>;">
                                            	<input type="file" name="signed_wo_file"  class="pull-left">
                                        	</div><?php */ ?>
                                        </div>
                                        
                                        <!-- add more signed wo files -->
										<div class="form-group">
											<div class="ml-15" id="addfiles_signedwo"></div>  
											<input type="hidden" name="num_signedwo" id="num_signedwo" value="0" >
											<button type="button" class="btn btn-primary btn-xs" onclick="addMoreFiles_signedwo();">Add Signed W/O</button>
										</div>
                                        
                                        <!-- Tech Invoice -->
										<div class="form-group">
                                            <label>Tech Invoice</label>
                                            <?php
                                            	if(isset($details['tech_invoice_file']) && !empty($details['tech_invoice_file'])){
                                            		foreach ($details['tech_invoice_file'] as $id=>$value){
                                            		?>
                                            		<div class="ml-15" id="wo_tech_invoice_div<?php echo $id; ?>">
                                            			<?php if($internal_user_external_user==1){ ?>
                                            			<a class="btn btn-primary btn-xs" onclick="removeWoFiles('wo_tech_invoice','<?php echo $details['id_workorder']; ?>','<?php echo $id ?>');">
															<i class="fa fa-trash-o "></i>
														</a>
														<?php }else{ ?>
															<a class="btn btn-primary btn-xs" href="<?php echo base_url('workorderfiles/tech_invoice/'.$value); ?>" target="_blank">
																<i class="fa fa-file-text "></i>
															</a>
														<?php } ?>
	                                            		<a href="<?php echo base_url('workorderfiles/tech_invoice/'.$value); ?>" target="_blank">
															View File
														</a>
													</div>
                                            		<?php
                                            		}
                                            	}
                                            ?>
                                            <?php /* ?><div class="ml-15" id="wo_tech_invoice_file_div" style="display:<?php echo (isset($details['tech_invoice_file']) && !empty($details['tech_invoice_file']))?'none':'inline-block'; ?>;">
	                                        	<input type="file" name="tech_invoice_file"  class="pull-left">
                                        	</div><?php */ ?>
                                        </div>
                                        
                                        <!-- add more techinvoice files -->
										<div class="form-group">
											<div class="ml-15" id="addfiles_techinvoice"></div>  
											<input type="hidden" name="num_techinvoice" id="num_techinvoice" value="0" >
											<button type="button" class="btn btn-primary btn-xs" onclick="addMoreFiles_techinvoice();">Add Tech Invoice</button>
										</div>
                                        
                                        <!-- Added Info -->
										<div class="form-group">
                                            <label>Added Info</label>
                                            <?php
                                            	if(isset($details['added_info_file']) && !empty($details['added_info_file'])){
                                            		foreach ($details['added_info_file'] as $id=>$value){
                                            		?>
                                            		<div class="ml-15" id="wo_added_info_div<?php echo $id; ?>">
                                            			<?php if($internal_user_external_user==1){ ?>
                                            			<a class="btn btn-primary btn-xs" onclick="removeWoFiles('wo_added_info','<?php echo $details['id_workorder']; ?>','<?php echo $id; ?>');">
															<i class="fa fa-trash-o "></i>
														</a>
														<?php }else{ ?>
															<a class="btn btn-primary btn-xs" href="<?php echo base_url('workorderfiles/added_info/'.$value); ?>" target="_blank">
																<i class="fa fa-file-text "></i>
															</a>
														<?php } ?>
	                                            		<a href="<?php echo base_url('workorderfiles/added_info/'.$value); ?>" target="_blank">
															View File
														</a>
													</div>
                                            		<?php
                                            		}
                                            	}
                                            ?>
                                            <?php /* ?><div class="ml-15" id="wo_added_info_file_div" style="display:<?php echo (isset($details['added_info_file']) && !empty($details['added_info_file']))?'none':'inline-block'; ?>;">
	                                        	<input type="file" name="added_info_file"  class="pull-left">
                                        	</div><?php */ ?>
                                        </div>
                                        
                                        <!-- add more addedinfo files -->
										<div class="form-group">
											<div class="ml-15" id="addfiles_addedinfo"></div>  
											<input type="hidden" name="num_addedinfo" id="num_addedinfo" value="0" >
											<button type="button" class="btn btn-primary btn-xs" onclick="addMoreFiles_addedinfo();">Add Added Info</button>
										</div>
                                      
                                    <div class="form-group">
                                        <div class="pull-left"><button type="submit" name="submit" id="submit" class="btn btn-block btn-primary btn-sm">Submit</button></div>
                                        <div class="pull-left" style="margin-left: 10px;"><button type="reset" name="reset" id="reset"  class="btn btn-block btn-primary btn-sm">Cancel</button></div>
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
							<div class="col-lg-12 ml-5">
								<div class="form-group">
									<label>Comment</label>
									<input type="hidden" value="<?php echo  $details['id_workorder']; ?>"  id="fk_workorder_id" name="fk_workorder_id">
                                	<textarea class="form-control" rows="4" name="comment" id="comment"></textarea>
								</div>
								<div class="form-group">
		                            <div class="pull-left">
		                                <button type="submit" name="submits" id="submits" class="btn btn-block btn-primary btn-sm">Submit</button>
		                            </div>
	                            </div>
							</div>
                            <?php echo form_close(); ?>
                        </div>
                                <!-- / comment form -->
                        
                    </div>
                    <!-- /.panel -->
                    
                    <!-- DISPLAYING COMMENTS -->
                    <div class="panel panel-default">
						<div class="panel-heading">
							Comments 
							<button class="pull-right btn btn-xs btn-primary" id="addcomment"><i class="fa fa-plus"></i>  Add Comment</button>
						</div>
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
function removeWoFiles(table,id_workorder,id){
	if(confirm("Are you sure you want to remove this file?")){
		// add loader to show process
	    add_loader();
	    
		var form_data = {id_workorder:id_workorder,table:table,id:id};
		 
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
	      		$("#"+table+"_div"+id).remove();
	      		//$("#"+table+"_file_div").show();
	        }
	    });
	}
}

// add more signed wo, tech invoice and added info
function addMoreFiles_signedwo(){
	var addnum=$('#num_signedwo').val();
    var adnum=parseInt(addnum)+parseInt(1);
    $('#num_signedwo').val(adnum);
    $("#addfiles_signedwo").append('<div class="form-group ml-15" id=fileadd_signedwo'+adnum+'><input type="file" name="files_signedwo[]"  class="pull-left"><button class="btn btn-danger btn-xs" onclick="removefile_signedwo('+adnum+')"><i class="fa fa-trash-o "></i></button></div>');
}
function removefile_signedwo(eleId){
    $( "div" ).remove( "#fileadd_signedwo"+eleId );
}

function addMoreFiles_techinvoice(){
	var addnum=$('#num_techinvoice').val();
    var adnum=parseInt(addnum)+parseInt(1);
    $('#num_techinvoice').val(adnum);
    $("#addfiles_techinvoice").append('<div class="form-group ml-15" id=fileadd_techinvoice'+adnum+'><input type="file" name="files_techinvoice[]"  class="pull-left"><button class="btn btn-danger btn-xs" onclick="removefile_techinvoice('+adnum+')"><i class="fa fa-trash-o "></i></button></div>');
}
function removefile_techinvoice(eleId){
    $( "div" ).remove( "#fileadd_techinvoice"+eleId );
}

function addMoreFiles_addedinfo(){
	var addnum=$('#num_addedinfo').val();
    var adnum=parseInt(addnum)+parseInt(1);
    $('#num_addedinfo').val(adnum);
    $("#addfiles_addedinfo").append('<div class="form-group ml-15" id=fileadd_addedinfo'+adnum+'><input type="file" name="files_addedinfo[]"  class="pull-left"><button class="btn btn-danger btn-xs" onclick="removefile_addedinfo('+adnum+')"><i class="fa fa-trash-o "></i></button></div>');
}
function removefile_addedinfo(eleId){
    $( "div" ).remove( "#fileadd_addedinfo"+eleId );
}
</script>
<!-- Custom Js -->
</body>
</html>
