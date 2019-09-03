<section id="main-content">
	<section class="wrapper site-min-height">
	<div class=" pull-left">
		<h3><i class="fa fa-paper-plane-o"></i> Sent Messages</h3>
	</div>
	<?php /* ?>
	<div class=" pull-right">
		<h3><a href="addmessage" class="btn btn-primary btn-sm btn-block" style="color: white;">Send Message</a></h3>
	</div>
	<?php */ ?>

	<div class="row mt">
		<div class="col-lg-12">
			<link href="<?php echo base_url(); ?>assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
			
			<!-- Advanced Tables -->
			<div class="panel panel-default">
			
				<div class="panel-body">
				<?php if($this->session->flashdata('error_message')) { ?>
					<br /><center><span class="error_flash"><?php echo $this->session->flashdata('error_message'); ?></span></center>
				<?php } ?>
				
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover" id="dataTablesMessages" style="table-layout:fixed;">
							<thead>
								<tr>
									<th width="20%">To</th>
									<th width="70%">Subject</th>
									<th width="10%">View Details</th>
								</tr>
							</thead>
							<tbody>
							<?php if(!empty($details)){ foreach($details as $row){ ?>
						
								<tr class="odd gradeX">
									<td><?php echo $row->to_name; ?></td>
									<td>
										<?php echo $row->subject; ?>
									</td>
									<td class="center">
										<a href="viewsentmessagedetails?id=<?php echo $row->id; ?>"><i class="fa fa-bars"></i></a>
									</td>
								</tr>
								<?php } } else{
									?>
									<tr>
										<td colspan="3">No Data Available</td>
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
	$('#dataTablesMessages').dataTable({
		"order": [],
		"columnDefs": [{
		    "defaultContent": "-",
		    "targets": "_all"
		  }]
    });
});
</script>
<!-- Custom Js -->