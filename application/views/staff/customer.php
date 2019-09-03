     <section id="main-content">
          <section class="wrapper site-min-height">
          	<div class=" pull-left" ><h3><?php /* ?><i class="fa fa-angle-right"></i><?php */ ?>Customer Profiles</h3></div>
          	
          	<?php if($urole==1){ ?>
				<div class=" pull-right"><h3><a href="addcustomer" class="btn btn-primary btn-sm btn-block" style="color:white;">Add Customer </a></h3></div>
			<?php } ?>
          	
          	<div class="row mt">
          		<div class="col-lg-12">
                            <link href="<?php echo base_url(); ?>assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />

                    <!-- Advanced Tables -->
                    <div class="panel panel-default">
                      
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <?php /* ?><th width="6%">User ID</th><?php */ ?>
                                            <th width="10%">Company Name</th>
                                            <th width="8%">First Name</th>
                                            <th width="8%">Last Name</th>
                                            <th width="17%">Email</th>
                                            <th width="10%">Contact No.</th>
                                            <th width="12%">Account Manager</th>
                                            <?php /* ?><th width="12%">Project Manager</th><?php */ ?>
                                            <?php /* ?><th width="12%">Associated Members</th><?php */ ?>
                                            <th width="7%">Status</th>
                                            <?php if($urole==1){ ?>
                                            <th width="5%">Details</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                          <?php if(!empty($list)){ foreach($list as $row){ ?>
                                        <tr class="odd gradeX">
                                            <?php /* ?><td><?php echo $row['username']; ?></td><?php */ ?>
                                            <td><?php echo $row['companyname']; ?></td>                                           
                                            <td><?php echo $row['first_name']; ?></td>
                                            <td><?php echo $row['last_name']; ?></td>
                                            <td><?php echo $row['emailid']; ?></td>
                                            <td><?php echo $row['contactno']; ?></td>
                                            <td><?php echo $row['accountmanager']; ?></td>
                                            <?php /* ?><td><?php echo $row['projectmanager']; ?></td><?php */ ?>
                                            <?php /* ?><td><?php echo $row['associated_members'] ?></td><?php */ ?>
                                            <td class="center"><?php echo $row['status']; ?></td>
                                            <?php if($urole==1){ ?>
                                            <td class="center"><a href ="customerdetails?id=<?php echo $row['id']; ?>" > <i class="fa fa-bars"></i></a></td>
                                            <?php } ?>
                                        </tr>
                                          <?php } } ?>
                                    </tbody>
                                </table>
                            </div>
                            
                        </div>
                    </div>
                    <!--End Advanced Tables -->
             
    
  
             <!-- /. PAGE INNER  -->
            </div>
                    </div>
          	</div>
			
		</section><! --/wrapper -->
      </section><!-- /MAIN CONTENT -->

         <!-- /. PAGE WRAPPER  -->
     <!-- /. WRAPPER  -->
    
</body>
</html>
