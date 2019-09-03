     <section id="main-content">
          <section class="wrapper site-min-height">
			<div class="pull-left"><h3><?php /* ?><i class="fa fa-chevron-circle-right"></i><?php */ ?> Add Category</h3></div>
			<div class="pull-right"><h3><a href="categories" class="btn btn-primary btn-sm btn-block">Back</a></h3></div>
           <div id="page-inner"> 
              <div class="row">
                <div class="col-lg-12">
                       <div class="text-center">
                            <div id="infoSuccess"><?php echo $this->session->flashdata('success');?></div>
                             <div id="infoError"><?php echo $this->session->flashdata('failed');?></div>
                        </div>
                    <div class="panel panel-default">
                     
                       
                        <div class="panel-body">
                            <?php echo form_open_multipart('portal/insert_category','class="form-horizontal" id="form_category"');?> 
                            <div class="row">
                                
                                <div class="col-lg-6 ml-15">
                                	
                                		<div class="form-group">
                                            <label>Category Name</label>
                                            <input type="text" name="name" id="name" class="form-control" id="name" placeholder="" maxlength="100">
                                            <div class="text-right hidden" id="e-name" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                		
                                        <div class="form-group">
	                                        <div class="pull-left"><button type="submit" name="submit" id="submit" class="btn btn-primary btn-sm btn-block">Submit</button></div>
                                        	<div class="pull-left" style="margin-left: 10px;"><button type="reset" name="reset" id="reset" class="btn btn-primary btn-sm btn-block">Reset</button></div>
                                    	</div>
                                </div>
                              
                               
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

<script type="text/javascript">
$(document).ready(function () {

	$("#submit").click(function() {   
	    var validate="no";
	    if($.trim($('#name').val())==""){ $('#e-name').removeClass("hidden");validate="yes";}  else{ $('#e-name').addClass("hidden");} 
	    
	    if(validate=='yes'){ return false; }else{  return true;
	    	$('#form_category').submit();
	    }
	});
});
</script>