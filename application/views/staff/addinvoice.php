     <section id="main-content">
          <section class="wrapper site-min-height">
			<div class="pull-left"><h3><?php /* ?><i class="fa fa-chevron-circle-right"></i><?php */ ?> Add Invoice</h3></div>
			<div class="pull-right"><h3><a class="btn btn-primary btn-sm btn-block" href="invoices" >Back</a></h3></div>
           <div id="page-inner"> 
              <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                      
                        <div class="panel-body">
                            <?php echo form_open_multipart('portal/insert_invoice','class="form-horizontal" id="myform"');?>
                            <div class="row">
                                
                                <div class="col-lg-6 ml-15">
                                        <div class="form-group">
                                           <label>Invoice Number</label>
                                           <input class="form-control" name='invoice_number' id='invoice_number' maxlength="100">
                                           <div class="text-right hidden" id="e-invoice_number"><span class="alert-danger">Required Field</span></div>
                                        </div>
                                                                              
                                        <div class="form-group">
                                            <label>Customer</label>
                                            <?php echo form_dropdown('fk_client_id', $customer,'',' id="fk_client_id"  class="form-control" onChange="updateTickets(this);" '); ?>
                                            <div class="text-right hidden" id="e-fk_client_id" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label >Ticket :</label>              
                                            <div id="ticket_dropdown_div">-- please select customer to see the list of tickets --<?php  //echo form_dropdown('fk_task_id', $fk_task_ids,'','class="form-control" id="fk_task_id"'); ?></div>
                                            <div class="text-right hidden" id="e-fk_task_id" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        
                                        <?php /* ?><div class="form-group">
                                           <label>Task</label>
                                           <input class="form-control" name='task' id='task'>
                                           <div class="text-right hidden" id="e-task"><span class="alert-danger">Required Field</span></div>
                                        </div><?php */ ?>
                                        
                                        <div class="form-group">
                                            <label>Invoice Date</label>
                                            <input type="text" name="dob" class="datefieldwidth" id="dob" placeholder="Date">&nbsp;<i class="fa fa-calendar" id="dobi"></i>            
                                            <div class="text-right hidden" id="e-dob" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        
                                        <div class="form-group">
                                           <label>Amount</label>
                                           <input class="form-control" name='amount' id='amount'>
                                           <div class="text-right hidden" id="e-amount"><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        
                                       	<div class="form-group">
                                            <label>Status</label>              
                                            <select name="invoice_status" id="invoice_status" class="form-control">
                                            	<option value="">-- please select --</option>
                                            	<option value="U">Un-paid</option>
                                            	<option value="P">Paid</option>
                                            </select>
                                            <div class="text-right hidden" id="e-invoice_status"><span class="alert-danger">Required Field</span></div>
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
                                        
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea class="form-control" rows="3" name="desc" id="desc"></textarea>
                                            <label class="textarea-character-limit pull-right"><span id="txtarea_character_limit">150</span> characters left</label><br />
                            				<input type="hidden" value="150" id="txt_character_limit_hidden">
                                            <div class="text-right hidden" id="e-desc"><span class="alert-danger">Required Field</span></div>
                                        </div>
                                                                            
                                        <div class="form-group">
                                            <div class="pull-left"><button type="submit" name="submit" id="submit" class="btn btn-primary btn-sm btn-block">Submit</button></div>
                                            <div class="pull-left" style="margin-left: 10px;"><button type="reset" class="btn btn-primary btn-sm btn-block">Reset</button></div>
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
});
    </script>
         <!-- Custom Js -->

       
    
    
