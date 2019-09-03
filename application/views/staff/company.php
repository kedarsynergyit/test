     <section id="main-content">
          <section class="wrapper site-min-height">
          	<div class=" pull-left" ><h3>Customer Company</h3></div>
          	
          	<div class=" pull-right">
          		<h3>
          			<a href="addcompany" class="btn btn-primary btn-sm btn-block" style="color:white;">Add Customer Company</a>
          		</h3>
          	</div>
			
          	
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
                                            <th width="95%">Company Name</th>
                                            <th width="5%">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php if(!empty($list)){ foreach($list as $row){ ?>
                                        <tr class="odd gradeX">
                                            <td><?php echo $row['name']; ?></td>
                                            <td class="center"><a href ="companydetails?id=<?php echo $row['id']; ?>" > <i class="fa fa-bars"></i></a></td>
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
