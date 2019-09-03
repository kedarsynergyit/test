  

<!-- **********************************************************************************************************************************************************
      MAIN CONTENT
      *********************************************************************************************************************************************************** -->
      <!--main content start--> 
      <section id="main-content">
          <section class="wrapper site-min-height">
              <div class=" pull-left" ><h3><?php /* ?><i class="fa fa-angle-right"></i><?php */ ?> PROJECTS</h3><?php //print_r($list); ?></div>
              
              <!-- export button for admin user only -->
              <?php //if($urole==1){ ?>
              <div class="pull-right">
              	<h3><a href="exportprojects" target="_blank"; class="btn btn-success btn-sm btn-block"><i class="fa fa-file-excel-o"></i> Export</a></h3>
              </div>
              <?php //} ?>
              
              <!-- add project -->
              <?php /*if($internal_user_external_user!=2 && $urole!=1){ ?>
              <div class="pull-right" style="margin-right: 10px;">
              	<h3><a href="addproject" class="btn btn-primary btn-sm btn-block" style="color:white;">Add Project</a></h3>
              </div>
              <?php }*/ ?>
              
          	<div class="row mt">
          		<div class="col-lg-12">
          		       <!-- Advanced Tables -->
                    <div class="panel panel-default">

                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>Customer</th>
                                            <th>Project Name</th>
                                            <th>All Tickets</th>
                                            <?php /* ?><th>Status</th>
                                            <th>Role</th><?php */ ?>
                                            <th>Start Date</th>
                                            <th>Closed Date</th>
                                            <th>Status</th>
                                            <?php if($internal_user_external_user!=2 && $urole!=1){ ?>
                                          <th  class="center" align='center'> Edit Details</th>
                                          <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       
                                        <?php if(!empty($list)){ foreach($list as $row){ ?>
                                 
                                        <tr>
                                            <td><?php if($urole!=1){ ?><a href="task?id=<?php echo $row['id']; ?>"><?php } ?><?php echo $row['customer']; ?><?php if($urole!=1){ ?></a><?php } ?></td>
                                            <td><?php if($urole!=1){ ?><a href="task?id=<?php echo $row['id']; ?>"><?php } ?><?php echo $row['title']; ?><?php if($urole!=1){ ?></a><?php } ?></td>
                                            <td><?php if($urole!=1){ ?><a href="task?id=<?php echo $row['id']; ?>"><?php } ?><?php echo $row['nooftask']; ?><?php if($urole!=1){ ?></a><?php } ?></td>  
                                            
                                        	<?php /* ?><td><a href="task?id=<?php echo $row['id']; ?>"><?php echo $row['status']; ?></a></td>
                                            <td><a href="task?id=<?php echo $row['id']; ?>"><?php echo $row['role']; ?></a></td><?php */ ?>
                                              
                                            <td><?php if($urole!=1){ ?><a href="task?id=<?php echo $row['id']; ?>"><?php } ?><?php echo $row['created_by']; ?><?php if($urole!=1){ ?></a><?php } ?></td>
                                            <td><?php if($urole!=1){ ?><a href="task?id=<?php echo $row['id']; ?>"><?php } ?><?php echo $row['end_date']; ?><?php if($urole!=1){ ?></a><?php } ?></td>
                                            <td><?php if($urole!=1){ ?><a href="task?id=<?php echo $row['id']; ?>"><?php } ?><?php echo $row['status']; ?><?php if($urole!=1){ ?></a><?php } ?></td>
                                            
                                        	<?php if($internal_user_external_user!=2 && $urole!=1){ ?>
                                            	<td class="center" align='center'> <a href ="projectdetails?id=<?php echo $row['id']; ?>" > <i class="fa fa-edit fa-2x"></i></a></td>
                                            <?php } ?>
                                            
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
 