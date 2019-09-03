<section id="main-content">
	<section class="wrapper site-min-height">
		
		<div class=" pull-left" ><h3><i class="fa fa-user-circle-o"></i> Profile Report</h3></div>
		
		<div class="row mt">
			<div class="col-lg-12">
				
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="row">
							<?php 
								/*echo $heading."<br />";
						    	echo $graph_title."<br /><pre>";
						    	print_r($arrX);
						    	print_r($arrProfiles);
						    	print_r($arrY);*/
							?>
						</div>
						
						<!-- top filters -->
						<?php echo form_open_multipart('portal/profilereport','class="form-horizontal" id="frm_profile_report"');?>
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
							<div class="col-lg-3">&nbsp;</div>
							<div class="col-lg-3">
								<div class="input-group">
									<span class="input-group-addon">Status : </span>
									<select class="form-control pull-left" name="active_inactive_users" id="active_inactive_users">
										<option value="-1" <?php if($active_inactive_users==-1){ echo 'selected'; } ?>>- All -</option>
										<option value="1" <?php if($active_inactive_users==1){ echo 'selected'; } ?>>Active</option>
										<option value="2" <?php if($active_inactive_users==2){ echo 'selected'; } ?>>In Active</option>
										<option value="3" <?php if($active_inactive_users==3){ echo 'selected'; } ?>>Closed</option>
									</select>
								</div>
								<?php /* ?><div class="form-group" style="margin-right: 0px;">
									<div class="pull-right">
										<label class="pull-left" for="active_inactive_users">Status</label>
										<select class="form-control pull-left" name="active_inactive_users" id="active_inactive_users">
											<option value="-1" <?php if($active_inactive_users==-1){ echo 'selected'; } ?>>- All -</option>
											<option value="1" <?php if($active_inactive_users==1){ echo 'selected'; } ?>>Active</option>
											<option value="2" <?php if($active_inactive_users==2){ echo 'selected'; } ?>>In Active</option>
											<option value="3" <?php if($active_inactive_users==3){ echo 'selected'; } ?>>Closed</option>
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
						
						<!-- overall status -->
						<div class="row">
							<div class="col-lg-12 text-center">
								<div class="report_title" style="font-size: 25px; text-decoration: underline;">overall status</div>
							</div>
						</div>
						<div class="row">
							<?php if(isset($percentage['active']) && isset($percentage['inactive']) && isset($percentage['closed'])){ ?>
								<div class="col-lg-4">
									<div class="box1">
										<h4 style="font-size: 35px; color: #1bdb07;"><?php echo $percentage['active']; ?></h4>
										<h4 style="font-size: 35px; color: #333; text-transform: uppercase;">Active</h4>
									</div>
								</div>
								<div class="col-lg-4">
									<div class="box1">
										<h4 style="font-size: 35px;"><?php echo $percentage['inactive']; ?></h4>
										<h4 style="font-size: 35px; color: #333; text-transform: uppercase;">In-Active</h4>
									</div>
								</div>
								<div class="col-lg-4">
									<div class="box1">
										<h4 style="font-size: 35px; color: #db0707;"><?php echo $percentage['closed']; ?></h4>
										<h4 style="font-size: 35px; color: #333; text-transform: uppercase;">closed</h4>
									</div>
								</div>
							<?php }else if(isset($percentage['active']) && !isset($percentage['inactive']) && !isset($percentage['closed'])){ ?>
								<div class="col-lg-12">
									<div class="box1">
										<h4 style="font-size: 35px; color: #1bdb07;"><?php echo $percentage['active']; ?></h4>
										<h4 style="font-size: 35px; color: #333; text-transform: uppercase;">Active</h4>
									</div>
								</div>
							<?php }else if(!isset($percentage['active']) && isset($percentage['inactive']) && !isset($percentage['closed'])){ ?>
								<div class="col-lg-12">
									<div class="box1">
										<h4 style="font-size: 35px;"><?php echo $percentage['inactive']; ?></h4>
										<h4 style="font-size: 35px; color: #333; text-transform: uppercase;">In-Active</h4>
									</div>
								</div>
							<?php }else if(!isset($percentage['active']) && !isset($percentage['inactive']) && isset($percentage['closed'])){ ?>
								<div class="col-lg-12">
									<div class="box1">
										<h4 style="font-size: 35px; color: #db0707;"><?php echo $percentage['closed']; ?></h4>
										<h4 style="font-size: 35px; color: #333; text-transform: uppercase;">closed</h4>
									</div>
								</div>
							<?php } ?>
							
						</div>
						<!-- overall status ends -->
						
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
			label: 'Total',
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
					labelString: 'Profile Status'
				}
			}],
			yAxes: [{
				display: true,
				scaleLabel: {
					display: true,
					labelString: 'No. of Profiles'
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
		$("#frm_profile_report").submit();
	});

	$("#end_date").on("change",function(){
		$("#frm_profile_report").submit();
	});

	$("#active_inactive_users").on("change",function(){
		$("#frm_profile_report").submit();
	});
});
</script>