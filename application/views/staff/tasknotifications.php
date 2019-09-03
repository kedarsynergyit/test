<section id="main-content">
	<section class="wrapper site-min-height">
	<div class=" pull-left">
		<h3>Ticket Notifications</h3>
	</div>
	
	<div class="row mt">
		<div class="col-lg-12">
			<link href="<?php echo base_url(); ?>assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
			
			<!-- Advanced Tables -->
			<div class="panel panel-default">
			
				<div class="panel-body">
				<?php if($this->session->flashdata('error_message')) { ?>
					<br /><center><span class="error_flash"><?php echo $this->session->flashdata('error_message'); ?></span></center>
				<?php }?>
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover" id="dataTablesTicketNotifications" style="table-layout:fixed;">
							<thead>
								<tr>
									<th width="10%">Ticket ID</th>
									<th width="80%">Updates</th>
									<th width="10%">View Details</th>
								</tr>
							</thead>
							<tbody>
							<?php 
								if(!empty($details)){ foreach($details as $row){
									
									$mark_as_read = ($row->read_unread==0)?'onclick="markTicketNotificationAsRead('.$row->id.');"':''; 
							?>
						
								<tr class="odd gradeX" style="font-weight:<?php echo ($row->read_unread==0)?'bold':'normal'; ?>;">
									<td>
										<a href="taskdetails?id=<?php echo $row->fk_task_id; ?>" <?php echo $mark_as_read; ?>>
											<?php echo $row->ticket_id; ?>
										</a>
									</td>
									<td>
										<?php echo $row->changes; ?>
									</td>
									<td class="center">
										<a href="taskdetails?id=<?php echo $row->fk_task_id; ?>" <?php echo $mark_as_read; ?>>
											<i class="fa fa-bars"></i>
										</a>
										
									</td>
								</tr>
								<?php } } else{
									?>
									<tr>
										<td colspan="3">No Notifications Available</td>
									</tr>
									<?php 
								} ?>
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
<script>
$(document).ready(function () {
	$('#dataTablesTicketNotifications').dataTable({
		"order": [],
		"columnDefs": [{
		    "defaultContent": "-",
		    "targets": "_all"
		  }]
    });
});

function markTicketNotificationAsRead(id){
	var data = "id="+id;
	var url = "<?php echo base_url().'index.php/portal/markTicketNotificationAsRead'; ?>";
    $.ajax({
		type: "POST",  // Request method: post, get 
		url: url,//window.location.href, // URL to request 
		data: data,
		async: true,
		global:true,
		success: function(response) {
			// nothing on change
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
