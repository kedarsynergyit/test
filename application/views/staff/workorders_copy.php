<section id="main-content">
	<section class="wrapper site-min-height">
	<div class=" pull-left">
		<h3><i class="fa fa-angle-right"></i> Workorder List</h3>
	</div>
	<div class=" pull-right">
		<?php /* ?><h3><a href="addworkorder" class="btn btn-primary btn-sm btn-block" style="color: white;">Add Workorder</a></h3><?php */ ?>
	</div>

	<div class="row mt">
		<div class="col-lg-12">
			<link href="<?php echo base_url(); ?>assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
			
			<!-- Advanced Tables -->
			<div class="panel panel-default">
			
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover" id="dataTablesWorkorders">
							<thead>
								<tr>
									<th>W/O Number</th>
									<th>Customer</th>
									<th>Assigned To</th>
									<th style="width:90px;">W/O Status</th>
									<th>Signed W/O</th>
									<th>Tech Invoice</th>
									<th>Added Info</th>
									<th>Pending Info Alert</th>
									<?php /* ?><th>Approved</th><?php */ ?>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
							<?php if(!empty($details)){ foreach($details as $row){ ?>
						
								<tr class="odd gradeX">
									<td><a <?php if($internal_user_external_user!=2 && $row['status']==1 && $internal_user_external_user!=1){ ?>onclick="changeWorkOrderStatus(<?php echo $row['id_workorder']; ?>,2,1)"<?php } ?> href="<?php echo $this->config->item('base_url')."/workorderfiles/".$row['workorder_number'].".pdf"; ?>" target="_blank"><?php echo $row['workorder_number']; ?></a></td>
									<td><?php echo $row['companyname']; ?></td>
									<td><?php echo $row['first_name']." ".$row['last_name']; ?></td>
									<td class="center">
										<?php
											$status_state = "not started";
											$a_title = "not started";
											$status_class = "workorder_status_not_started";
											
											if($row['status']==2){
												$status_state = "in progress";
												$a_title = "in progress";
												$status_class = "workorder_status_in_progress";
											}else if($row['status']==3){
												$status_state = "pending";
												if($internal_user_external_user!=2){
													$a_title = "mark workorder as completed";
												}else{
													$a_title = "pending";
												}
												$status_class = "workorder_status_pending";
											}else if($row['status']==4){
												$status_state = "completed";
												$a_title = "Completed";
												$status_class = "workorder_status_completed";
											} 
										?>
										<span title="<?php echo $a_title; ?>" class="<?php echo $status_class; ?>" id="mark_workorder_as_done_<?php echo $row['id_workorder']; ?>" <?php if($row['status']==3 && $internal_user_external_user!=2){ ?>onclick="mark_workorder_as_done(<?php echo $row['id_workorder']; ?>)" style="cursor: pointer;" <?php } ?>>
											<?php echo $status_state; ?>
										</span>
										<?php /*if($row['status']==1){ ?>
											<span class="workorder_status_pending" id="mark_workorder_as_done_<?php echo $row['id_workorder']; ?>"><a href="#" title="Mark Workorder as Done"><i class="fa fa-minus"></i></a></span>
										<?php }else{ ?>
											<span class="workorder_status_completed" id="mark_workorder_as_done_<?php echo $row['id_workorder']; ?>"><a href="#" title="Completed"><i class="fa fa-check"></i></a></span>
										<?php }*/ ?>
									</td>
									
									<td class="center">
										<?php
										if(isset($row['signed_wo']) && !empty($row['signed_wo'])){
											?>
											<a href="<?php echo base_url('workorderfiles/signed_wo/'.$row['signed_wo']); ?>" target="_blank">
												<i class="fa fa-file-text" aria-hidden="true"></i>
											</a>
											<?php
										}else{
											echo "-";
										} 
										?>
									</td>
									<td class="center">
										<?php
										if(isset($row['tech_invoice']) && !empty($row['tech_invoice'])){
											?>
											<a href="<?php echo base_url('workorderfiles/tech_invoice/'.$row['tech_invoice']); ?>" target="_blank">
												<i class="fa fa-file-text" aria-hidden="true"></i>
											</a>
											<?php
										}else{
											echo "-";
										} 
										?>
									</td>
									<td class="center">
										<?php
										if(isset($row['added_info']) && !empty($row['added_info'])){
											?>
											<a href="<?php echo base_url('workorderfiles/added_info/'.$row['added_info']); ?>" target="_blank">
												<i class="fa fa-file-text" aria-hidden="true"></i>
											</a>
											<?php
										}else{
											echo "-";
										} 
										?>
									</td>
									<td class="center">
										<?php
											$pending_info = array();
											if(empty($row['signed_wo'])){
												$pending_info[] = "Signed W/O";
											}
											if(empty($row['tech_invoice'])){
												$pending_info[] = "Tech Invoice";
											}
											if(empty($row['added_info'])){
												$pending_info[] = "Added Info";
											}
											
											echo implode(", ", $pending_info);
										?>
									</td>
									<?php /* ?><td class="center">Approved</td><?php */ ?>
									
									<?php /* ?><td><?php echo $row['first_name']." ".$row['last_name']; ?></td>
									<td id="workorder_status_<?php echo $row['id_workorder']; ?>"><?php echo ($row['status']==1)?"Assigned":"Completed"; ?></td><?php */ ?>
									<td class="center">
										<span><a <?php if($internal_user_external_user!=2 && $row['status']==1 && $internal_user_external_user!=1){ ?>onclick="changeWorkOrderStatus(<?php echo $row['id_workorder']; ?>,2,0)"<?php } ?> href="workorderdetails?id=<?php echo $row['id_workorder']; ?>" title="Edit"><i class="fa fa-bars"></i></a></span>
									</td>
								</tr>
								<?php } } ?>
							</tbody>
						</table>
					</div>
				
				</div>
			</div>
		<!--End Advanced Tables --> <!-- /. PAGE INNER  -->
		</div>
	</div>

	</section>
<! --/wrapper -->
</section>
<!-- /MAIN CONTENT -->

<!-- /. PAGE WRAPPER  -->
<!-- /. WRAPPER  -->
<!-- JS Scripts-->
<script src="<?php echo base_url(); ?>assets/js/jquery.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery-1.8.3.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
<!-- DATA TABLE SCRIPTS -->
<script src="<?php echo base_url(); ?>assets/js/dataTables/jquery.dataTables.js"></script>
<script src="<?php echo base_url(); ?>assets/js/dataTables/dataTables.bootstrap.js"></script>
<script>
$(document).ready(function () {
	$('#dataTablesWorkorders').dataTable({
		"order": []
    });
});

function mark_workorder_as_done(id_workorder){
	if(confirm("Are you sure you want to mark this workorder as Done?")){
		add_loader();

		var data = "id_workorder="+id_workorder;
		var url = "<?php echo base_url().'index.php/portal/markWorkorderAsDone'; ?>";
	    $.ajax({
			type: "POST",  // Request method: post, get 
			url: url,//window.location.href, // URL to request 
			data: data,
			async: true,
			global:true,
			success: function(response) {
				var arrResponse = response.split('||');
				$("#mark_workorder_as_done_"+id_workorder).html(arrResponse[0]);
				$("#mark_workorder_as_done_"+id_workorder).prop('title',arrResponse[1]);
				$("#mark_workorder_as_done_"+id_workorder).prop('class',arrResponse[2]);
				//$("#mark_workorder_as_done_"+id_workorder).prop('onclick',null).off('click');
				$("#mark_workorder_as_done_"+id_workorder).removeAttr('onclick');
				$("#mark_workorder_as_done_"+id_workorder).css('cursor','text');
	    		remove_loader();
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				
			}
		});
	}
	return false;
}

function changeWorkOrderStatus(id_workorder,status_to_change,refresh_page){
	var data = "id_workorder="+id_workorder+"&status_to_change="+status_to_change;
	var url = "<?php echo base_url().'index.php/portal/changeWorkOrderStatus'; ?>";
    $.ajax({
		type: "POST",  // Request method: post, get 
		url: url,//window.location.href, // URL to request 
		data: data,
		async: true,
		global:true,
		success: function(response) {
			if(refresh_page==1){
				location.reload();
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			
		}
	});
	return false;
}
</script>
<!-- Custom Js -->


</body>
</html>
