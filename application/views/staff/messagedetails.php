     <section id="main-content">
          <section class="wrapper site-min-height">
			<div class="pull-left"><h3><i class="fa fa-chevron-circle-right"></i> Message Details</h3></div>
			<div class="pull-right"><h3><a class="btn btn-primary btn-sm btn-block" href="messages">Back</a></h3></div>
           <div id="page-inner"> 
              <div class="row">
                <div class="col-lg-12">
                       <div class="text-center">
                            <div id="infoSuccess"><?php echo $this->session->flashdata('success');?></div>
                             <div id="infoError"><?php echo $this->session->flashdata('failed');?></div>
                        </div>
                    <div class="panel panel-default">
                     
                        <div class="panel-body">
                            <?php echo form_open_multipart('portal/update_message','class="form-horizontal" id="messageForm"');?> 
                            <div class="row">
                                
                                <div class="col-lg-6 ml-15">
                                      <input type='hidden' name='id' id="id" value="<?php echo $details[0]->id; ?>" />
                                      
                                      <div class="form-group">
                                            <label>To</label>
                                            <input type="text" name="to" class="form-control" id="to" placeholder="" maxlength="150" value="<?php echo $details[0]->to; ?>">
                                            <div class="text-right hidden" id="e-to" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Subject</label>
                                            <input type="text" name="subject" class="form-control" id="subject" placeholder="" maxlength="1024" value="<?php echo $details[0]->subject; ?>">
                                            <div class="text-right hidden" id="e-subject" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                		
                                        <div class="form-group">
                                            <label>Message</label>
                                            <textarea class="form-control" rows="4" name="message" id="message"><?php echo $details[0]->message; ?></textarea>
                                            <?php /* ?><label class="textarea-character-limit pull-right"><span id="txtarea_character_limit">1000</span> characters left</label><br />
                            				<input type="hidden" value="1000" id="txt_character_limit_hidden"><?php */ ?>
                                            <div class="text-right hidden" id="e-message"><span class="alert-danger">Required Field</span></div>
                                        </div>  
										
								</div>
                            </div>
                            <!-- /.row (nested) -->
                            
                            <div class="row">
                            	<div class="col-sm-6 col-md-6 col-lg-6 ml-15">
                            		<div class="form-group">
                                        <div class="pull-left"><button type="submit" name="submit" id="submit" class="btn btn-block btn-primary btn-sm">Submit</button></div>
                                        <div class="pull-left" style="margin-left: 10px;"><button type="reset" name="reset" id="reset"  class="btn btn-block btn-primary btn-sm">Reset</button></div>
                                    </div>
								</div>
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

<!-- nicedit -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/nicedit/nicEdit.js"></script>     

<script type="text/javascript">
$(document).ready(function () {

	new nicEditor({iconsPath : '<?php echo base_url()."assets/js/nicedit/nicEditorIcons.gif"; ?>'}).panelInstance('message');
	
	$("#submit").click(function() {   
		var validate="no";
	    if($.trim($('#to').val())==""){ $('#e-to').removeClass("hidden");validate="yes";}  else{ $('#e-to').addClass("hidden");}
	    if($.trim($('#subject').val())==""){ $('#e-subject').removeClass("hidden");validate="yes";}  else{ $('#e-subject').addClass("hidden");} 
	    /*if($.trim($('#message').val())==""){ $('#e-message').removeClass("hidden");validate="yes";}  else{ $('#e-message').addClass("hidden");}*/
	    if(validate=='yes'){ return false; }else{  return true;
	    	$('#form_message').submit();
	    }
	});

	// call this function once to get count for existing characters and to minus them from the text
	//check_blog_text_limit();
	
	/*$('#news_title').keyup(function() {
		check_blog_text_limit();
    });*/
         
});

/*function check_blog_text_limit(){
	// limit the character limit for blog text
	var maxLength = $("#txt_character_limit_hidden").val();

    var textlen = maxLength - $('#news_title').val().length;
	if (textlen < 0) {
		$('#news_title').val($('#news_title').val().substring(0, maxLength));
	} else {
        $("#txtarea_character_limit").html(textlen);
	}
}*/
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