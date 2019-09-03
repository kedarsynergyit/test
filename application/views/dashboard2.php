   
      <!-- **********************************************************************************************************************************************************
      MAIN CONTENT
      *********************************************************************************************************************************************************** -->
      <!--main content start-->
      <section id="main-content">
          <section class="wrapper site-min-height-dash">

              <div class="row">
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
					  			<h4>All Task / Tickets</h4>
                  			</div>
					  			<p><a href="projects">Click Here for All Tickets / Projects.</a></p>
                  		</div>
                           
                  	
                  		<div class="col-md-3 col-sm-3 box0">
                  			<div class="box1">
					  			<span class=""><i class="fa fa-folder-open fa-6" aria-hidden="true"></i></span>
                                                              <h4><?php if(empty($open)){echo '0';} else{echo $open; }?></h4>
					  			<h4>Open Task / Tickets</h4>
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
                   <p class="newsp">               
                     <?php echo $each_news['news_text']; ?><a title="click here for more info" href="newsdetails?id=<?php echo $each_news['news_id']; ?>">...Read More</a>                  
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
                      		<?php echo $comm['comments']; ?> <a href ="taskdetails?id=<?php echo $comm['taskid']; ?>"  > <b> More...</b></a><br/>
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
                  
                   
              </div><! --/row -->
            
          </section>
		  
		  <div style="position:absolute;border:0px;">
           <div class="row" style="margin-right: 0px; margin-left: 0px; margin-top: 30px;">
              <div class="col-lg-12"><img src="<?php echo base_url(); ?>assets/img/banner.png" width="100%" height="100%"/></div> 
      
        </div>
		<div>
            <!--footer start-->
      <footer class="site-footer">
          <div class="text-center">
              
              2017-@PlatinaIt
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
     /*   $(document).ready(function () {
        var unique_id = $.gritter.add({
            // (string | mandatory) the heading of the notification
            title: 'Welcome to Dashgum!',
            // (string | mandatory) the text inside the notification
            text: 'Hover me to enable the Close Button. You can hide the left sidebar clicking on the button next to the logo. Free version for <a href="http://blacktie.co" target="_blank" style="color:#ffd777">BlackTie.co</a>.',
            // (string | optional) the image to display on the left
            image: '<?php// echo base_url(); ?>assets/img/ui-sam.jpg',
            // (bool | optional) if you want it to fade out on its own or just sit there
            sticky: true,
            // (int | optional) the time you want it to be alive for before fading out
            time: '',
            // (string | optional) the class name you want to apply to that specific message
            class_name: 'my-sticky-class'
        });

        return false;
        });*/
	</script>
	
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
