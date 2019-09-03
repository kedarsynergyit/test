   
      <!-- **********************************************************************************************************************************************************
      MAIN CONTENT
      *********************************************************************************************************************************************************** -->
      <!--main content start-->
      <section id="main-content">
      <?php
      	// show dashboard items only if the logged in user is customer
      	if($is_customer){ 
      ?>
      
		<section class="wrapper site-min-height-dash">

			<!-- the upper section -->
			<div class="row">
				<div class="col-lg-12 main-chart">
					
					<!-- First Row -->
					<div class="row">
						
						<!-- my projects -->
						<div class="col-lg-4">
							<a href="projects">
								<div class="box1">
									<span><i class="fa fa-file-pdf-o" aria-hidden="true" style="font-size: 70px;"></i></span>
									<h4 style="font-size: 30px;"><?php echo $myprojects; ?></h4>
									<h4 style="font-size: 20px;">My Projects</h4>
								</div>
							</a>
						</div>
						
						<div class="col-lg-4">&nbsp;</div>
						
						<?php /* ?><div class="col-lg-3">
							
							<div class="panel panel-default">
								<div class="panel-heading">
		                            <b>OUTSTANDING INVOICES</b>
		                        </div>
		                        <div class="panel-body">
		                        	<p>
		                        		<span style="font-size: 28px; font-weight:bold;">$<?php echo number_format($pending_invoice_amount,2); ?></span><br />
		                        		<span style="font-size: 12px;">Total Outstanding (CAD)</span>
		                        	</p>
		                        </div>
							</div>
							
						</div><?php */ ?>
						
					</div>
					
					<!-- Second Row -->
					<div class="row">
						
						<div class="col-lg-4">
							<a href="messages">
								<div class="box1">
									<span><i class="fa fa-commenting-o" aria-hidden="true" style="font-size: 70px;"></i></span>
									<?php if(!empty($new_messages)){ ?>
										<h4 style="color: #330099; font-size: 30px;"><?php echo $new_messages.' New Messages'; ?></h4>
										<?php }else{ ?>
										<h4 style="font-size: 30px;">&nbsp;</h4>
										<?php } ?>
									<h4 style="font-size: 20px;">Message Box</h4>
								</div>
							</a>
						</div>
						
						<div class="col-lg-4">
							<a href="tasknotifications">
								<div class="box1">
									<span><i class="fa fa-file" aria-hidden="true" style="font-size: 70px;"></i></span>
									<h4 style="font-size: 30px;"><?php echo $ticket_changes_notification; ?></h4>
									<h4 style="font-size: 20px;">Open Notifications</h4>
								</div>
							</a>
						</div>
						
						<div class="col-lg-4">
							<a href="settings">
								<div class="box1">
									<span><i class="fa fa-cog" aria-hidden="true" style="font-size: 70px;"></i></span>
									<h4 style="font-size: 30px;">&nbsp;</h4>
									<h4 style="font-size: 20px;">Settings</h4>
								</div>
							</a>
						</div>
						
					</div>
					
					<div class="row">
	          			<div style="height:15px;">&nbsp;</div>
	          		</div>
					
					<?php /* ?><div class="row">
						
						<!-- your account manager -->
						<div class="col-lg-3">
							<div class="darkblue-panel pn">
								<div class="darkblue-header">
									<h5>Your Account Manager</h5>
								</div>
								<?php if($accdetails[0]->userprofile==""){?>
									<i style="font-size: 6em" class="fa fa-photo fa-4x"></i>
								<?php } else { ?>
									<p><image width="80" height="80" src="<?php echo base_url(); ?><?php echo $accdetails[0]->userprofile; ?>" /></p>
								<?php } ?>
								<p style="text-transform:capitalize;"><?php echo $accdetails[0]->first_name." ".$accdetails[0]->last_name; ?></p>
								<p><i class="fa fa-phone"></i> <?php echo $accdetails[0]->phone; ?></p>
								<p><i class="fa fa-envelope"></i> <a style="color:white" href="mailto:<?php echo $accdetails[0]->email; ?>"><?php echo $accdetails[0]->email; ?></a></p>
							</div>
						</div>
						
						<!-- all tickets -->
						<div class="col-lg-3">
							<div class="box1">
								<span class="li_data"></span>
								<h4><?php echo $countoftask; ?></h4>
								<h4>All Tickets</h4>
							</div>
							<p style="text-align:center;"><a href="projects">Click Here for All Tickets / Projects.</a></p>
						</div>
						
						<!-- open tickets -->
						<div class="col-lg-3">
							<div class="box1">
								<span class=""><i class="fa fa-folder-open fa-6" aria-hidden="true"></i></span>
								<h4><?php if(empty($open)){echo '0';} else{echo $open; }?></h4>
								<h4>Open Tickets</h4>
							</div>
							<p style="text-align:center;"><a href="alltask?ticket=open">Click Here For Open Tickets</a></p>
						</div>
						
						<!-- add tickets -->
						<div class="col-lg-3">
							<div class="box1">
								<span class=""><i class="fa fa-plus-circle fa-6" aria-hidden="true"></i></span>
								<h4>&nbsp;</h4>
								<h4>Add Ticket</h4>
							</div>
							<p style="text-align:center;"><a href="addtask">Click here to add a new Ticket</a></p>
						</div>
						
					</div><?php */ ?>
					
					<!-- second row for blog and ticket notification updates -->
					<?php /* ?><div class="row">
					
						<!-- blog updates -->
						<div class="col-lg-8 ds">
							<h3>BLOG UPDATES</h3>
							<div id="divNiceScroll" style="height:300px !important; overflow:auto;">
								<?php if(!empty($news)){ foreach ($news as $each_news){?>
								<div class="desc newsdetails">
									<div class="thumb">
										<span class="badge bg-theme"><i class="fa fa-newspaper-o"></i></span> <muted><?php echo date('Y-m-d h:m A',strtotime($each_news['created_on'])); ?></muted>
									</div>
									<div class="details">
										<p class="newsp text-overflow-wrap">
											<?php echo (strlen($each_news['news_text'])>150)?substr($each_news['news_text'], 0, 147):$each_news['news_text']; ?>
											<?php if(strlen($each_news['news_text'])>150){ ?>
												<a title="click here for more info" href="newsdetails?id=<?php echo $each_news['news_id']; ?>">...more</a>
											<?php } ?>
										</p>
									</div>
								</div>
								<?php  } } else {?>
								<div class="desc">
									<div class="thumb">
										<span class="badge bg-theme"><i class="fa fa-newspaper-o"></i></span>
									</div>
									<div class="details">
										<p>No News Available</p>
									</div>
								</div>
								<?php }?>
							</div>
						</div>
						
						<!-- ticket notification -->
						<div class="col-lg-4 ds">
							<h3>TICKETS NOTIFICATIONS</h3>
							<div id="divNiceScrollTicket" style="height:300px !important; overflow:auto;">
								<?php if(!empty($taskcomm)){ foreach ($taskcomm as $comm){?>
								<div class="desc">
									<div class="thumb">
										<?php if($comm['commentedby']==1){ $noti_color='badge bg-theme';} else{$noti_color='badge bg-theme05';} ?>
										<span class="<?php echo $noti_color; ?>"><i class="fa fa-comments-o"></i></span>
									</div>
									<div class="details">
										<p>
											<a href ="taskdetails?id=<?php echo $comm['taskid']; ?>" >(<?php echo $comm['taskid_for_comment']; ?>)</a>&nbsp;<muted><?php echo date('Y-m-d (h:i a)',strtotime($comm['created_on'])); ?></muted><br/>
											<span class="each_comment_display"><?php echo $comm['comments']; ?></span> <a href ="taskdetails?id=<?php echo $comm['taskid']; ?>"  > <b> More...</b></a><br/>
										</p>
										<p class="text-right"><a href="#"><?php echo $comm['commented_by']; ?></a></p>
									</div>
								</div>
								<?php  } } else {?>
								<div class="desc">
									<div class="thumb">
										<span class="badge bg-theme"><i class="fa fa-comments-o"></i></span>
									</div>
									<div class="details">
										<p>No Notification Available</p>
									</div>
								</div>
								<?php }?>
							</div>
						</div>
						
						<div class="col-lg-4">
							<a href="tasknotifications">
								<div class="box1">
									<span><i class="fa fa-file" aria-hidden="true" style="font-size: 70px;"></i></span>
									<h4 style="font-size: 30px;"><?php echo $ticket_changes_notification; ?></h4>
									<h4 style="font-size: 20px;">Open Notifications</h4>
								</div>
							</a>
						</div>
						
					</div><?php */ ?>
					
				</div>
			</div>

             <?php /* ?> <div class="row">
                  <div class="col-lg-9 main-chart">
                   	<div class="row">   
                                                        <div class="col-md-3 col-sm-3">
                      		<div class="darkblue-panel pn">
                      			<div class="darkblue-header">
						<h5>Your Account Manager</h5>
                      			</div>
								<?php if($accdetails[0]->userprofile==""){?>
                                                            <i style="font-size: 6em" class="fa fa-photo fa-4x"></i>
                                                            <?php } else { ?>
                                                            <p><image width="80" height="80" src="<?php echo base_url(); ?><?php echo $accdetails[0]->userprofile; ?>" /></p>
                                                            <?php } ?>
								<p><?php echo $accdetails[0]->first_name; ?><?php echo $accdetails[0]->last_name; ?>
                                                                  </p> 
                                                                <p><i class="fa fa-phone"></i><?php echo $accdetails[0]->phone; ?>	</p>
								
								<p><i class="fa fa-envelope"></i> <a style="color:white" href="mailto:<?php echo $accdetails[0]->email; ?>"><?php echo $accdetails[0]->email; ?></a>	</p>
                                                                        
                      		</div><! -- /darkblue panel -->
                            </div><!-- /col-md-4 -->	
                            <div class="col-md-3 col-sm-3 box0">
                  			<div class="box1">
					  			<span class="li_data"></span>
                                                                <h4><?php echo $countoftask; ?></h4>
					  			<h4>All Tickets</h4>
                  			</div>
					  			<p><a href="projects">Click Here for All Tickets / Projects.</a></p>
                  		</div>
                           
                  	
                  		<div class="col-md-3 col-sm-3 box0">
                  			<div class="box1">
					  			<span class=""><i class="fa fa-folder-open fa-6" aria-hidden="true"></i></span>
                                                              <h4><?php if(empty($open)){echo '0';} else{echo $open; }?></h4>
					  			<h4>Open Tickets</h4>
                  			</div>
					  			<p><a href="alltask?ticket=open">Click Here For Open Tickets</a></p>
                  		</div>
                            <div class="col-md-3 col-sm-3 box0">
								<div class="box1">
									<span class=""><i class="fa fa-plus-circle fa-6" aria-hidden="true"></i></span>
									<h4>Add Ticket</h4>
								</div>
								<p><a href="addtask">Click here to add a new Ticket</a></p>
							</div>
						<!-- TWITTER PANEL -->
						
			</div><!-- /row -->	
                         <hr/>
                               	<div class="row mtbox">
                  		
                       
                     
                      <div class="custom-bar-chart no-bottom-border">
                          <div class="news">
                                 <h3>Blog Updates</h3>
                          <?php if(!empty($news)){ foreach ($news as $each_news){?>
            <div class="desc newsdetails">
               <div class="thumb">
                  <span class="badge bg-theme"><i class="fa fa-newspaper-o"></i></span> <muted><?php echo date('Y-m-d h:m A',strtotime($each_news['created_on'])); ?></muted>
                  
               </div>
               <div class="details">
                   <p class="newsp text-overflow-wrap">               
                     <?php echo (strlen($each_news['news_text'])>150)?substr($each_news['news_text'], 0, 147):$each_news['news_text']; ?>
                     <?php if(strlen($each_news['news_text'])>150){ ?>
                     	<a title="click here for more info" href="newsdetails?id=<?php echo $each_news['news_id']; ?>">...more</a>
                     <?php } ?>                  
                  </p>
                 
               </div>
            </div>
            <?php  } } else {?>
            <div class="desc">
               <div class="thumb">
                  <span class="badge bg-theme"><i class="fa fa-newspaper-o"></i></span>
               </div>
               <div class="details">
                  <p>No News Available
                  </p>
               </div>
            </div>
            <?php }?>
                          </div>
                      </div>
                  	
                  	</div><!-- /row mt -->	
                  </div><!-- /col-lg-9 END SECTION MIDDLE -->
                        
      <!-- **********************************************************************************************************************************************************
      RIGHT SIDEBAR CONTENT
      *********************************************************************************************************************************************************** -->                  
                  
                  <div class="col-lg-3 ds">
                    <!--COMPLETED ACTIONS DONUTS CHART-->
						<h3>NOTIFICATIONS</h3>
                                        
                      <!-- First Action -->
                      
                      <?php if(!empty($taskcomm)){ foreach ($taskcomm as $comm){?>
                          <div class="desc">
                      	<div class="thumb">
						<?php if($comm['commentedby']==1){ $noti_color='badge bg-theme';} else{$noti_color='badge bg-theme05';} ?>
                      		<span class="<?php echo $noti_color; ?>"><i class="fa fa-comments-o"></i></span>
                      	</div>
                      	<div class="details">
                      		<p><a href ="taskdetails?id=<?php echo $comm['taskid']; ?>" >(<?php echo $comm['taskid_for_comment']; ?>)</a>&nbsp;<muted><?php echo date('Y-m-d (h:i a)',strtotime($comm['created_on'])); ?></muted><br/>
                      		<span class="each_comment_display"><?php echo $comm['comments']; ?></span> <a href ="taskdetails?id=<?php echo $comm['taskid']; ?>"  > <b> More...</b></a><br/>
                      		</p>
                                <p class="text-right">  <a href="#"><?php echo $comm['commented_by']; ?></a> </p>
                      	</div>
                      </div>
                      <?php  } } else {?>
                <div class="desc">
                      	<div class="thumb">
                      		<span class="badge bg-theme"><i class="fa fa-comments-o"></i></span>
                      	</div>
                      	<div class="details">
                      		<p>No Notification Available
                      		</p>
                      	</div>
                      </div>
                      <?php }?>

                     
                      
                  </div><!-- /col-lg-3 -->
                  
                   
              </div><! --/row --><?php */ ?>
            
          </section>
		  <?php }else{ ?>
		  	<section class="wrapper site-min-height-dash">
          		<div class="row">
          			<div style="height:400px;">&nbsp;</div>
          		</div>
          	</section>
          <?php } ?>
		  <div style="border:0px;">
           <div class="row" style="margin-right: 0px; margin-left: 0px; margin-top: 30px;">
              <div class="col-lg-12"><img src="<?php echo base_url(); ?>assets/img/banner.png" width="100%" height="100%"/></div> 
      		</div>
        </div>
		<div>
            <!--footer start-->
      <footer class="site-footer" style="position: fixed;bottom: 0;right: 0;width: 85%;">
          <div class="text-center">
              <?php echo date("Y"); ?> - @SynergyIT
              <a href="#" class="go-top">
                  <i class="fa fa-angle-up"></i>
              </a>
          </div>
      </footer>
         </div>
      <!--footer end-->
      </section>
  
      <!--main content end-->


    <!-- js placed at the end of the document so the pages load faster -->
    <script src="<?php echo base_url(); ?>assets/js/jquery.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery-1.8.3.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
    <script class="include" type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.dcjqaccordion.2.7.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery.scrollTo.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery.nicescroll.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery.sparkline.js"></script>


    <!--common script for all pages-->
    <script src="<?php echo base_url(); ?>assets/js/common-scripts.js"></script>
    
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/gritter/js/jquery.gritter.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/gritter-conf.js"></script>

    <!--script for this page-->
    <script src="<?php echo base_url(); ?>assets/js/sparkline-chart.js"></script>    
	<script src="<?php echo base_url(); ?>assets/js/zabuto_calendar.js"></script>	
	
	<script type="text/javascript">
        $(document).ready(function () {
            $("#date-popover").popover({html: true, trigger: "manual"});
            $("#date-popover").hide();
            $("#date-popover").click(function (e) {
                $(this).hide();
            });
        
            $("#my-calendar").zabuto_calendar({
                action: function () {
                    return myDateFunction(this.id, false);
                },
                action_nav: function () {
                    return myNavFunction(this.id);
                },
                ajax: {
                    url: "show_data.php?action=1",
                    modal: true
                },
                legend: [
                    {type: "text", label: "Special event", badge: "00"},
                    {type: "block", label: "Regular event", }
                ]
            });

            $("#divNiceScroll").niceScroll({styler:"fb",cursorcolor:"#2C73B6", cursorwidth: '3', cursorborderradius: '10px', background: '#404040', spacebarenabled:false, cursorborder: ''});
			$("#divNiceScrollTicket").niceScroll({styler:"fb",cursorcolor:"#2C73B6", cursorwidth: '3', cursorborderradius: '10px', background: '#404040', spacebarenabled:false, cursorborder: ''});
        });
        
        
        function myNavFunction(id) {
            $("#date-popover").hide();
            var nav = $("#" + id).data("navigation");
            var to = $("#" + id).data("to");
            console.log('nav ' + nav + ' to: ' + to.month + '/' + to.year);
        }
    </script>
  

  </body>
</html>
