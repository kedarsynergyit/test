
<section id="main-content">
	<section class="wrapper site-min-height">
	<div class=" pull-left">
		<h3><i class="fa fa-angle-right"></i> Blog List</h3>
	</div>
	<div class=" pull-right">
		<h3><a href="addnews" class="btn btn-primary btn-sm btn-block" style="color: white;">Add Blog</a></h3>
	</div>

	<div class="row mt">
		<div class="col-lg-12">
			<link href="<?php echo base_url(); ?>assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
			
			<!-- Advanced Tables -->
			<div class="panel panel-default">
			
				<div class="panel-body">
				<?php     if($this->session->flashdata('error_message')) {
				   			?><br /><center><span class="error_flash"><?php echo $this->session->flashdata('error_message'); ?></span></center><?php 
                   }
?>
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover" id="dataTablesNews" style="table-layout:fixed;">
							<thead>
								<tr>
									<th>Description</th>
									<th width="10%">From Date</th>
									<th width="10%">To Date</th>
									<th width="5%">Show Customers?</th>
									<th width="5%">Show Users?</th>
									<th width="5%">Edit Details</th>
								</tr>
							</thead>
							<tbody>
							<?php if(!empty($details)){ foreach($details as $row){ ?>
						
								<tr class="odd gradeX">
									<td><span class="text-overflow-wrap"><?php echo $row->notification; ?></span></td>
									<td><?php echo date("d F, Y",strtotime($row->from)); ?></td>
									<td><?php echo date("d F, Y",strtotime($row->to)); ?></td>
									<td class="center"><?php echo ($row->show_customer==0)?"No":"Yes"; ?></td>
									<td class="center"><?php echo ($row->show_user==0)?"No":"Yes"; ?></td>
									<td class="center">
										<a href="newsdetails?id=<?php echo $row->id; ?>"><i class="fa fa-bars"></i></a>
									</td>
								</tr>
								<?php } } else{
									?>
									<tr>
										<td colspan="6">No Data Available</td>
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
	$('#dataTablesNews').dataTable();
		"order": []
    });
});
</script>
<!-- Custom Js -->


</body>
</html>
