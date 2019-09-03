
      <!--main content start--> 
      <section id="main-content">
          <section class="wrapper site-min-height">
              <div class=" pull-left" ><h3>INVOICES</h3></div>
              
			<!-- add invoice -->
			<?php /* ?><div class="pull-right" style="margin-right: 10px;">
				<h3><a href="addinvoice" class="btn btn-primary btn-sm btn-block" style="color:white;">Add Invoice</a></h3>
			</div><?php */ ?>
              
          	<div class="row mt">
          		<div class="col-lg-12">
          		       <!-- Advanced Tables -->
                    <div class="panel panel-default">

                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Invoice No</th>
                                            <th class="text-center">Client Name</th>
                                            <th class="text-center">Ticket ID</th>
                                            <th class="text-center" width="40%">Tasks</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Invoice<br />Amount</th>
                                            <th class="text-center">Remaining<br />Amount</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       
                                        <?php if(!empty($list)){ foreach($list as $row){ ?>
                                 
                                        <tr>
                                            <td class="text-center"><?php echo $row['invoice_number']; ?></td>
                                            <td class="text-center"><?php echo $row['customer']; ?></td>
                                            <td class="text-center">
                                            	<?php 
                                            	if(isset($row['fk_task_id']) && !empty($row['fk_task_id']) && isset($row['show_to_customer']) && $row['show_to_customer']==0){
                                            		?>
                                            		<a href="taskdetails?id=<?php echo $row['fk_task_id']; ?>" target="_blank">
                                            			<?php echo $row['ticket_id']; ?>
                                            		</a>
                                            		<?php
                                            	}else{
                                            		echo $row['ticket_id'];
                                            	}
                                            	?>
                                            </td>
                                            <td class="text-left"><?php echo $row['task']; ?></td>
                                            <td class="text-center"><?php echo $row['invoice_date']; ?></td>
                                            <td class="text-center"><?php echo $row['amount']; ?></td>
                                            <td class="text-center"><?php echo $row['remaining_amount']; ?></td>
                                            <td class="text-center"><?php echo $row['invoice_status']; ?></td>               
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
			
		</section> <!--/wrapper -->
      </section><!-- /MAIN CONTENT -->

      <!--main content end-->
 