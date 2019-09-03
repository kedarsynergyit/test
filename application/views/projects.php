  

<!-- **********************************************************************************************************************************************************
      MAIN CONTENT
      *********************************************************************************************************************************************************** -->
      <!--main content start--> 
      <section id="main-content">
          <section class="wrapper site-min-height">
          	<h3><i class="fa fa-angle-right"></i> PROJECTS</h3><?php //print_r($list); ?>
          	<div class="row mt">
          		<div class="col-lg-12">
          		       <!-- Advanced Tables -->
                    <div class="panel panel-default">

                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>Project Name</th>
                                            <th>All Tickets</th>
                                            <?php /* ?><th>Status</th><?php */ ?>
                                            <th>Open Tickets</th>
                                            <th>Closed/Completed Tickets</th>
                                            <th>Start Date</th>
                                            <th>Closed Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       
                                        <?php if(!empty($list)){ foreach($list as $row){ ?>
                                 
                                        <tr>
                                  
                                            <td><a href="task?id=<?php echo $row['id']; ?>"><?php echo $row['title']; ?></a></td>
                                            <td><a href="task?id=<?php echo $row['id']; ?>"><?php echo $row['nooftask']; ?></a></td>  
                                            <?php /* ?><td><a href="task?id=<?php echo $row['id']; ?>"><?php echo $row['status']; ?></a></td><?php */ ?>
                                            <td><a href="task?id=<?php echo $row['id']; ?>&show=open"><?php echo $row['open_task_count']; ?></a></td>
                                            <td><a href="task?id=<?php echo $row['id']; ?>&show=completed"><?php echo $row['completed_task_count']; ?></a></td>
                                            <td><a href="task?id=<?php echo $row['id']; ?>"><?php echo $row['created_by']; ?></a></td>
                                            <td><a href="task?id=<?php echo $row['id']; ?>"><?php echo $row['end_date']; ?></a></td>
                                    
                                        </tr>
                                 <?php } } ?>
                                       
                          
                                       
                                    </tbody>
                                </table>
                            </div>
                            
                        </div>
                    </div>
                    <!--End Advanced Tables -->
              
          		</div>
          	</div>
			
		</section><! --/wrapper -->
      </section><!-- /MAIN CONTENT -->

      <!--main content end-->
 