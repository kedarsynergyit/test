<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Dashboard">
   

    <title>Synergy Portal</title>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/jquery-ui-1.12.0/jquery-ui.css" /> 
    <!-- Bootstrap core CSS -->
    <link href="<?php echo base_url(); ?>assets/css/bootstrap.css" rel="stylesheet">
    <!--external css-->
    <link href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/gritter/css/jquery.gritter.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/lineicons/style.css">    
    <link href="<?php echo base_url(); ?>assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
    <!-- Custom styles for this template -->
    <link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/style-responsive.css" rel="stylesheet">

	<script src="<?php echo base_url(); ?>assets/js/common.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/chart-master/Chart.js"></script>
    
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?php  $sessionid = $this->session->userdata('sid');
    $sessionrole=$this->session->userdata('srole');
 $usertype=$this->session->userdata('internal_user_external_user');	?>
 
 	<script src="<?php echo base_url(); ?>assets/js/jquery.js"></script>
 	<script>
	    $(document).ready(function () {
		    <?php if(isset($sessionrole) && !empty($sessionrole) && $sessionrole>1){ ?>
		    	// only call this function if the role is not 1(admin), to check if any project is revoked or not
		    	checkProjectAssignments();
		    <?php } ?>

		    function checkProjectAssignments(){
			    var url = "<?php echo base_url().'index.php/portal/checkProjectAssignments'; ?>";
			    $.ajax({
					type: "GET",  // Request method: post, get 
					url: url,//window.location.href, // URL to request 
					data: '',
					global:true,
					success: function(response) {
						if(response==1){
							// redirect here
							window.location = "<?php echo base_url().'index.php/portal/logout'; ?>";
						}
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						
					}
				});
		    	setTimeout(checkProjectAssignments, 3000);
		    }

		    $("#notification_bell_a").on("click",function(){
		    	// when user clicks on the bell icon, simply update the session variables and hide the bell icon for new comments to come up
		    	var url = "<?php echo base_url().'index.php/portal/updateCommentsSession'; ?>";
			    $.ajax({
					type: "GET",  // Request method: post, get 
					url: url,//window.location.href, // URL to request 
					data: '',
					global:true,
					success: function(response) {
			    		$(this).hide();
	    				window.location.href = "<?php echo base_url().'index.php/portal/dashboard'; ?>";
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						
					}
				});
				
			    //$(this).hide();
			});

		    $("#notification_bell_workorders_comments").on("click",function(){
		    	// when user clicks on the bell icon, simply update the session variables and hide the bell icon for new comments to come up
		    	var url = "<?php echo base_url().'index.php/portal/updateWorkorderCommentsSession'; ?>";
			    $.ajax({
					type: "GET",  // Request method: post, get 
					url: url,//window.location.href, // URL to request 
					data: '',
					global:true,
					success: function(response) {
			    		$(this).hide();
	    				window.location.href = "<?php echo base_url().'index.php/portal/dashboard'; ?>";
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						
					}
				});
				
			    //$(this).hide();
			});

			// below function will check for any new comments there or not
		    checkNewComments();
		    function checkNewComments(){
		    	var url = "<?php echo base_url().'index.php/portal/checkNewComments'; ?>";
			    $.ajax({
					type: "GET",  // Request method: post, get 
					url: url,//window.location.href, // URL to request 
					data: '',
					global:true,
					success: function(response) {
						if(response>0){
							$("#notification_bell_value").html(response);
							$("#notification_bell_a").show();
						}
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						
					}
				});
		    	setTimeout(checkNewComments, 3000);
		    }

		 	// below function will check for any new comments there or not
		    checkNewWorkorderComments();
		    function checkNewWorkorderComments(){
		    	var url = "<?php echo base_url().'index.php/portal/checkNewWorkorderComments'; ?>";
			    $.ajax({
					type: "GET",  // Request method: post, get 
					url: url,//window.location.href, // URL to request 
					data: '',
					global:true,
					success: function(response) {
						if(response>0){
							$("#notification_bell_workorders_comments_value").html(response);
							$("#notification_bell_workorders_comments").show();
						}
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						
					}
				});
		    	setTimeout(checkNewWorkorderComments, 3000);
		    }
		    
		});
    </script>
 
  </head>

  <body>

  <section id="container">
      <!-- **********************************************************************************************************************************************************
      TOP BAR CONTENT & NOTIFICATIONS
      *********************************************************************************************************************************************************** -->
      <!--header start-->
      <header class="header black-bg">
              <div class="sidebar-toggle-box">
                  <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
              </div>
            <!--logo start-->
            <a href="dashboard" class="logo"><img src="<?php echo base_url(); ?>assets/img/logo.png"/></a>
            <!--logo end-->
            <div class="nav notify-row" id="top_menu">
                <!--  notification start -->
                <ul class="nav top-menu">
                    <!-- settings start -->
                    <?php  ?>
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="index.html#">
                            <i class="fa fa-tasks"></i>
                            <span class="badge bg-theme"><?php echo $countoftask; ?></span>
                        </a>
                        <ul class="dropdown-menu extended tasks-bar">
                            <div class="notify-arrow notify-arrow-green"></div>
                            <li>
                                <p class="green">Tasks</p>
                            </li>
                             <li>
                                <a href="#">
                                    <div class="task-info">
                                        <div class="desc">Not Started (<?php if(!isset($notstarted)) $notstarted=0;  echo $notstarted; ?>)</div>
                                           <?php if( $notstarted==0){$notstartedperc=0;}else{ $notstartedperc=round((($notstarted/$countoftask)*100),2) ; }?>
                                        <div class="percent"><?php echo $notstartedperc; ?>%</div>
                                    </div>
                                    <div class="progress progress-striped">
                                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $notstartedperc; ?>%">
                                            <span class="sr-only"><?php echo $notstartedperc; ?>% Complete (warning)</span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                                  <li>
                                <a href="#">
                                    <div class="task-info">
                                        <div class="desc"> Started (1 to 50%) (<?php if(!isset($started)) $started=0;  echo $started; ?>)</div>
                                          <?php  if( $started==0){$startedperc=0;}else{ $startedperc=round((($started/$countoftask)*100),2)  ;} ?>
                                        <div class="percent"><?php echo $startedperc; ?>%</div>
                                    </div>
                                    <div class="progress progress-striped">
                                        <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $startedperc; ?>%">
                                            <span class="sr-only"><?php echo $startedperc; ?>% Complete (warning)</span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                           
                            <li>
                                <a href="#">
                                    <div class="task-info">
                                        <div class="desc">In Progress (50 to 100%)  (<?php if(!isset($inprogress)) $inprogress=0;  echo $inprogress; ?>)</div>
                                            <?php if( $inprogress==0){$inprogressperc=0;}else{ $inprogressperc=round((($inprogress/$countoftask)*100),2) ; }?>
                                        <div class="percent"><?php echo $inprogressperc; ?>%</div>
                                    </div>
                                    <div class="progress progress-striped">
                                        <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="<?php echo $inprogressperc; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $inprogressperc; ?>%">
                                            <span class="sr-only"><?php echo $inprogressperc; ?>% Complete</span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <div class="task-info">
                                        <div class="desc">On Hold  (<?php if(!isset($onhold)) $onhold=0;  echo $onhold; ?>)</div>
                                        <?php if( $onhold==0){$onholdperc=0;}else{ $onholdperc=round((($onhold/$countoftask)*100),2)  ; } ?>
                                        <div class="percent"><?php echo $onholdperc; ?>%</div>
                                    </div>
                                    <div class="progress progress-striped">
                                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="<?php echo $onholdperc; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $onholdperc; ?>%">
                                            <span class="sr-only"><?php echo $onholdperc; ?>% Complete (Important)</span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <div class="task-info">
                                        <div class="desc">Completed(<?php if(!isset($completed)) $completed=0; echo $completed;  ?>)</div>
                                         <?php if( $completed==0){$completedperc=0;}else{$completedperc=round((($completed/$countoftask)*100),2)  ;}?>
                                        <div class="percent"><?php echo $completedperc; ?>%</div>
                                    </div>
                                    <div class="progress progress-striped">
                                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo $completedperc; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $completedperc; ?>%">
                                            <span class="sr-only"><?php echo $completedperc; ?>% Complete (success)</span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="external">
                                <a href="projects">See All Projects</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                    	<a href="#" style="color:#fc4646; display:none;" id="notification_bell_a">
                            <i class="fa fa-bell"></i>
                            <span id="notification_bell_value" class="badge bg-theme" style="background-color:#fc4646;"><?php echo $current_number_of_comments; ?></span>
                        </a>
                    </li>
                    <li>
                    	<a href="#" style="color:#fce046; display:none;" id="notification_bell_workorders_comments">
                            <i class="fa fa-bell"></i>
                            <span id="notification_bell_workorders_comments_value" class="badge bg-theme" style="background-color:#fce046; color: #000000;"><?php echo $current_number_of_workorder_comments; ?></span>
                        </a>
                    </li>
                    <!-- settings end -->
                   
                </ul>
                <!--  notification end -->
            </div>
            <div class="top-menu">
            	<ul class="nav pull-right top-menu hidden-md hidden-sm hidden-xs">
            		<li><span class="logged_in_username"><?php echo "Welcome, ".$logged_in_username; ?></span></li>
                    <li><a class="logout" href="logout">Logout</a></li>
            	</ul>
            </div>
        </header>
      <!--header end-->
      
      <!-- **********************************************************************************************************************************************************
      MAIN SIDEBAR MENU
      *********************************************************************************************************************************************************** -->
      <!--sidebar start-->
      <aside>
          <div id="sidebar"  class="nav-collapse " >
              <!-- sidebar menu start-->
              <ul class="sidebar-menu" id="nav-accordion">
              <?php //echo $this->uri->uri_string; ?>
              	  <p class="centered"></p>
              	 
              	 <li class="visible-md visible-sm visible-xs">
                      <a href="#">
                          <span style="color: #ffffff !important;"><?php echo "Welcome, ".$logged_in_username; ?></span>
                      </a>
                  </li>
              	 
                  <li class="mt">
                      <a <?php if($this->uri->uri_string=="portal/dashboard") {echo 'class="active"';} ?> href="dashboard">
                          <i class="fa fa-dashboard"></i>
                          <span>Dashboard</span>
                      </a>
                  </li>

				<?php if($internal_user_external_user!=2){ ?>
                  <li class="sub-menu">
                      <a <?php if(($this->uri->uri_string=="portal/projects") || ($this->uri->uri_string=="portal/task" )) {echo 'class="active"';} ?>href="projects">
                          <i class="fa fa-book"></i>
                          <span>Projects / Services</span>
                      </a>
                  </li>
                  <?php } ?>
				  <li class="sub-menu">
                      <a <?php if($this->uri->uri_string=="customerportal/alltask") {echo 'class="active"';} ?> href="alltask?ticket=open">
                          <i class="fa fa-ticket"></i>
                          <span>Tickets</span>
                      </a>
                  </li>
				  <?php if($usertype==1){ ?>
                  <li class="sub-menu">
                      <a  <?php if($this->uri->uri_string=="portal/Customer") {echo 'class="active"';} ?> href="customers">
                           <i class="fa fa-address-card"></i>
                          <span>Customer</span>
                      </a>
                  </li>
				   <?php } if($sessionrole==1){ ?>
                   <li class="sub-menu">
                      <a  <?php if($this->uri->uri_string=="portal/users") {echo 'class="active"';} ?> href="users">
                           <i class="fa fa-users"></i>
                          <span>Users</span>
                      </a>
                  </li>
                  <?php } if($internal_user_external_user!=2){
                  	?>
                  	<li class="sub-menu">
                      <a  <?php if($this->uri->uri_string=="portal/news") {echo 'class="active"';} ?> href="news">
                           <i class="fa fa-newspaper-o"></i>
                          <span>Blog</span>
                      </a>
                  </li>
                  	<?php
                  }?>
				  
				 <?php /* workorder menu item */ 
					// the workorder will be displayed to all employees 
					?>
					<li class="sub-menu">
						<a  <?php if($this->uri->uri_string=="portal/workorders") {echo 'class="active"';} ?> href="workorders">
							<i class="fa fa-file-pdf-o"></i>
							<span>Workorders</span>
						</a>
					</li>
					
					<?php /*if($usertype==1){*/ ?>
					<li class="sub-menu">
						<a  <?php if($this->uri->uri_string=="portal/completedworkorders") {echo 'class="active"';} ?> href="completedworkorders">
							<i class="fa fa-file-pdf-o"></i>
							<span>Completed Workorders</span>
						</a>
					</li>
					<?php /*}*/ ?>
                  
                <!--   <li class="sub-menu">
                      <a <?php if($this->uri->uri_string=="portal/settings") {echo 'class="active"';} ?> href="settings">
                          <i class="fa fa-newspaper-o"></i>
                          <span>News</span>
                      </a>                     
                  </li>-->
                  <li class="sub-menu">
                      <a <?php if($this->uri->uri_string=="portal/settings") {echo 'class="active"';} ?> href="settings">
                          <i class="fa fa-cogs"></i>
                          <span>Settings</span>
                      </a>                     
                  </li>
                  
                  <?php 
                  	if(isset($chat_project_list) && !empty($chat_project_list)){
                  		?>
                  		<li class="sub-menu" style="border-top: 1px solid #989898 !important; text-transform: uppercase;">
							<a href="javascript:void();">
								<i class="fa fa-comments"></i>
								<span>Projects Chat</span>
							</a>
						</li>
                  		<?php
                  		foreach ($chat_project_list as $each_chat_project){
                  			?>
                  			<li class="sub-menu">
								<a href="chat_project?project_id=<?php echo $each_chat_project['project_id']; ?>" <?php if($this->uri->uri_string=="portal/chat_project") {echo 'class="active"';} ?>>
									<span><?php echo $each_chat_project['project_name']; ?></span>
								</a>
							</li>
                  			<?php
                  		} 
                  ?>
                  <?php } ?>
                  
                  <li class="visible-md visible-sm visible-xs" style="border-top: 1px solid #989898 !important;">
                      <a href="logout">
						<i class="fa fa-sign-out"></i>
                        <span style="color: #ffffff !important;">Logout</span>
                      </a>
                  </li>
                
              </ul>
              <!-- sidebar menu end-->
          </div>
      </aside>
      <!--sidebar end-->
  <?php //$this->output->enable_profiler(TRUE); ?>