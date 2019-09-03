<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Dashboard">
   

    <title>SynergyInteract</title>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/jquery-ui-1.12.0/jquery-ui.css" /> 
    <!-- Bootstrap core CSS -->
    <link href="<?php echo base_url(); ?>assets/css/bootstrap.css" rel="stylesheet">
    <!--external css-->
    <link href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
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
 $usertype=$this->session->userdata('internal_user_external_user');	
 
 // display date & time
	$dtz = new DateTimeZone('America/Toronto');
	$time_in_sofia = new DateTime('now', $dtz);
	$dtz->getOffset( $time_in_sofia );
	$offset = -($dtz->getOffset( $time_in_sofia ) / 3600);
	$set=0-$offset;
	
 ?>
 
 	<script src="<?php echo base_url(); ?>assets/js/jquery.js"></script>
 	<script>
	    $(document).ready(function () {

	    	// date_time('date_time');
	    	updateClock('date_time','<? echo date("D M j G:i:s T Y"); ?>',0,'<? echo $set; ?>');
		    
		    <?php if(isset($sessionrole) && !empty($sessionrole) && $sessionrole>1){ ?>
		    	// only call this function if the role is not 1(admin), to check if any project is revoked or not
		    	//checkProjectAssignments();
		    	//checkNewComments();
		    	//checkNewBlogs();
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
		    	var url = "<?php echo base_url().'index.php/customerportal/updateCommentsSession'; ?>";
			    $.ajax({
					type: "GET",  // Request method: post, get 
					url: url,//window.location.href, // URL to request 
					data: '',
					global:true,
					success: function(response) {
			    		$(this).hide();
    					window.location.href = "<?php echo base_url().'index.php/customerportal/dashboard'; ?>";
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						
					}
				});
				
			    //$(this).hide();
			});

			// below function will check for any new comments there or not
		    function checkNewComments(){
		    	var url = "<?php echo base_url().'index.php/customerportal/checkNewComments'; ?>";
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

		    /* blogs notification */
		    $("#notification_bell_blogs").on("click",function(){
		    	// when user clicks on the bell icon, simply update the session variables and hide the bell icon for new comments to come up
		    	var url = "<?php echo base_url().'index.php/customerportal/updateBlogsSession'; ?>";
			    $.ajax({
					type: "GET",  // Request method: post, get 
					url: url,//window.location.href, // URL to request 
					data: '',
					global:true,
					success: function(response) {
			    		$(this).hide();
    					window.location.href = "<?php echo base_url().'index.php/customerportal/dashboard'; ?>";
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						
					}
				});
				
			    //$(this).hide();
			});

			// below function will check for any new blog's there or not
		    function checkNewBlogs(){
		    	var url = "<?php echo base_url().'index.php/customerportal/checkNewBlogs'; ?>";
			    $.ajax({
					type: "GET",  // Request method: post, get 
					url: url,//window.location.href, // URL to request 
					data: '',
					global:true,
					success: function(response) {
						if(response>0){
							$("#notification_bell_blogs_value").html(response);
							$("#notification_bell_blogs").show();
						}
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						
					}
				});
		    	setTimeout(checkNewBlogs, 3000);
		    }
		    
		});

	    $(document).bind("contextmenu",function(e) {
			e.preventDefault();
    	});
	    $(document).keydown(function(e){
	        if(e.which === 123){
	           return false;
	        }
	    });
    </script>
  </head>

  <body>

  <section id="container" >
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
                <?php if($is_customer){ ?>
                <ul class="nav top-menu">
                    <!-- settings start -->
                    <?php  ?>
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle tooltips" href="index.html#" data-placement="bottom" data-original-title="Tickets">
                            <i class="fa fa-tasks"></i>
                            <span class="badge bg-theme"><?php echo $countoftask; ?></span>
                        </a>
                        <ul class="dropdown-menu extended tasks-bar">
                            <div class="notify-arrow notify-arrow-green"></div>
                            <li>
                                <p class="green">Tickets</p>
                            </li>
                            <li>
                                <a href="alltask?status=0">
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
                                <a href="alltask?status=1">
                                    <div class="task-info">
                                        <div class="desc"> Started (1 to 50%)(<?php if(!isset($started)) $started=0;  echo $started; ?>)</div>
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
                                <a href="alltask?status=2">
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
                                <a href="alltask?status=3">
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
                                <a href="alltask?status=4">
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
                        </ul>
                    </li>
					<li>
                    	<a data-placement="bottom" data-original-title="Ticket Comments" class="tooltips" href="#" style="color:#fc4646; display:none;" id="notification_bell_a">
                            <i class="fa fa-bell"></i>
                            <span id="notification_bell_value" class="badge bg-theme" style="background-color:#fc4646;"><?php echo $current_number_of_comments_client; ?></span>
                        </a>
                    </li>
                    
                    <?php /* ?><li>
                    	<a data-placement="bottom" data-original-title="Blogs" class="tooltips" href="#" style="color:#fce046; display:none;" id="notification_bell_blogs">
                            <i class="fa fa-newspaper-o"></i>
                            <span id="notification_bell_blogs_value" class="badge bg-theme" style="background-color:#fce046; color: #000000;"><?php echo $current_number_of_blogs; ?></span>
                        </a>
                    </li><?php */ ?>
                    
                    <!-- settings end -->                   
                </ul>
                <?php } ?>
                <!--  notification end -->
            </div>
            <div class="top-menu hidden-md hidden-sm hidden-xs">
            	<ul class="nav pull-right top-menu">
            		<li>
            			<div class="div_profile_picture">
            				<?php if(isset($profile_picture) && !empty($profile_picture) && file_exists(FCPATH.$profile_picture)){ ?>
            					<img src="<?php echo base_url().$profile_picture; ?>" />
            				<?php }else{ // show default photo ?>
            					<img src="<?php echo base_url()."user/user-default.png"; ?>" style="margin: -2px 0px 0px -1px;" />
            				<?php } ?>
            			</div>
            		</li>
            		<li><span class="logged_in_username"><?php echo "Welcome, ".$logged_in_clientname; ?></span></li>
                    <li><a class="logout" href="logout">Logout</a></li>
            	</ul>
            </div>
            
            <!-- display date and time -->
            <div class="hidden-md hidden-sm hidden-xs" id="date_time"></div>
        </header>
      <!--header end-->
      
      <!-- **********************************************************************************************************************************************************
      MAIN SIDEBAR MENU
      *********************************************************************************************************************************************************** -->
      <!--sidebar start-->
      <aside>
          <div id="sidebar"  class="nav-collapse ">
              <!-- sidebar menu start-->
              <ul class="sidebar-menu" id="nav-accordion">
              <?php //echo $this->uri->uri_string; ?>
              	  <p class="centered"></p>
              	 
              	 <?php /* ?><li class="mt">
                      <a id="date_time" href="#"></a>
                  </li><?php */ ?>
              	 
              	  <li class="visible-md visible-sm visible-xs">
                      <a href="#">
                      	<div class="div_profile_picture_small">
            				<?php if(isset($profile_picture) && !empty($profile_picture) && file_exists(FCPATH.$profile_picture)){ ?>
	            				<img src="<?php echo base_url().$profile_picture; ?>" />
	            			<?php }else{ // show default photo ?>
	            				<img src="<?php echo base_url()."user/user-default.png"; ?>" style="margin: -7px 0px 0px -1px;" />
	            			<?php } ?>
            				
            			</div>
                          <span style="color: #ffffff !important;"><?php echo "Welcome, ".$logged_in_clientname; ?></span>
                      </a>
                  </li>
              	 
                  <li class="sub-menu">
                      <a <?php if($this->uri->uri_string=="customerportal/dashboard") {echo 'class="active"';} ?> href="dashboard">
                          <i class="fa fa-dashboard"></i>
                          <span>Dashboard</span>
                      </a>
                  </li>
				
				<?php if($is_customer){ ?>
                  <li class="sub-menu">
                      <a <?php if(($this->uri->uri_string=="customerportal/projects") || ($this->uri->uri_string=="customerportal/task" ) || ($this->uri->uri_string=="customerportal/addtask")) {echo 'class="active"';} ?>href="projects">
                          <i class="fa fa-book"></i>
                          <span>Projects</span>
                      </a>
                  </li>
                  
                  <?php /* ?><li class="sub-menu">
                      <a href="javascript:void();" <?php if($this->uri->uri_string=="customerportal/invoices" || $this->uri->uri_string=="customerportal/paymenthistory") {echo 'class="active"';} ?>>
                          <i class="fa fa-file-text-o"></i>
                          <span>Invoices</span>
                      </a>
                      <ul style="margin-left: -50px !important;">
							<li class="sub-menu" style="margin-right:0px !important;">
								<a href="invoices" <?php if($this->uri->uri_string=="customerportal/invoices") {echo 'class="active"';} ?>>
									<i class="fa fa-bars"></i>
									<span>List Invoices</span>
								</a>
							</li>
							<li class="sub-menu" style="margin-right:0px !important;">
								<a href="paymenthistory" <?php if($this->uri->uri_string=="customerportal/paymenthistory") {echo 'class="active"';} ?>>
									<i class="fa fa-history"></i>
									<span>Payment History</span>
								</a>
							</li>
						</ul>
                  </li><?php */ ?>
                  
                  <li class="sub-menu">
                      <a href="#" <?php if($this->uri->uri_string=="customerportal/messages" || $this->uri->uri_string=="customerportal/addmessage" || $this->uri->uri_string=="customerportal/messagedetails" || $this->uri->uri_string=="customerportal/viewmessagedetails" || $this->uri->uri_string=="customerportal/replyTo" || $this->uri->uri_string=="customerportal/sentmessages" || $this->uri->uri_string=="customerportal/viewsentmessagedetails") {echo 'class="active"';} ?>>
                          <i class="fa fa-commenting-o"></i>
                          <span>Messages</span>
                      </a>
                      
                      <ul style="margin-left: -50px !important;">
                      	
                      	<li class="sub-menu" style="margin-right:0px !important;">
							<a href="messages" <?php if($this->uri->uri_string=="customerportal/messages" || $this->uri->uri_string=="customerportal/viewmessagedetails" || $this->uri->uri_string=="customerportal/addmessage" || $this->uri->uri_string=="customerportal/replyTo") {echo 'class="active"';} ?> >
								<i class="fa fa-inbox"></i>
								<span>Inbox</span>
							</a>
						</li>
						
						<li class="sub-menu" style="margin-right:0px !important;">
							<a href="sentmessages" <?php if($this->uri->uri_string=="customerportal/sentmessages" || $this->uri->uri_string=="customerportal/viewsentmessagedetails") {echo 'class="active"';} ?> >
								<i class="fa fa-paper-plane-o"></i>
								<span>Sent</span>
							</a>
						</li>
						
                      </ul>
                      
                  </li>
                  
				  <li class="sub-menu">
                      <a <?php if($this->uri->uri_string=="customerportal/alltask") {echo 'class="active"';} ?> href="alltask?ticket=open">
                          <i class="fa fa-ticket"></i>
                          <span>Tickets</span>
                      </a>
                  </li>
                  <!-- <li class="sub-menu">
                      <a  <?php if($this->uri->uri_string=="customerportal/form") {echo 'class="active"';} ?> href="form">
                           <i class="fa fa-tasks"></i>
                          <span>Contact Form</span>
                      </a>
                  </li>-->

                  <li class="sub-menu">
                      <a <?php if($this->uri->uri_string=="customerportal/settings") {echo 'class="active"';} ?> href="settings">
                          <i class="fa fa-cogs"></i>
                          <span>Settings</span>
                      </a>                     
                  </li>
                  <?php } ?>
                  
                  <?php 
                  	/*if(isset($chat_project_list) && !empty($chat_project_list)){
                  		?>
                  		<li class="sub-menu" style="border-top: 1px solid #989898 !important; text-transform: uppercase;">
							<a href="javascript:void();" <?php if($this->uri->uri_string=="customerportal/chat_project") {echo 'class="active"';} ?>>
								<i class="fa fa-comments"></i>
								<span>Projects Chat</span>
							</a>
						</li>
						
						<?php
	                  		foreach ($chat_project_list as $each_chat_project){
	                  			?>
	                  			<li class="sub-menu">
									<a href="chat_project?project_id=<?php echo $each_chat_project['project_id']; ?>" <?php if(isset($_GET['project_id']) && $_GET['project_id']==$each_chat_project['project_id']) {echo 'class="active"';} ?>>
										<i class="fa fa-comment"></i>
										<?php 
										$projectname = (strlen($each_chat_project['project_name'])>23)?substr($each_chat_project['project_name'], 0, 20)."...":$each_chat_project['project_name'];
										?>
										<span><?php echo $projectname; echo (isset($each_chat_project['num_chat_notification']) && !empty($each_chat_project['num_chat_notification']))?" (".$each_chat_project['num_chat_notification'].")":''; ?></span>
									</a>
								</li>
	                  			<?php
	                  		}
	                  		?>
						
                  <?php }*/ ?>
                  
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
  