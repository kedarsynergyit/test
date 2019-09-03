     <section id="main-content">
          <section class="wrapper site-min-height">
          	<div class=" pull-left" ><h3><?php /* ?><i class="fa fa-angle-right"></i><?php */ ?> Tech Profiles</h3></div>
                
                <?php if($urole==1){ ?>
                	<div class=" pull-right"><h3><a href="addtech" class="btn btn-primary btn-sm btn-block" style="color:white;">Add Tech</a></h3></div>
                <?php } ?>
                
          	
          	<div class="row mt">
          		<div class="col-lg-12">
					<link href="<?php echo base_url(); ?>assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />

                    <!-- Advanced Tables -->
                    <div class="panel panel-default">
                       
                        <div class="panel-body">
                        	<!-- show dropdown to show users active/inactive/all -->
                        	<?php /* ?><form name ="frm_filter" id="frm_filter" action="" method="get">
	                        	Show Users :
	                        	<select name="active_inactive_users" id="active_inactive_users" onchange="$('#frm_filter').submit();">
	                        		<option value="0" <?php if($active_inactive==0){ echo 'selected'; } ?>>- All -</option>
	                        		<option value="1" <?php if($active_inactive==1){ echo 'selected'; } ?>>Active</option>
	                        		<option value="2" <?php if($active_inactive==2){ echo 'selected'; } ?>>In Active</option>
	                        	</select>
                        	</form><?php */ ?>
                        	Show Techs :
	                        	<select name="active_inactive_users" id="active_inactive_users">
	                        		<option value="0" <?php if($active_inactive==0){ echo 'selected'; } ?>>- All -</option>
	                        		<option value="1" <?php if($active_inactive==1){ echo 'selected'; } ?>>Active</option>
	                        		<option value="2" <?php if($active_inactive==2){ echo 'selected'; } ?>>In Active</option>
	                        		<option value="3" <?php if($active_inactive==3){ echo 'selected'; } ?>>Deleted</option>
	                        	</select>
                        	<div style="clear:both;">&nbsp;</div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-users">
                                    <thead>
                                        <tr>
                                            <?php /* ?><th width="10%">Tech Id</th>
                                            <th width="15%">User ID</th><?php */ ?>
                                            <th width="10%">First Name</th>
                                            <th width="10%">Last Name</th>
                                            <th width="15%">Email ID</th>                                           
                                            <th width="10%">Contact No.</th>
											<?php /* ?><th width="10%">Internal/External</th><?php */ ?>
											<th width="7%">Status</th>
											<?php if($urole==1){ ?>
                                            <th width="5%">Edit Details</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                   
                                    <tbody>   <?php if(!empty($details)){ foreach($details as $row){ ?>
                                        
                                        <tr class="odd gradeX">
                                            <?php /* ?><td><?php echo $row->employeeid; ?></td>
                                            <td class="text-overflow-wrap"><?php echo $row->username; ?></td><?php */ ?>
                                            <td class="text-overflow-wrap"><?php echo $row->first_name; ?></td>
                                            <td class="text-overflow-wrap"><?php echo $row->last_name; ?></td>
                                            <td class="center"><?php echo $row->email; ?></td>
                                            <td class="center"><?php echo $row->phone; ?></td>
											 <?php /* ?><td class="center"><?php if($row->internal_user_external_user==1)echo 'Internal';else echo'External'; ?></td><?php */ ?>
											 <td class="center">
											 	<?php
											 		$status = "Active"; 
											 		if($row->status==2){
											 			$status='Inactive';
											 		}else if($row->status==3){
											 			$status='Deleted';
											 		}
											 		echo $status;
											 	?>
											 </td>
											 <?php if($urole==1){ ?>
                                            	<td class="center"> <a href ="techdetails?id=<?php echo $row->id; ?>" > <i class="fa fa-bars"></i></a></td>
                                            <?php } ?>
                                        </tr>
                                    <?php } } ?>
                                    </tbody>
                                </table>
                            </div>
                            
                        </div>
                    </div>
                    <!--End Advanced Tables -->
             
    
  
             <!-- /. PAGE INNER  -->
            </div>
                    </div>
          	</div>
			
		</section><! --/wrapper -->
      </section><!-- /MAIN CONTENT -->

         <!-- /. PAGE WRAPPER  -->
     <!-- /. WRAPPER  -->
        <script>
            $(document).ready(function () {
				$('#dataTables-users').dataTable({
					"order": []
				});

               $("#active_inactive_users").on("change",function(){
                   location.href=location.pathname+"?active_inactive_users="+$(this).val();
               });
            });
    </script>
         <!-- Custom Js -->
      
   
</body>
</html>
