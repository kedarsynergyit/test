
<section id="main-content">
	<section class="wrapper site-min-height">
	<div class=" pull-left">
		<h3>Ticket Categories</h3>
	</div>
	
	<div class=" pull-right">
		<h3><a href="addcategory" class="btn btn-primary btn-sm btn-block" style="color: white;">Add Category</a></h3>
	</div>
	

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
						<table class="table table-striped table-bordered table-hover" id="dataTablesCategories" style="table-layout:fixed;">
							<thead>
								<tr>
									<th width="90%">Category Name</th>
									<th width="10%">View Details</th>
								</tr>
							</thead>
							<tbody>
							<?php if(!empty($details)){ foreach($details as $row){ ?>
						
								<tr class="odd gradeX">
									<td><?php echo $row->name; ?></td>
									<td class="center">
										<a href="categorydetails?id=<?php echo $row->id; ?>"><i class="fa fa-bars"></i></a>
									</td>
								</tr>
							<?php } } else{
									?>
									<tr>
										<td colspan="2">No Data Available</td>
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
<!-- /wrapper -->
</section>
<!-- /MAIN CONTENT -->

<!-- /. PAGE WRAPPER  -->
<!-- /. WRAPPER  -->
<script>
$(document).ready(function () {
	$('#dataTablesCategories').dataTable({
		"order": [],
		"columnDefs": [{
		    "defaultContent": "-",
		    "targets": "_all"
		  }]
    });
});
</script>
<!-- Custom Js -->


</body>
</html>
