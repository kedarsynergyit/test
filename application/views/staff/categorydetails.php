     <section id="main-content">
          <section class="wrapper site-min-height">
           <div class="pull-left"><h3><?php /* ?><i class="fa fa-chevron-circle-right"></i><?php */ ?> Category details</h3></div>
           <div class="pull-right"><h3><a class="btn btn-primary btn-sm btn-block" href="categories" >Back</a></h3></div>
           <div id="page-inner"> 
              <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                      <div class="panel-body">
                            <?php echo form_open_multipart('portal/update_category','class="form-horizontal" id="myform"');?> 
                            <div class="row">
                                
                                <div class="col-lg-6 ml-15">
									<div class="form-group">
										<label>Category Name</label>
										<input type='hidden' name='id' id="id" value="<?php echo $details[0]->id; ?>" />
										<input class="form-control" name='name' id='name' value="<?php echo $details[0]->name; ?>" >
										<div class="text-right hidden" id="e-name"><span class="alert-danger">Required Field</span></div>
									</div>
                                        
									<div class="form-group">
										<div class="pull-left"><button type="submit" name="submit" id="submit" class="btn btn-primary btn-sm btn-block">Submit</button></div>
										<div class="pull-left" style="margin-left: 10px;"><button type="reset" name="reset" id="reset" class="btn btn-block btn-primary btn-sm">Cancel</button></div>
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
          
			
		</section><! --/wrapper -->
      </section><!-- /MAIN CONTENT -->

         <!-- /. PAGE WRAPPER  -->
     <!-- /. WRAPPER  -->
<!-- JS Scripts-->
<script src="<?php echo base_url(); ?>assets/js/jquery.js"></script>
<script>
$("#submit").click(function() {   
    var validate="no";
    if($('#name').val()==""){ $('#e-name').removeClass("hidden"); validate="yes";} else{$('#e-name').addClass("hidden");} 

    if(validate=='yes'){ return false; }else{  return true;
    	$('#myform').submit();
    }
});   
</script>