     <section id="main-content">
          <section class="wrapper site-min-height">
           <div class="pull-left"><h3><?php /* ?><i class="fa fa-chevron-circle-right"></i><?php */ ?> Invoice details</h3></div>
           <div class="pull-right"><h3><a class="btn btn-primary btn-sm btn-block" href="invoices" >Back</a></h3></div>
           <div id="page-inner"> 
              <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <?php echo form_open_multipart('portal/update_invoice','class="form-horizontal" id="myform"');?>
                            <div class="row">
                                
                                <div class="col-lg-6 ml-15">
                                        <div class="form-group">
                                            <label>Invoice Number</label>
                                            <input type='hidden' name='id' id="id" value="<?php echo $details[0]->id; ?>" />
                                            <input type="text" class="form-control" name='invoice_number' id='invoice_number' value="<?php echo $details[0]->invoice_number; ?>" >
                                            <div class="text-right hidden" id="e-invoice_number"><span class="alert-danger">Required Field</span></div>
                                        </div>  
                                        
                                        <div class="form-group">
                                            <label >Customer</label>              
                                            <?php echo form_dropdown('fk_client_id', $customer,$details[0]->fk_client_id,'class="form-control" id="fk_client_id" onChange="updateTickets(this);"'); ?>
                                            <div class="text-right hidden" id="e-fk_client_id" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label >Ticket :</label>              
                                            <div id="ticket_dropdown_div"><?php  echo form_dropdown('fk_task_id', $tickets,$details[0]->fk_task_id,'class="form-control" id="fk_task_id"'); ?></div>
                                            <div class="text-right hidden" id="e-fk_task_id" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        
                                        <?php /* ?><div class="form-group">
                                            <label>Task</label>
                                            <input type="text" class="form-control" name='task' id='task' value="<?php echo $details[0]->task; ?>" >
                                            <div class="text-right hidden" id="e-task"><span class="alert-danger">Required Field</span></div>
                                        </div><?php */ ?>
                                        
                                        <div class="form-group">
                                            <label>Invoice Date</label>
                                            <input type="text" name="dob"  class="datefieldwidth" id="dob" placeholder="Date"  value="<?php echo date("m/d/Y",strtotime($details[0]->invoice_date)); ?>" >&nbsp;<i class="fa fa-calendar" id="dobi"></i>            
                                            <div class="text-right hidden" id="e-dob" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Amount</label>
                                            <input type="text" class="form-control" name='amount' id='amount' value="<?php echo $details[0]->amount; ?>" readonly="readonly" >
                                            <div class="text-right hidden" id="e-amount"><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Remaining Amount</label>
                                            <input type="text" class="form-control" name='remaining_amount' id='remaining_amount' value="<?php echo $details[0]->remaining_amount; ?>" readonly="readonly" >
                                            <div class="text-right hidden" id="e-amount"><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        
                                        <?php 
                                        $total_received = 0;
                                        if(isset($received_payments) && !empty($received_payments)){
                                        	
                                         ?>
                                        <div class="form-group">
                                            <label>Payments Received</label>
                                            <table class="table table-bordered">
                                            	<thead>
                                            		<tr>
                                            			<th style="width:70%;">Task</th>
                                            			<th>Date</th>
                                            			<th>Amount</th>
                                            		</tr>
                                            	</thead>
                                            	<tbody>
                                            		<?php foreach ($received_payments as $eachpayment){ 
                                            			$total_received = intval($total_received)+intval($eachpayment->amount);
                                            		?>
                                            			<tr>
	                                            			<td><?php echo $eachpayment->task; ?></td>
	                                            			<td><?php echo date("d M, Y",strtotime($eachpayment->paid_date)); ?></td>
	                                            			<td><?php echo $eachpayment->amount; ?></td>
	                                            		</tr>
                                            		<?php } ?>
                                            	</tbody>
                                            </table>
                                        </div>
                                        <?php } ?>
                                        <input type="hidden" name="total_received" id="total_received" value="<?php echo $total_received; ?>" />
                                        
                                        <!-- add payment part starts -->
                                        <div class="row" id="addpayments">
                                        	<?php /* ?><div class="form-group">
                                        		<div class="col-lg-6">
                                        			<input type="text" class="form-control" name='payment_task[]' value="" placeholder="payment for task" />
                                        		</div>
                                        		<div class="col-lg-3">
                                        			<input type="text" name="payment_date[]" id="payment_date" class="form-control" placeholder="Date" />
                                        		</div>
                                        		<div class="col-lg-3">
                                        			<input type="text" class="form-control" name='payment_amount[]' value="" placeholder="amount" />
                                        		</div>
                                        	</div><?php */ ?>
                                        </div>
                                        <div class="form-group">  
                                            <input type="hidden" name="num_payment" id="num_payment" value="0" >
                                            <button type="button" class="btn btn-primary btn-xs" onclick="addMorePayment()">Add Payment</button>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label >Status :</label>              
                                            <select name="invoice_status" id="invoice_status" class="form-control">
                                            	<option value="">-- please select --</option>
                                            	<option value="U" <?php echo ($details[0]->invoice_status=="U")?'selected':''; ?>>Un-paid</option>
                                            	<option value="P" <?php echo ($details[0]->invoice_status=="P")?'selected':''; ?>>Paid</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>File input</label>
											<div class="ml-15" id="fileadd0">
											<input type="file" name='files[]' id="upload" class="pull-left">
	                                            <button class="btn btn-danger btn-xs" onclick="removefile(0)"><i class="fa fa-trash-o "></i></button> 
	                                        </div>
                                        </div>
                                        <div class="ml-15" id="addfiles">                                            
                                        </div>
                                    
                                        <div class="form-group">  
                                            <input type="hidden" name="num" id="num" value="1" >
                                            <button type="button" class="btn btn-default btn-xs" onclick="addMoreFiles()">Add More</button>
                                        </div>
                                      <?php  if(!empty($files)) { ?>
	                                   <div class="table-responsive">
	                                       <table class="table table-striped  table-hover"  >
	                                        <tr>
	                                              <?php $i=1; foreach ( $files as $eachfile){ ?>
												   
	                                                     
	                                            <td class="text-center imagetd" style="border-top:0px;"> 
												  <?php   $endexdt= pathinfo($eachfile->file, PATHINFO_EXTENSION);
	                                                     if($endexdt=='jpg'||$endexdt=='jpeg'||$endexdt=='png'||$endexdt=='gif'){ ?>
															<img data-toggle="modal" data-target="#myModal" src="<?php echo base_url(); ?><?php echo $eachfile->file; ?>"  width="100" height="100">
														 <?php } else { ?>
															<i  class="fa fa-file fa-3" aria-hidden="true"></i>
															<a target="_blank" href="<?php echo base_url().$eachfile->file; ?>">
																<b>Download <?php echo strtoupper($endexdt); ?> File</b>
															</a>
														 <?php } ?>
												</td>
	                                          <?php $i++; if($i==6){$i=1; echo '</tr>';} } if($i==5) { echo '</tr>'; } ?>
	                                        </tr>
	                                    </table>
	                                   </div>
                                    <?php } ?>
                               
                                   
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea class="form-control" rows="3" name="desc" id="desc"><?php echo $details[0]->description; ?></textarea>
                                            <label class="textarea-character-limit pull-right"><span id="txtarea_character_limit">150</span> characters left</label><br />
                            				<input type="hidden" value="150" id="txt_character_limit_hidden">
                                            <div class="text-right hidden" id="e-desc"><span class="alert-danger">Required Field</span></div>
                                        </div>
                                                                            
                                        
                                        <div class="form-group">
											<div class="pull-left"><button type="submit" name="submit" id="submit" class="btn btn-primary btn-sm btn-block">Submit</button></div>
											<div class="pull-left" style="margin-left: 10px;"><button type="reset" name="reset" id="reset"  class="btn btn-block btn-primary btn-sm">Cancel</button></div>
                                        </div>
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                               
                                <!-- /.col-lg-6 (nested) -->
                               
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
			
			</div>
             <!-- /. PAGE INNER  -->
          
			
		</section><!--/wrapper -->
      </section><!-- /MAIN CONTENT -->

         <!-- /. PAGE WRAPPER  -->
     <!-- /. WRAPPER  -->
     <!-- JS Scripts-->
     <script src="<?php echo base_url(); ?>assets/js/jquery.js"></script>


        <script>

function addMorePayment(){
	var addnum=$('#num_payment').val();
    var adnum=parseInt(addnum)+parseInt(1);
    $('#num_payment').val(adnum);

    var html = '<div class="form-group" id=paymentadd'+adnum+'><div class="col-lg-6"><input type="text" class="form-control" name="payment_task[]" value="" placeholder="payment for task" /></div><div class="col-lg-3"><input type="text" name="payment_date[]" id="payment_date'+adnum+'" class="form-control" placeholder="Date" /></div><div class="col-lg-3"><input type="text" class="form-control" myattr="payment_amount" name="payment_amount[]" value="" placeholder="amount" onkeyup="calculate_remaining_amount()" /></div></div>';

    $("#addpayments").append(html);

    // now also set date picker
    $("#payment_date"+adnum).datepicker();
    
    //$("#addpayments").append('<div class="form-group ml-15" id=paymentadd'+adnum+'><input type="file" name="files[]"  class="pull-left"><button class="btn btn-danger btn-xs" onclick="removefile('+adnum+')"><i class="fa fa-trash-o "></i></button></div>');
}

function calculate_remaining_amount(){
	var original_amount = $("#amount").val();
	var total_received = $("#total_received").val();
	var total_paid = 0;
	$('input[myattr=payment_amount]').each(function(){
		if($(this).val()!=""){
			total_paid = parseInt(total_paid)+parseInt($(this).val());
		}
	});

	var remaining_amount = parseInt(original_amount)-(parseInt(total_paid)+parseInt(total_received));
	
	$("#remaining_amount").val(remaining_amount);
}

function addMoreFiles(){
    var addnum=$('#num').val();
    var adnum=parseInt(addnum)+parseInt(1);
    $('#num').val(adnum);
    $("#addfiles").append('<div class="form-group ml-15" id=fileadd'+adnum+'><input type="file" name="files[]"  class="pull-left"><button class="btn btn-danger btn-xs" onclick="removefile('+adnum+')"><i class="fa fa-trash-o "></i></button></div>');
}
$("#submit").click(function() {   
    var validate="no";

    if($('#invoice_number').val()==""){ $('#e-invoice_number').removeClass("hidden"); validate="yes";} else{$('#e-invoice_number').addClass("hidden");}
    if($('#fk_client_id').val()==""){ $('#e-fk_client_id').removeClass("hidden");validate="yes";}  else{$('#e-fk_client_id').addClass("hidden");}
    if($('#fk_task_id').val()==""){ $('#e-fk_task_id').removeClass("hidden");validate="yes";}  else{$('#e-fk_task_id').addClass("hidden");}
    //if($('#task').val()==""){ $('#e-task').removeClass("hidden");validate="yes";}  else{$('#e-task').addClass("hidden");} 
    if($('#dob').val()==""){ $('#e-dob').removeClass("hidden");validate="yes";}  else{$('#e-dob').addClass("hidden");} 
    if($('#amount').val()==""){ $('#e-amount').removeClass("hidden");validate="yes";}  else{$('#e-amount').addClass("hidden");} 
    if($('#invoice_status').val()==""){ $('#e-invoice_status').removeClass("hidden");validate="yes";}  else{$('#e-invoice_status').addClass("hidden");} 
    if($('#desc').val()==""){ $('#e-desc').removeClass("hidden");validate="yes";}  else{$('#e-desc').addClass("hidden");}
    
    if(validate=='yes'){ return false; }else{  return true;
    $('#myform').submit();
    }
});   
function removefile(eleId)
{
    $( "div" ).remove( "#fileadd"+eleId );
}

$("#reset").click(function() { 
$(location).attr('href', 'invoices');
});

function updateTickets(customer){
	// add loader to show process
    add_loader();
    
	var customer_id = customer.value;
	if(customer_id!=""){
		 var form_data = {     
				 customer_id: customer_id
			}; 
			$.ajax({
	        url: "<?php echo site_url('portal/updateticketdropdown'); ?>",
	        type: 'POST',
	        async: true,
	        data: form_data,
	        success: function(msg) {
	            $('#ticket_dropdown_div').html(msg);

	         	// hide loader now
	      		remove_loader();
	        }
	    });
	}
}

$(document).ready(function () {

	var maxLength = $("#txt_character_limit_hidden").val();
    $('#desc').keyup(function() {
		var textlen = maxLength - $(this).val().length;
		if (textlen < 0) {
			$(this).val($(this).val().substring(0, maxLength));
		} else {
    		$("#txtarea_character_limit").html(textlen);
		}
    });

    $('#desc').keyup();
});
    </script>
         <!-- Custom Js -->

       
    
    
