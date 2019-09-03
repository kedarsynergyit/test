     <section id="main-content">
          <section class="wrapper site-min-height">
          	<div class=" pull-left" ><h3><i class="fa fa-angle-right"></i>Customer List</h3></div>
                <div class=" pull-right"><h3><a href="addcustomer" class="btn btn-primary btn-sm btn-block" style="color:white;">Add Customer </a></h3></div>
          	
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
                                            <th>User ID</th>
                                            <th>Company Name</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Email</th>
                                            <th>Contact No.</th>
                                            <th>Account Manager</th>
                                            <th>Project Manager</th>
                                            <th>Status</th>
                                            <th>Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                          <?php if(!empty($list)){ foreach($list as $row){ ?>
                                        <tr class="odd gradeX">
                                            <td><?php echo $row['username']; ?></td>
                                            <td><?php echo $row['companyname']; ?></td>                                           
                                            <td><?php echo $row['first_name']; ?></td>
                                            <td><?php echo $row['last_name']; ?></td>
                                            <td><?php echo $row['emailid']; ?></td>
                                            <td><?php echo $row['contactno']; ?></td>
                                            <td><?php echo $row['accountmanager']; ?></td>
                                            <td><?php echo $row['projectmanager']; ?></td>
                                            <td class="center"><?php echo $row['status']; ?></td>
                                            <td class="center"><a href ="customerdetails?id=<?php echo $row['id']; ?>" > <i class="fa fa-bars"></i></a></td>
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
    <!-- JS Scripts-->
       <script src="<?php echo base_url(); ?>assets/js/jquery.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery-1.8.3.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
     <!-- DATA TABLE SCRIPTS -->
    <script src="<?php echo base_url(); ?>assets/js/dataTables/jquery.dataTables.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/dataTables/dataTables.bootstrap.js"></script>
        <script>
            $(document).ready(function () {
               // $('#dataTables-example').dataTable();
            });
    </script>
         <!-- Custom Js -->
      
   
</body>
</html>
