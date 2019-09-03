   
      <!-- **********************************************************************************************************************************************************
      MAIN CONTENT
      *********************************************************************************************************************************************************** -->
      <!--main content start-->
      <section id="main-content"  >
          <section class="wrapper site-min-height-dash" style="min-height: 550px;">
				
				<?php if($urole!=1){
					// this view is for users other than admin
				?>
					
					<?php
						if($internal_user_external_user==1){
							// internal user
							
							?>
							<!-- FIRST ROW -->
							<div class="row">
								
								<div class="col-lg-4">
									<a href="workorders">
										<div class="box1">
											<span><i class="fa fa-file-pdf-o" aria-hidden="true" style="font-size: 70px;"></i></span>
											<h4 style="font-size: 30px;"><?php echo $countofworkorders; ?></h4>
											<h4 style="font-size: 20px;">Open Workorders</h4>
										</div>
									</a>
								</div>
								
								<!-- leave blank for empty space -->
								<div class="col-lg-4">
									<a href="alltask?ticket=open">
										<div class="box1">
											<span><i class="fa fa-book" aria-hidden="true" style="font-size: 70px;"></i></span>
											<h4 style="font-size: 30px;"><?php echo $open; ?></h4>
											<h4 style="font-size: 20px;">Open Tickets</h4>
										</div>
									</a>
								</div>
								
								<div class="col-lg-4">
									<a href="projects">
										<div class="box1">
											<span><i class="fa fa-book" aria-hidden="true" style="font-size: 70px;"></i></span>
											<h4 style="font-size: 30px;"><?php echo $countofprojects; ?></h4>
											<h4 style="font-size: 20px;">Open Projects</h4>
										</div>
									</a>
								</div>
		
							</div>
							
							<!-- SECOND ROW -->
							<?php /* ?><div class="row">
								
								<!-- leave blank for empty space -->
								<div class="col-lg-4">
									&nbsp;
								</div>
								
								<div class="col-lg-4">
									<a href="alltask?ticket=open">
										<div class="box1">
											<span><i class="fa fa-book" aria-hidden="true" style="font-size: 70px;"></i></span>
											<h4 style="font-size: 30px;"><?php echo $open; ?></h4>
											<h4 style="font-size: 20px;">Open Tickets</h4>
										</div>
									</a>
								</div>
								
								<!-- leave blank for empty space -->
								<div class="col-lg-4">
									&nbsp;
								</div>
		
							</div><?php */ ?>
							
							<!-- THIRD ROW -->
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
								
								<!-- leave blank for empty space -->
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
							<?php
							
						}else{
							// external user
							
							?>
							<!-- FIRST ROW -->
							<div class="row">
								
								<div class="col-lg-4">
									<a href="workorders">
										<div class="box1">
											<span><i class="fa fa-file-pdf-o" aria-hidden="true" style="font-size: 70px;"></i></span>
											<h4 style="font-size: 30px;"><?php echo $countofworkorders; ?></h4>
											<h4 style="font-size: 20px;">Open Workorders</h4>
										</div>
									</a>
								</div>
								
								<!-- leave blank for empty space -->
								<div class="col-lg-4">&nbsp;</div>
								
								<div class="col-lg-4">
									<a href="alltask?ticket=open">
										<div class="box1">
											<span><i class="fa fa-ticket" aria-hidden="true" style="font-size: 70px;"></i></span>
											<h4 style="font-size: 30px;"><?php echo $open; ?></h4>
											<h4 style="font-size: 20px;">Open Tickets</h4>
										</div>
									</a>
								</div>
		
							</div>
							
							<!-- SECOND ROW -->
							<div class="row">
								
								<!-- leave blank for empty space -->
								<div class="col-lg-4">&nbsp;</div>
								
								<div class="col-lg-4">
									<a href="tasknotifications">
										<div class="box1">
											<span><i class="fa fa-file" aria-hidden="true" style="font-size: 70px;"></i></span>
											<h4 style="font-size: 30px;"><?php echo $ticket_changes_notification; ?></h4>
											<h4 style="font-size: 20px;">Open Notifications</h4>
										</div>
									</a>
								</div>
								
								<!-- leave blank for empty space -->
								<div class="col-lg-4">&nbsp;</div>
		
							</div>
							
							<!-- THIRD ROW -->
							<div class="row">
								
								<div class="col-lg-4">
									<a href="messages">
										<div class="box1">
											<span><i class="fa fa-commenting-o" aria-hidden="true" style="font-size: 70px;"></i></span>
											<?php if(!empty($new_messages)){ ?>
												<h4 style="color: #330099;"><?php echo $new_messages.' New Messages'; ?></h4>
												<?php } ?>
											<h4 style="font-size: 20px;">Message Box</h4>
										</div>
									</a>
								</div>
								
								<!-- leave blank for empty space -->
								<div class="col-lg-4">&nbsp;</div>
								
								<div class="col-lg-4">
									<a href="settings">
										<div class="box1">
											<span><i class="fa fa-cog" aria-hidden="true" style="font-size: 70px;"></i></span>
											<h4 style="font-size: 20px;">Settings</h4>
										</div>
									</a>
								</div>
		
							</div>
							<?php
							
						}
					?>
					
					<!-- the upper section -->
					<?php /* ?><div class="row">
						<div class="col-lg-12 ds">
							<div class="row">
								<!-- Open Workorders -->
								<div class="col-lg-3 ds">
									<div class="box1">
										<span class=""><i class="fa fa-file-pdf-o fa-6" aria-hidden="true" style="font-size: 50px;"></i></span>
										<h4><?php echo $countofworkorders; ?></h4>
										<h4>Open Workorders</h4>
									</div>
									<p style="text-align:center;"><a href="workorders">Click Here for Open Workorders</a></p>
								</div>
								
								<!-- open projects -->
								<div class="col-lg-3 ds">
									<div class="box1">
										<span class=""><i class="fa fa-book fa-6" aria-hidden="true" style="font-size: 50px;"></i></span>
										<h4><?php echo $countofprojects; ?></h4>
										<h4>Open Projects</h4>
									</div>
									<?php
										//$href = ($internal_user_external_user!=2)?"projects":"alltask";
									?>
									<p style="text-align:center;"><a href="<?php echo 'projects';//$href; ?>">Click Here for Open Projects</a></p>
								</div>
								
								<!-- Open tickets button -->
								<div class="col-lg-3 ds">
									<div class="box1">
										<span class=""><i class="fa fa-ticket fa-6" aria-hidden="true" style="font-size: 50px;"></i></span>
										<h4><?php if(empty($open)){echo '0';} else{echo $open; }?></h4>
										<h4>Open Tickets</h4>
									</div>
									<p style="text-align:center;"><a href="alltask?ticket=open">Click Here For Open Tickets</a></p>
								</div>
								
								<!-- Message Box -->
								<div class="col-lg-3 ds">
									<div class="box1">
										<span class=""><i class="fa fa fa-commenting-o fa-6" aria-hidden="true" style="font-size: 50px;"></i></span>
										<h4 style="color: #330099;"><a href="messages"><?php echo (!empty($new_messages))?$new_messages.' New Messages':'&nbsp;'; ?></a></h4>
										<h4>Message Box</h4>
									</div>
									<p style="text-align:center;"><a href="messages">Click Here for Message Box</a></p>
								</div>
							</div>
						</div>
					</div><?php */ ?>
					
					<!-- lower section -->
					<?php /* ?><div class="row">
						<div class="col-lg-12 ds">
							<div class="row">
								<!-- workorder comments -->
								<div class="col-lg-4 ds">
									<h3>WORKORDER COMMENTS</h3>
									<!-- First Action -->
									<div id="divNiceScroll" style="height:300px !important; overflow:auto;">
										<?php if(!empty($workorder_comments)){ foreach ($workorder_comments as $comm){ ?>
										<div class="desc">
											<div>
												<?php if($comm['commentedby']==1){ $noti_color='badge bg-theme';} else{$noti_color='badge bg-theme05';} ?>
												<span class="<?php echo $noti_color; ?>"><i class="fa fa-comments-o"></i></span>
												<?php echo "Workorder Number : ".$comm['workorder_number']; echo (isset($comm['customer_name']) && !empty($comm['customer_name']))?"<br />(".$comm['customer_name'].")":""; ?>
					                      	</div>
					                      	<div class="details">
					                      		<p><muted><?php echo date('Y-m-d (h:i a)',strtotime($comm['created_on'])); ?></muted><br/>
					                      		<?php echo $comm['comments']; ?><a href ="workorderdetails?id=<?php echo $comm['fk_workorder_id']; ?>"  > <b>  More...</b></a><br/>
					                      		</p>
												<p class="text-right">  <a href="#"><?php echo $comm['commented_by']; ?></a></p>
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
										<?php } ?>
									</div>
								</div>
								
								<!-- Chat Updates -->
								<div class="col-lg-4 ds">
									<h3>CHAT UPDATES</h3>
									
									<div id="dashboard_chat_notifications" style="height:300px !important; overflow:auto;">
										
									</div>
								</div>
								
								<!-- Task Comments -->
								<div class="col-lg-4 ds">
									<h3>TICKETS NOTIFICATIONS</h3>
									<div id="divNiceScrollTicket" style="height:300px !important; overflow:auto;">
										<?php if(!empty($taskcomm)){ foreach ($taskcomm as $comm){?>
											<div class="desc">
												<div class="thumb">
													<?php if($comm['commentedby']==1){ $noti_color='badge bg-theme';} else{$noti_color='badge bg-theme05';} ?>
													<span class="<?php echo $noti_color; ?>"><i class="fa fa-comments-o"></i></span>
													
													<?php if(!empty($last_comment_id) && ($comm['chat_id']>$last_comment_id)){ ?><span class="new_comment_alert">new</span><?php } ?>
												</div>
												<div class="details">
													<p><muted><?php echo "(".$comm['taskid'].")"; ?>&nbsp;<?php echo date('Y-m-d  (h:i a)',strtotime($comm['created_on'])); ?></muted><br/>
														<span class="each_comment_display"><?php echo $comm['comments']; ?></span><a href ="taskdetails?id=<?php echo $comm['task_id']; ?>"  > <b>  More...</b></a><br/>
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
							</div>
						</div>
					</div><?php */ ?>
				
				<?php }else if($urole==1){
					// this view is for admin
					?>
					<?php /* ?><div class="row">
						<div class="col-lg-6 text-center">
							<i class="fa fa-user" aria-hidden="true" style="font-size: 200px;"></i>
						</div>
						<div class="col-lg-6">
							
							<div class="row">
								<div class="col-lg-12">
									
									<div class="row">
										<div class="col-lg-6">
											<a href="users">
												<div class="box1">
													<span><i class="fa fa-user-circle-o" aria-hidden="true" style="font-size: 50px;"></i></span>
													<h4><?php echo $total_profiles; ?></h4>
													<h4>Total Profiles</h4>
												</div>
											</a>
										</div>
										<div class="col-lg-6">
											<a href="projects">
												<div class="box1">
													<span class=""><i class="fa fa-book" aria-hidden="true" style="font-size: 50px;"></i></span>
													<h4><?php echo $countofprojects; ?></h4>
													<h4>Total Projects</h4>
												</div>
											</a>
										</div>
									</div>
									
									<div class="row">
										<div class="col-lg-6">
											<div class="box1">
												<span><i class="fa fa-commenting-o" aria-hidden="true" style="font-size: 50px;"></i></span>
												<h4>Message Box</h4>
											</div>
										</div>
										<div class="col-lg-6">
											<a href="settings">
												<div class="box1">
													<span class=""><i class="fa fa-cog" aria-hidden="true" style="font-size: 50px;"></i></span>
													<h4>Settings</h4>
												</div>
											</a>
										</div>
									</div>
									
								</div>
							</div>
							
						</div>
					</div><?php */ ?>
					
					
					<div class="col-lg-12">
									
						<div class="row">
							<div class="col-lg-6">
								<a href="employees">
									<div class="box1">
										<span><i class="fa fa-user-circle-o" aria-hidden="true" style="font-size: 100px;"></i></span>
										<h4 style="font-size: 50px;"><?php echo $total_profiles; ?></h4>
										<h4 style="font-size: 30px;">Total Profiles</h4>
									</div>
								</a>
							</div>
							<div class="col-lg-6">
								<a href="projects">
									<div class="box1">
										<span class=""><i class="fa fa-book" aria-hidden="true" style="font-size: 100px;"></i></span>
										<h4 style="font-size: 50px;"><?php echo $countofprojects; ?></h4>
										<h4 style="font-size: 30px;">Total Projects</h4>
									</div>
								</a>
							</div>
						</div>
						
						<div class="row">
							<div class="col-lg-6">
								<a href="messages">
									<div class="box1">
										<span><i class="fa fa-commenting-o" aria-hidden="true" style="font-size: 100px;"></i></span>
										<?php if(!empty($new_messages)){ ?>
										<h4 style="color: #330099;"><?php echo $new_messages.' New Messages'; ?></h4>
										<?php } ?>
										<h4 style="font-size: 30px;">Message Box</h4>
									</div>
								</a>
							</div>
							<div class="col-lg-6">
								<a href="settings">
									<div class="box1">
										<span class=""><i class="fa fa-cog" aria-hidden="true" style="font-size: 100px;"></i></span>
										<h4 style="font-size: 30px;">Settings</h4>
									</div>
								</a>
							</div>
						</div>
						
					</div>
					<?php
				} ?>
				
				<!-- blog section -->
				<?php /* ?><div class="row">
					<div class="col-lg-12 ds">
						<div class="news">
							<h3>BLOG UPDATES</h3>
							<div id="divNiceScrollBlog" style="height:300px !important; overflow:auto;">
								<?php if(!empty($news)){ foreach ($news as $each_news){?>
									<div class="desc newsdetails" style="height:350px !important; overflow:auto;">
										<div class="thumb">
											<span class="badge bg-theme"><i class="fa fa-newspaper-o"></i></span> <muted><?php echo date('Y-m-d h:m A',strtotime($each_news['created_on'])); ?></muted>
										</div>
										<div class="details">
											<p class="newsp text-overflow-wrap">               
												<?php echo (strlen($each_news['news_text'])>150)?substr($each_news['news_text'], 0, 147):$each_news['news_text']; ?>
												<?php if(strlen($each_news['news_text'])>150){ ?>
												<a title="click here for more info" href="newsdetailsview?id=<?php echo $each_news['news_id']; ?>">...more</a>
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
					</div>
				</div><?php */ ?>
				
				<?php 
				/**
				 * BELOW CODE IS OLD
				 */
				?>
				
              <?php /* ?><div class="row"><?php */ ?>
				
				<?php /* ?><div class="col-lg-3 ds">
					<h3>WORKORDER COMMENTS</h3>
					<!-- First Action -->
					<?php if(!empty($workorder_comments)){ foreach ($workorder_comments as $comm){ ?>
					<div class="desc">
						<div>
							<?php if($comm['commentedby']==1){ $noti_color='badge bg-theme';} else{$noti_color='badge bg-theme05';} ?>
							<span class="<?php echo $noti_color; ?>"><i class="fa fa-comments-o"></i></span>
							<?php echo "Workorder Number : ".$comm['workorder_number']; echo (isset($comm['customer_name']) && !empty($comm['customer_name']))?"<br />(".$comm['customer_name'].")":""; ?>
                      	</div>
                      	<div class="details">
                      		<p><muted><?php echo date('Y-m-d (h:i a)',strtotime($comm['created_on'])); ?></muted><br/>
                      		<?php echo $comm['comments']; ?><a href ="workorderdetails?id=<?php echo $comm['fk_workorder_id']; ?>"  > <b>  More...</b></a><br/>
                      		</p>
							<p class="text-right">  <a href="#"><?php echo $comm['commented_by']; ?></a></p>
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
                      <?php } ?>       
				</div><!-- /col-lg-3 --><?php */ ?>
                  
                  <?php /* ?><div class="col-lg-6 main-chart">
                      
                       	<div class="row mtbox">
                  		<div class="col-md-4 col-sm-4 col-md-offset-1 box0">
                  			<div class="box1">
					  			<span class="li_data"></span>
                                                                <h4><?php echo $countoftask; ?></h4>
					  			<h4>All Tickets</h4>
                  			</div>
                  			<?php
                  				$href = ($internal_user_external_user!=2)?"projects":"alltask";
                  				$link_text =  ($internal_user_external_user!=2)?"Click Here for All Tickets / Projects.":"Click Here for All Tickets.";
                  			?>
					  		<p><a href="<?php echo $href; ?>"><?php echo $link_text; ?></a></p>
                  		</div>
                           
                  	
                  		<div class="col-md-4 col-sm-4 box0">
                  			<div class="box1">
					  			<span class=""><i class="fa fa-folder-open fa-6" aria-hidden="true"></i></span>
                                                                <h4><?php if(empty($open)){echo '0';} else{echo $open; }?></h4>
					  			<h4>Open Tickets</h4>
                  			</div>
					  			<p><a href="alltask?ticket=open">Click Here For Open Tickets</a></p>
                  		</div>
                  	
                  	</div><!-- /row mt -->	
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
                     	<a title="click here for more info" href="newsdetailsview?id=<?php echo $each_news['news_id']; ?>">...more</a>
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
                      
                   	
                      <!--custom chart end-->
					</div><!-- /row --><?php */ ?>
                        
      <!-- **********************************************************************************************************************************************************
      RIGHT SIDEBAR CONTENT
      *********************************************************************************************************************************************************** -->                  
                  
                  <?php /* ?><div class="col-lg-3 ds">
                    <!--COMPLETED ACTIONS DONUTS CHART-->
						<h3>TICKETS NOTIFICATIONS</h3>
                                        
                      <!-- First Action -->
                      
               <?php if(!empty($taskcomm)){ foreach ($taskcomm as $comm){?>
                          <div class="desc">
                      	<div class="thumb">
						<?php if($comm['commentedby']==1){ $noti_color='badge bg-theme';} else{$noti_color='badge bg-theme05';} ?>
                      		<span class="<?php echo $noti_color; ?>"><i class="fa fa-comments-o"></i></span>
                      	<?php echo "(".$comm['taskid'].")"; ?>
                      	</div>
                      	<div class="details">
                      		<p><muted><?php echo date('Y-m-d  (h:i a)',strtotime($comm['created_on'])); ?></muted><br/>
                      		<?php echo $comm['comments']; ?><a href ="taskdetails?id=<?php echo $comm['taskid']; ?>"  > <b>  More...</b></a><br/>
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
               
                     
                      
                  </div><!-- /col-lg-3 --><?php */ ?>
                  
                   
              <?php /* ?></div><!--/row --><?php */ ?>
            
          </section>
		  
		
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
	
	<script type="application/javascript">
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

			// call the chat notification function on page load
			check_chat_project();

			// add nice scroll instead of normal scroll
			$("#divNiceScroll").niceScroll({styler:"fb",cursorcolor:"#2C73B6", cursorwidth: '3', cursorborderradius: '10px', background: '#404040', spacebarenabled:false, cursorborder: ''});
			$("#divNiceScrollTicket").niceScroll({styler:"fb",cursorcolor:"#2C73B6", cursorwidth: '3', cursorborderradius: '10px', background: '#404040', spacebarenabled:false, cursorborder: ''});
			//$("#divNiceScrollBlog").niceScroll({styler:"fb",cursorcolor:"#2C73B6", cursorwidth: '3', cursorborderradius: '10px', background: '#404040', spacebarenabled:false, cursorborder: ''});
			$("#dashboard_chat_notifications").niceScroll({styler:"fb",cursorcolor:"#2C73B6", cursorwidth: '3', cursorborderradius: '10px', background: '#404040', spacebarenabled:false, cursorborder: ''});
        });
        
		function check_chat_project(){
		var data={
				"project_id":$("#project_id").val(),
				"txt_search":$("#txt_search").val()
			};
			$.ajax({
				url: "<?php echo site_url('portal/dashboard_chat_notifications'); ?>",
				type: 'POST',
	        	data: data,
	        	async: true,
	        	success: function(response) {
					$('#dashboard_chat_notifications').html(response);
	        	}
			});

			setTimeout(check_chat_project, 3000);
		}

		
        
        function myNavFunction(id) {
            $("#date-popover").hide();
            var nav = $("#" + id).data("navigation");
            var to = $("#" + id).data("to");
            console.log('nav ' + nav + ' to: ' + to.month + '/' + to.year);
        }
    </script>
  

  </body>
</html>
