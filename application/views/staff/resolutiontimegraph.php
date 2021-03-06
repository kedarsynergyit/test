<section id="main-content">
	<section class="wrapper site-min-height">
		
		<div class="pull-left" ><h3><i class="fa fa-ticket"></i> Resolution Time Graph</h3></div>
		<?php /* ?><div class="pull-right" >
			<h3><a href="exportticketreport?<?php echo base64_encode("condition"); ?>=<?php echo base64_encode($condition); ?>" target="_blank"; class="btn btn-success btn-sm btn-block"><i class="fa fa-file-excel-o"></i> Export</a></h3>
		</div><?php */ ?>
		
		<div class="row mt">
			<div class="col-lg-12">
				
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="row">
							<?php 
								/*echo $heading."<br />";
						    	echo $graph_title."<br /><pre>";
						    	print_r($arrX);
						    	print_r($arrTickets);
						    	print_r($arrY);*/
							?>
						</div>
						
						<!-- top filters -->
						<?php echo form_open_multipart('portal/resolutiontimegraph','class="form-horizontal" id="frm_sla_report"');?>
						<div class="row">
							<div class="col-lg-3">
								<div class="input-group">
									<span class="input-group-addon">From Date : </span>
									<input type="text" name="start_date" class="form-control" id="start_date" placeholder="From" value="<?php echo $start_date; ?>">
									<span class="input-group-addon"><i class="fa fa-calendar" id="start_datei"></i></span>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="input-group">
									<span class="input-group-addon">To Date : </span>
									<input type="text" name="end_date" class="form-control" id="end_date" placeholder="To" value="<?php echo $end_date; ?>">
									<span class="input-group-addon"><i class="fa fa-calendar" id="end_datei"></i></span>
								</div>
							</div>
							
							<div class="col-lg-3">
								<div class="input-group">
									<span class="input-group-addon">Customers : </span>
									<?php echo form_dropdown('customers', $customers, $customer,' id="customers" class="form-control " '); ?>
								</div>
							</div>
							
							<div class="col-lg-3">
								<?php /* ?><div class="input-group">
									<span class="input-group-addon">Status : </span>
									<select class="form-control" name="active_inactive_users" id="active_inactive_users">
										<option value="-1" <?php if($active_inactive_users==-1){ echo 'selected'; } ?>>- All -</option>
										<option value="0" <?php if(empty($active_inactive_users)){ echo 'selected'; } ?>>Not Started</option>
										<option value="1" <?php if($active_inactive_users==1){ echo 'selected'; } ?>>Started</option>
										<option value="2" <?php if($active_inactive_users==2){ echo 'selected'; } ?>>In-Progress</option>
										<option value="3" <?php if($active_inactive_users==3){ echo 'selected'; } ?>>On-Hold</option>
										<option value="4" <?php if($active_inactive_users==4){ echo 'selected'; } ?>>Completed</option>
									</select>
								</div>
								
								<div class="form-group" style="margin-right: 0px;">
									<div class="pull-right">
										<select class="form-control" name="active_inactive_users" id="active_inactive_users">
											<option value="-1" <?php if($active_inactive_users==-1){ echo 'selected'; } ?>>- All -</option>
											<option value="0" <?php if(empty($active_inactive_users)){ echo 'selected'; } ?>>Not Started</option>
											<option value="1" <?php if($active_inactive_users==1){ echo 'selected'; } ?>>Started</option>
											<option value="2" <?php if($active_inactive_users==2){ echo 'selected'; } ?>>In-Progress</option>
											<option value="3" <?php if($active_inactive_users==3){ echo 'selected'; } ?>>On-Hold</option>
											<option value="4" <?php if($active_inactive_users==4){ echo 'selected'; } ?>>Completed</option>
										</select>
									</div>
								</div><?php */ ?>
							</div>
						</div>
						<?php echo form_close(); ?>
						<!-- FILTER ENDS -->
						
						<br />
						
						<!-- REPORT PART -->
						<div class="row">
							
							<div class="col-lg-12">
							
								<!-- report title -->
								<div class="row">
									<div class="col-lg-12 text-center">
										<div class="report_title"><?php echo $heading; ?></div>
									</div>
								</div>
								
								<!-- chart -->
								<div class="row">
									<div class="col-lg-12 text-center">
										
										<!-- canvas style -->
										<style>
										canvas {
											-moz-user-select: none;
											-webkit-user-select: none;
											-ms-user-select: none;
										}
										</style>
										
										<!-- graph canvas -->
										<canvas id="canvas"></canvas>
										
									</div>
								</div>
							
							</div>
							
						</div>
						<!-- REPORT ENDS -->
						
						<div class="row">&nbsp;</div>
						<div class="row">&nbsp;</div>
						<div class="row">&nbsp;</div>
						
					</div>
				</div>
				
			</div>
		</div>
		
	</section>
</section>

<!-- include js -->
<?php /* ?><link href="<?php echo base_url(); ?>assets/js/charts/Chart.bundle.js" /><?php */ ?>
<?php /* ?><script type="text/javascript" src="http://www.chartjs.org/assets/Chart.js"></script><?php */ ?>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.2/Chart.min.js"></script>
<link href="<?php echo base_url(); ?>assets/js/charts/utils.js" />
										
										
<script>
color = Chart.helpers.color;

var barChartData = {
	labels:[<?php echo implode(",", $arrX); ?>],
	datasets: [
	<?php 
	if(count($arrY)==1 && isset($arrY[0])){ 
	?>
		{
			label: 'Response Time',
			backgroundColor: "#00b7ff",
			borderColor: "#00b7ff",
			borderWidth: 1,
			data: [<?php echo implode(",", $arrY[0]); ?>]
		}
	<?php 
	}else{
		$counter = 1;
		foreach ($arrY as $status=>$details){
			?>{
				label: '<?php echo $status; ?>',
				backgroundColor: "<?php echo $details['color']; ?>",
				borderColor: "<?php echo $details['color']; ?>",
				borderWidth: 1,
				data: [<?php echo implode(",", $details['details']); ?>]
			}<?php
			if($counter!=count($arrY)){
				// just to put comma for each dataset seperater
				?>,<?php
			}
			$counter++;
		}
	?><?php } ?>
	]
};

var ctx = document.getElementById("canvas").getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: barChartData,
    options: {
		responsive: true,
		legend: {
			position: 'top',
		},
		title: {
			display: true,
			text: '<?php echo $graph_title; ?>'
		},
		scales: {
			xAxes: [{
				display: true,
				scaleLabel: {
					display: true,
					labelString: 'Ticket Number'
				}
			}],
			yAxes: [{
				display: true,
				scaleLabel: {
					display: true,
					labelString: 'Response time in minutes'
				}
			}]
		}
	}
});

$(function(){

	$("#start_date").datepicker({
        changeMonth: true,
    	changeYear: false
    });

    $("#end_date").datepicker({
        changeMonth: true,
    	changeYear: false
    });

    $("#start_datei").click(function() {
        $("#start_date").datepicker("show" );
    });

    $("#end_datei").click(function() {
        $("#end_date").datepicker("show" );
    });
	
	$("#start_date").on("change",function(){
		$("#frm_sla_report").submit();
	});

	$("#end_date").on("change",function(){
		$("#frm_sla_report").submit();
	});

	$("#customers").on("change",function(){
		$("#frm_sla_report").submit();
	});
});
</script>