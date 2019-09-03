     <section id="main-content">
          <section class="wrapper site-min-height">
			<div class="pull-left"><h3>Add Customer Company</h3></div>
			<div class="pull-right"><h3><a href="company" class="btn btn-primary btn-sm btn-block">Back</a></h3></div>
           <div id="page-inner"> 
              <div class="row">
                <div class="col-lg-12">
                       <div class="text-center">
                            <div id="infoSuccess"><?php echo $this->session->flashdata('success');?></div>
                             <div id="infoError"><?php echo $this->session->flashdata('failed');?></div>
                        </div>
                    <div class="panel panel-default">
                     
                       
                        <div class="panel-body">
                            <?php echo form_open_multipart('portal/insert_company','class="form-horizontal" id="myform"');?>
                            
                            <?php 
                            if($this->session->flashdata('error_message')) {
				   			?>
				   				<br /><center><span class="error_flash"><?php echo $this->session->flashdata('error_message'); ?></span></center>
				   			<?php 
                            }
							?>
                             
                            <div class="row">
                                
                                <div class="col-sm-5 col-md-5 col-lg-5 ml-15">
                                        <div class="form-group">
                                            <label>Company Name</label>
                                            <input type="hidden" class="form-control" name='id' id='id' value="">
                                            <input class="form-control" name='company' id='company' value="">
                                            <div class="text-right hidden" id="e-company"><span class="alert-danger">Required Field</span></div>
                                            <div class="text-right hidden" id="e-company-text-limit"><span class="alert-danger">Maximum 100 characters allowed!</span></div>
                                            <div class="text-right hidden" id="e-company-already-exists"><span class="alert-danger">Company name already exists!</span></div>
                                        </div>
                                      
                                    <div class="form-group">
                                        <div class="pull-left"><button type="submit" name="submit" id="submit" class="btn btn-primary btn-sm btn-block">Submit</button></div>
                                        <div class="pull-left" style="margin-left: 10px;"><button type="reset" name="reset" id="reset"  class="btn btn-primary btn-sm btn-block">Reset</button></div>
                                    </div>
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                                
                                <!-- /.col-lg-6 (nested) -->
                                <div class="col-sm-6 col-md-6 col-lg-6 ml-15">&nbsp;</div>
                               
                            </div>
                            <?php echo form_close(); ?>
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
          
			
		</section><! --/wrapper -->
      </section><!-- /MAIN CONTENT -->

         <!-- /. PAGE WRAPPER  -->
     <!-- /. WRAPPER  -->

 <script type="text/javascript">
     $(document).ready(function () {
        // check for duplicate company name
		$("#company").blur(function(){
			var companyname = $(this).val();

			var form_data = {companyname:companyname}; 
			$.ajax({
				url: "<?php echo site_url('portal/checkduplicatecompanyname'); ?>",
				type: 'POST',
				data: form_data,
				success: function(msg) {
					if(msg==1){
						$('#e-company-already-exists').removeClass("hidden"); validate="yes";
					}else{
						$('#e-company-already-exists').addClass("hidden");
					}
				}
			});
		});
  
    });
$("#submit").click(function() {   
    var validate="no";

    if($.trim($('#company').val())==""){ $('#e-company').removeClass("hidden"); validate="yes";} else{ $('#e-company').addClass("hidden"); }
    if($('#company').val().length>100){
		$('#e-company-text-limit').removeClass("hidden"); validate="yes";
	}else{ 
		$('#e-company-text-limit').addClass("hidden");
	}
    
    if(validate=='yes'){ 
        return false; 
    }else{  
        return true;
    	$('#myform').submit();
    }
});   


</script>
         <!-- Custom Js -->
      
   
</body>
</html>
