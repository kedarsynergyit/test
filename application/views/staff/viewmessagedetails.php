     <section id="main-content">
          <section class="wrapper site-min-height">
			<?php /* ?><div class="pull-left"><h3><i class="fa fa-chevron-circle-right"></i> Message Details</h3></div><?php */ ?>
			<div class="pull-left"><h3><?php echo $details[0]->subject; ?></h3></div>
			<div class="pull-right"><h3><a class="btn btn-primary btn-sm btn-block" href="messages">Back</a></h3></div>
           <div id="page-inner"> 
              <div class="row">
                <div class="col-lg-12">
                       <div class="text-center">
                            <div id="infoSuccess"><?php echo $this->session->flashdata('success');?></div>
                             <div id="infoError"><?php echo $this->session->flashdata('failed');?></div>
                        </div>
                    <div class="panel panel-default">
                     	
                     	<div class="panel-heading">
                            <a href="<?php echo base_url().'index.php/portal/replyTo?id='.$details[0]->id; ?>" title="Reply to this message..." style="color: #330099 !important;"><i class="fa fa-reply" aria-hidden="true"></i>&nbsp; reply...</a>
                        </div>
                     	
                        <div class="panel-body">
                            <div class="row">
                                
                                <div class="col-lg-6 ml-15">
                                      <input type='hidden' name='id' id="id" value="<?php echo $details[0]->id; ?>" />
                                      
                                        <div class="form-group">
                                            <label><b><u>From</b></u></label>
                                            <p><?php echo $details[0]->from_email; ?></p>
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
<script type="text/javascript">
$(document).ready(function () {
	$("#submit").click(function() {   
		var validate="no";
	    if($.trim($('#to').val())==""){ $('#e-to').removeClass("hidden");validate="yes";}  else{ $('#e-to').addClass("hidden");}
	    if($.trim($('#subject').val())==""){ $('#e-subject').removeClass("hidden");validate="yes";}  else{ $('#e-subject').addClass("hidden");} 
	    if($.trim($('#message').val())==""){ $('#e-message').removeClass("hidden");validate="yes";}  else{ $('#e-message').addClass("hidden");}
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