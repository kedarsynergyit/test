
      <!--main content start--> 
      <section id="main-content">
          <section class="wrapper site-min-height">
              <div class=" pull-left" ><h3>PAYMENT HISTORY</h3></div>
              
			<!-- add invoice -->
			<?php /* ?><div class="pull-right" style="margin-right: 10px;">
				<h3><a href="addinvoice" class="btn btn-primary btn-sm btn-block" style="color:white;">Add Invoice</a></h3>
			</div><?php */ ?>
            
          	<div class="row mt">
          		<div class="col-lg-12">
          		       <!-- Advanced Tables -->
                    <div class="panel panel-default">

						<div class="panel-body">
							Select Invoice :
                        	<?php echo form_dropdown('fk_invoice_id', $invoices,$fk_invoice_id,'id="fk_invoice_id"'); ?>
						</div>

                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Invoice No</th>
                                            <th class="text-center" width="70%">Paid for Task</th>
                                            <th class="text-center">Payment Date</th>
                                            <th class="text-center">Amount Paid</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       
                                        <?php if(!empty($list)){ foreach($list as $row){ ?>
                                 
                                        <tr>
                                            <td class="text-center"><?php echo $row['invoice_number']; ?></td>
                                            <td class="text-left"><?php echo $row['task']; ?></td>
                                            <td class="text-center"><?php echo $row['paid_date']; ?></td>
                                            <td class="text-center"><?php echo $row['amount']; ?></td>               
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
 <script>
 $(document).ready(function () {
	$("#fk_invoice_id").on("change",function(){
		add_loader();
        location.href=location.pathname+"?fk_invoice_id="+$(this).val();
    });
 });
 </script>