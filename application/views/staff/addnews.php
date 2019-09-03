     <section id="main-content">
          <section class="wrapper site-min-height">
			<div class="pull-left"><h3><i class="fa fa-chevron-circle-right"></i> Add Blog</h3></div>
			<div class="pull-right"><h3><a href="news" class="btn btn-primary btn-sm btn-block">Back</a></h3></div>
           <div id="page-inner"> 
              <div class="row">
                <div class="col-lg-12">
                       <div class="text-center">
                            <div id="infoSuccess"><?php echo $this->session->flashdata('success');?></div>
                             <div id="infoError"><?php echo $this->session->flashdata('failed');?></div>
                        </div>
                    <div class="panel panel-default">
                     
                       
                        <div class="panel-body">
                            <?php echo form_open_multipart('portal/insert_news','class="form-horizontal" id="form_news"');?> 
                            <div class="row">
                                
                                <div class="col-lg-6 ml-15">
                                        <div class="form-group">
                                            <label>Blog Description</label>
                                            <?php /* ?><input class="form-control" name='news_title' id='news_title' value=""><?php */ ?>
                                            <textarea class="form-control" rows="4" name="news_title" id="news_title"></textarea>
                                            <label class="textarea-character-limit pull-right"><span id="txtarea_character_limit">1000</span> characters left</label><br />
                            				<input type="hidden" value="1000" id="txt_character_limit_hidden">
                                            <div class="text-right hidden" id="e-news_title"><span class="alert-danger">Required Field</span></div>
                                        </div>
                                      
                                        <div class="form-group">
                                            <label>From Date</label>
                                            <input type="text" name="news_from_date" class="datefieldwidth" id="news_from_date" placeholder="From Date">&nbsp;<i class="fa fa-calendar" id="news_from_datei"></i>            
                                            <div class="text-right hidden" id="e-news_from_date" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>To Date</label>
                                            <input type="text" name="news_to_date" class="datefieldwidth" id="news_to_date" placeholder="To Date">&nbsp;<i class="fa fa-calendar" id="news_to_datei"></i>            
                                            <div class="text-right hidden" id="e-news_to_date" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Show Customers?</label><br/>
                                            <input type="radio" name="cust_show" id="cust_show" value="0"  checked="checked">No &nbsp;
                                            <input type="radio" name="cust_show" id="cust_show" value="1" >Yes
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Show Users?</label><br/>
                                            <input type="radio" name="user_show" id="user_show" value="0" >No &nbsp;
                                            <input type="radio" name="user_show" id="user_show" value="1" checked="checked">Yes
                                        </div>

										<div class="form-group">
											<label>Image</label> 
											<div class="ml-15">
												<input type="file" name='files[]' id="upload" class="form-group  ml-15"> 
											</div>
										</div>
                                    	
                                    	<div class="form-group">
	                                        <div class="pull-left"><button type="submit" name="submit" id="submit" class="btn btn-primary btn-sm btn-block">Submit</button></div>
                                        	<div class="pull-left" style="margin-left: 10px;"><button type="reset" name="reset" id="reset"  class="btn btn-primary btn-sm btn-block">Reset</button></div>
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

	// limit the character limit for blog text
	var maxLength = $("#txt_character_limit_hidden").val();
    $('#news_title').keyup(function() {
		var textlen = maxLength - $(this).val().length;
		if (textlen < 0) {
			$(this).val($(this).val().substring(0, maxLength));
		} else {
        	$("#txtarea_character_limit").html(textlen);
		}
    });
	
	$("#submit").click(function() {   
	    var validate="no";
	    if($.trim($('#news_title').val())==""){ $('#e-news_title').removeClass("hidden");validate="yes";}  else{ $('#e-news_title').addClass("hidden");}
	    if($.trim($('#news_from_date').val())==""){ $('#e-news_from_date').removeClass("hidden");validate="yes";}  else{ $('#e-news_from_date').addClass("hidden");} 
	    if($.trim($('#news_to_date').val())==""){ $('#e-news_to_date').removeClass("hidden");validate="yes";}  else{ $('#e-news_to_date').addClass("hidden");}
	    if(validate=='yes'){ return false; }else{  return true;
	    	$('#form_news').submit();
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