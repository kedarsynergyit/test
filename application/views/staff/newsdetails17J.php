     <section id="main-content">
          <section class="wrapper site-min-height">
           <h3>
               <i class="fa fa-chevron-circle-right"></i> Blog Details
                  </h3>
                  <p class="text-left"><a href="news" ><span class="badge bg-theme">Back</span></a></p>
           <div id="page-inner"> 
              <div class="row">
                <div class="col-lg-12">
                       <div class="text-center">
                            <div id="infoSuccess"><?php echo $this->session->flashdata('success');?></div>
                             <div id="infoError"><?php echo $this->session->flashdata('failed');?></div>
                        </div>
                    <div class="panel panel-default">
                     
                        <div class="panel-body">
                            <?php echo form_open_multipart('portal/update_news','class="form-horizontal" id="newsForm"');?> 
                            <div class="row">
                                
                                <div class="col-lg-6 ml-15">
                                      <input type='hidden' name='id' id="id" value="<?php echo $details[0]->id; ?>" />
                                        <div class="form-group">
                                            <label>Blog Description</label>
											<input class="form-control right" name='news_title' id='news_title' value="<?php echo $details[0]->notification; ?>">                                             
                                            <div class="text-right hidden" id="e-news_title" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>From Date</label>
                                            <input type="text" name="news_from_date" class="datefieldwidth" id="news_from_date" placeholder="From Date" value="<?php echo date("m/d/Y",strtotime($details[0]->from)); ?>">&nbsp;<i class="fa fa-calendar" id="news_from_datei"></i>            
                                            <div class="text-right hidden" id="e-news_from_date" ><span class="alert-danger">Required Field</span></div>
                                        </div>
										
										<div class="form-group">
                                            <label>To Date</label>
                                            <input type="text" name="news_to_date" class="datefieldwidth" id="news_to_date" placeholder="To Date" value="<?php echo date("m/d/Y",strtotime($details[0]->to)); ?>">&nbsp;<i class="fa fa-calendar" id="news_to_datei"></i>            
                                            <div class="text-right hidden" id="e-news_to_date" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Show Customers?</label><br/>
                                            <input type="radio" name="cust_show" id="cust_show" value="0" <?php echo ($details[0]->show_customer==0)?'checked="checked"':""; ?>>No &nbsp;
                                            <input type="radio" name="cust_show" id="cust_show" value="1" <?php echo ($details[0]->show_customer==1)?'checked="checked"':""; ?>>Yes
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Show Users?</label><br/>
                                            <input type="radio" name="user_show" id="user_show" value="0" <?php echo ($details[0]->show_user==0)?'checked="checked"':""; ?>>No &nbsp;
                                            <input type="radio" name="user_show" id="user_show" value="1" <?php echo ($details[0]->show_user==1)?'checked="checked"':""; ?>>Yes
                                        </div>
										
                                     </div>
                                <!-- /.col-lg-6 (nested) -->
                                <div class="col-lg-4 ml-15">
                                     <label>File</label> 
                                      <div class="ml-15">
                                       <input type="file" name='files' id="upload" class="form-group  ml-15"> 
                                      </div>
                                      <?php if(!empty($details[0]->image)){ ?>
                                      
                                      	<?php
                                        $endexdt= pathinfo($details[0]->image, PATHINFO_EXTENSION);
                                        if($endexdt=='jpg'||$endexdt=='jpeg'||$endexdt=='png'||$endexdt=='gif'){ 
                                        ?>
                                        	<img data-toggle="modal" data-target="#myModal" src="<?php echo base_url(); ?><?php echo $details[0]->image; ?>"  width="100" height="100">
                                        <?php
                                        } else { ?>
											<i  class="fa fa-file fa-3" aria-hidden="true"></i> <a target="_blank" href="<?php echo base_url().$details[0]->image; ?>"><b>Download <?php echo strtoupper($endexdt); ?> File</b></a><?php 
                                        } 
										?>
                                      	
                                      <?php } ?>
                                      <input type='hidden' name='path' id="path" value="<?php echo base_url(); ?><?php echo $details[0]->image; ?>" />
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                               
                            </div>
                            <!-- /.row (nested) -->
                                    <div  class="text-center">
                                        <button type="submit" name="submit" id="submit" class="btn btn-round btn-success btn-sm">Submit</button>
                                        <button type="reset" name="reset" id="reset"  class="btn btn-round btn-warning btn-sm">Reset</button>
                                    </div>
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
  <div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times</button>
          </div>
            <div align="center" class="modal-body center">
                <img class="img-responsive" src="" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>   
         <!-- /. PAGE WRAPPER  -->
     <!-- /. WRAPPER  -->
    <!-- JS Scripts-->
       <script src="<?php echo base_url(); ?>assets/js/jquery.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery-1.8.3.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
    
<script type="text/javascript">
$(document).ready(function () {
	$("#submit").click(function() {   
	    var validate="no";
	    if($.trim($('#news_title').val())==""){ $('#e-news_title').removeClass("hidden");validate="yes";}  else{ $('#e-news_title').addClass("hidden");}
	    if($.trim($('#news_from_date').val())==""){ $('#e-news_from_date').removeClass("hidden");validate="yes";}  else{ $('#e-news_from_date').addClass("hidden");} 
	    if($.trim($('#news_to_date').val())==""){ $('#e-news_to_date').removeClass("hidden");validate="yes";}  else{ $('#e-news_to_date').addClass("hidden");}
	    if(validate=='yes'){ return false; }else{  return true;
	    	$('#newsForm').submit();
	    }
	});
         
});
</script>
<!-- Custom Js -->
      
<script>
$('img').on('click', function () {
	var image = $(this).attr('src');
	$('#myModal').on('show.bs.modal', function () {
		$(".img-responsive").attr("src", image);
	});
});
</script>