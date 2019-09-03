
<section id="main-content">
	<section class="wrapper site-min-height">
	<div class=" pull-left">
		<h3><i class="fa fa-inbox"></i> Inbox</h3>
	</div>
	<?php //if($urole!=1){ ?>
	<div class=" pull-right">
		<h3><a href="addmessage" class="btn btn-primary btn-sm btn-block" style="color: white;">Send Message</a></h3>
	</div>
	<?php //} ?>

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
						<table class="table table-striped table-bordered table-hover" id="dataTablesMessages" style="table-layout:fixed;">
							<thead>
								<tr>
									<th width="20%">From</th>
									<th width="70%">Subject</th>
									<th width="10%"><?php echo 'View';//($urole==1)?'View':'Edit'; ?> Details</th>
								</tr>
							</thead>
							<tbody>
							<?php if(!empty($details)){ foreach($details as $row){ ?>
						
								<tr class="odd gradeX" style="font-weight:<?php echo ($row->read_unread==0)?'bold':'normal'; ?>;">
									<td><?php echo $row->from_email; ?></td>
									<td>
										<?php echo $row->subject; ?>
									</td>
									<td class="center">
										<?php 
											$mark_as_read = ($row->read_unread==0)?'onclick="markMessageAsRead('.$row->id.');"':'';
										?>
										<a href="viewmessagedetails?id=<?php echo $row->id; ?>" <?php echo $mark_as_read; ?>><i class="fa fa-bars"></i></a>
										
										<!-- reply to -->
										&nbsp;
										<a href="<?php echo base_url().'index.php/portal/replyTo?id='.$row->id; ?>" title="Reply to this message..."><i class="fa fa-reply" aria-hidden="true"></i></a>
										
										<?php /*if($urole==1){ ?>
											<a href="viewmessagedetails?id=<?php echo $row->id; ?>"><i class="fa fa-bars"></i></a>
										<?php }else{ ?>
											<a href="messagedetails?id=<?php echo $row->id; ?>"><i class="fa fa-bars"></i></a>
										<?php }*/ ?>
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

function markMessageAsRead(id){
	var data = "id="+id;
	var url = "<?php echo base_url().'index.php/portal/markMessageAsRead'; ?>";
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
