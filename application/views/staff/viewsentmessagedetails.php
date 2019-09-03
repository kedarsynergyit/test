     <section id="main-content">
          <section class="wrapper site-min-height">
			<div class="pull-left"><h3><?php echo $details[0]->subject; ?></h3></div>
			<div class="pull-right"><h3><a class="btn btn-primary btn-sm btn-block" href="sentmessages">Back</a></h3></div>
           <div id="page-inner"> 
              <div class="row">
                <div class="col-lg-12">
                       <div class="text-center">
                            <div id="infoSuccess"><?php echo $this->session->flashdata('success');?></div>
                             <div id="infoError"><?php echo $this->session->flashdata('failed');?></div>
                        </div>
                    <div class="panel panel-default">
                     	
                     	<?php /* ?><div class="panel-heading">
                            <a href="<?php echo base_url().'index.php/portal/replyTo?id='.$details[0]->id; ?>" title="Reply to this message..." style="color: #330099 !important;"><i class="fa fa-reply" aria-hidden="true"></i>&nbsp; reply...</a>
                        </div><?php */ ?>
                     	
                        <div class="panel-body">
                            <div class="row">
                                
                                <div class="col-lg-6 ml-15">
                                      <input type='hidden' name='id' id="id" value="<?php echo $details[0]->id; ?>" />
                                      
                                        <div class="form-group">
                                            <label><b><u>To</b></u></label>
                                            <p><?php echo $details[0]->to_name; ?></p>
                                        </div>
                                		
                                        <div class="form-group">
                                            <label><b><u>Message</b></u></label>
                                            <p><?php echo nl2br($details[0]->message); ?></p>
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