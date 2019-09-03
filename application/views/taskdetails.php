   
      <!-- **********************************************************************************************************************************************************
      MAIN CONTENT
      *********************************************************************************************************************************************************** -->
      <!--main content start-->
      <section id="main-content">
         
          <section class="wrapper site-min-height">
             <div class="row">
                  <div class="col-lg-9 main-chart">  
                       <div class="pull-left"><h3><i class="fa fa-chevron-circle-right"></i> PROJECT
                  <i class="fa fa-angle-right"></i> <?php echo $prjname;  ?><sup><span class="badge bg-info"> <?php  echo $nooftask;?> </span></sup>
                    <i class="fa fa-angle-right"></i><?php echo $task[0]->taskid;  ?></h3></div>
                    
                 <div class="pull-right"><h3><a class="btn btn-primary btn-sm btn-block" href="task?id=<?php echo $prjid; ?>" >Back</a></h3></div>
          
                  	<div class="row mt">
                  	<div class="col-lg-12">
                            <link href="<?php echo base_url(); ?>assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />

                    <!-- Advanced Tables -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                             Tickets Details (<?php  echo $task[0]->title; ?>)
                        </div>
                        <div class="panel-body">                               
                             <div class="col-lg-12 ml-15">
                                        <div class="form-group">
                                            <label>Description</label>
                                            <div><?php  echo $task[0]->description; ?></div>
                                          <?php // print_r($task); ?>
                                        </div>
                               <!-- displaying task images -->
                                        <?php  if(!empty($taskimages)) { ?>
	                                   <div class="table-responsive">
	                                       <table class="table table-striped  table-hover"  >
	                                        <tr>
	                                              <?php $i=1; foreach ( $taskimages as $timg){ ?>
												   
	                                                     
	                                            <td class="text-center imagetd" style="border-top:0px;"> 
												  <?php   $endexdt= pathinfo($timg->image_path, PATHINFO_EXTENSION);
	                                                     if($endexdt=='jpg'||$endexdt=='jpeg'||$endexdt=='png'||$endexdt=='gif'){ ?>
												<img data-toggle="modal" data-target="#myModal" src="<?php echo base_url(); ?><?php echo $timg->image_path; ?>"  width="100" height="100">
												<?php } else { ?>
											<i  class="fa fa-file fa-3" aria-hidden="true"></i> <a target="_blank" href="<?php echo base_url().$timg->image_path; ?>"><b>Download <?php echo strtoupper($endexdt); ?> File</b></a><?php } ?>
												</td>
	                                          <?php $i++; if($i==6){$i=1; echo '</tr>';} } if($i==5) { echo '</tr>'; } ?>
	                                        </tr>
	                                    </table>
	                                   </div>
	                                    <?php } ?>


							   </div>
                             <div class="pull-left">
                             	<button class="btn btn-primary btn-sm btn-block" id="addcomment">
                             		<i class="fa fa-plus"></i>  Add Comment
                             	</button>
                             </div>
							<?php
								if($this->session->flashdata('error_message')) { ?>
								<br /><center><span class="error_flash"><?php echo $this->session->flashdata('error_message'); ?></span></center>
							<?php 
								}
							?>
                        </div>
                   
                        
                                                   
                      
                        <div class="panel-body hidden" id="addcomm" >   
                         <?php echo form_open_multipart('customerportal/addtaskcomments','class="form-horizontal" id="my_form_id"');?> 
                             <div class="col-lg-12 ml-15">
                                        <div class="form-group">
                                            <label>Comment</label>
                                          
                                        </div>
                                 <input type="hidden" value="<?php echo  $task[0]->taskid; ?>"  id="taskid_c" name="taskid_c">
                                <input type="hidden" value="<?php echo  $task[0]->id; ?>"  id="id_c" name="id_c">
                                <input type="hidden" value="<?php echo  $task[0]->projectid; ?>"  id="prjid" name="prjid">
								<input type="hidden" class="form-control" name='accountmanager' id='accountmanager' value="<?php echo $accountmanager; ?>">
								<input type="hidden" class="form-control" name='projectmanager' id='projectmanager' value="<?php echo $projectmanager; ?>">
								<input type="hidden" class="form-control" name='assignedto' id='assignedto' value="<?php echo $assignedtoid; ?>">
                                <textarea class="form-control" rows="4" name="comment" id="comment"></textarea>
                                <label class="textarea-character-limit pull-right"><span id="txtarea_character_limit">300</span> characters left</label><br />
                            	<input type="hidden" value="300" id="txt_character_limit_hidden">
                                <div class="form-group">
                                            <label>File input <i>(maximum file size 3MB)</i></label>
                                            <div id="fileadd0">
	                                            <input type="file" name='files[]' id="upload" class="pull-left">
	                                            <button class="btn btn-danger btn-xs" onclick="removefile(0)"><i class="fa fa-trash-o "></i></button> 
	                                        </div>
                                        </div>								
											
                                <div class="form-group">
                                	<div  id="addfiles"></div>
	                                <input type="hidden" name="num" id="num" value="1" >
	                                <button type="button" class="btn btn-primary btn-xs" onclick="addMoreFiles()">Add More</button>
                                </div>
                                
                                <div class="form-group">
	                                <div class="pull-left">
	                                	<button type="submit" name="submits" id="submits" class="btn btn-primary btn-sm">Submit</button>
	                                </div>
	                            </div>
                                
                            </div>
                            
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                        <div class="panel panel-default">
                         <div class="panel-heading">
                             Comments
                        </div>
                             <div class="panel-body">                                                                       
                              <?php  if(!empty($comment)){ foreach($comment as $row){ ?>
                            <div class=" ds">
                                <div class="desc">                
                                    <div class="" style=" margin: 0 10px 0 20px;">
                                        <div class="pull-left"><p><span class="badge bg-theme"><i class="fa fa-comment"></i></span> &nbsp;<muted><?php echo $row['commented_by']; ?></muted></p>
                                        </div>                       
                                        <div class="pull-right"><p><muted><?php echo date('Y-m-d  h:i A',strtotime($row['created_on'])); ?></muted></p></div>
                                    </div> 
                                <br/>
                                <br/>
                              
                                <div class="ml-15 each_comment_display"> <?php  echo $row['comments']; ?></div>  
                               
                                    <?php  if(!empty($row['images'])) { ?>
                                   <div class="table-responsive">
                                       <table class="table table-striped  table-hover"  >
                                        <tr>
                                              <?php $i=1; foreach ( $row['images']as $img){ ?>
											   
                                                     
                                            <td class="text-center imagetd" style="border-top:0px;"> 
											  <?php   $endexdt= pathinfo($img->image_path, PATHINFO_EXTENSION);
                                                     if($endexdt=='jpg'||$endexdt=='jpeg'||$endexdt=='png'||$endexdt=='gif'){ ?>
											<img data-toggle="modal" data-target="#myModal" src="<?php echo base_url(); ?><?php echo $img->image_path; ?>"  width="100" height="100">
											<?php } else { ?>
										<i  class="fa fa-file fa-3" aria-hidden="true"></i> <a target="_blank" href="<?php echo base_url().$img->image_path; ?>"><b>Download <?php echo strtoupper($endexdt); ?> File</b></a><?php } ?>
											</td>
                                          <?php $i++; if($i==6){$i=1; echo '</tr>';} } if($i==5) { echo '</tr>'; } ?>
                                        </tr>
                                    </table>
                                   </div>
                                    <?php } ?>
                               
                                </div>                        
                            </div>
                             <?php } }?>
                          
                          
       
                            
                        </div>
                       
                       
                    </div>
                    <!--End Advanced Tables -->              
             <!-- /. PAGE INNER  -->
            </div>                  	
                  	</div><!-- /row mt -->	
                  </div><!-- /col-lg-9 END SECTION MIDDLE -->                  
                 
      <!-- **********************************************************************************************************************************************************
      RIGHT SIDEBAR CONTENT
      *********************************************************************************************************************************************************** -->                  
                  
                  <div class="col-lg-3 ds" id='task-details' >
                    <!--COMPLETED ACTIONS DONUTS CHART-->
                     <?php /* ?><div class="desc">
                         <a href="addtask?id=<?php echo $prjid; ?>" class="btn btn-primary btn-lg btn-block" style="color:white;">Add Ticket</a>        
                  </div><?php */ ?>
				  <?php 
						if($task[0]->expected_date==""||$task[0]->expected_date=='0000-00-00 00:00:00'){  $expeted_start="--"; } 
							else{$expeted_start=date('Y-m-d',strtotime($task[0]->expected_date));} 
						if($task[0]->expected_end==""||$task[0]->expected_end=='0000-00-00 00:00:00'){  $expectecomplete="--"; } else{
							$expectecomplete=date('Y-m-d',strtotime($task[0]->expected_end));} 
						if($task[0]->start==""||$task[0]->start=='0000-00-00 00:00:00'){  $start="--"; } else{
							$start=date('Y-m-d',strtotime($task[0]->start));}
						if($task[0]->end_date==""||$task[0]->end_date=='0000-00-00 00:00:00'){  $completed="--"; } else{
							$completed=date('Y-m-d',strtotime($task[0]->end_date));}
				?>
				
                    <div class=" text-center" > <br/><b>Details</b></div>                         
                        <div class="desc" id="details">
                        
                        <div class="details">
                        	<label>Category</label> :<?php echo $category_name; ?>
                        </div> 
                                            
                         <div class="details">
                            <label>Assigned To</label> :<?php echo $assignedto ?>
                        </div>
                        <div class="details">
                            <label>Status</label> :<?php echo $status ?>
                        </div>
                        <div class="details">
                            <label>Priority</label> :<?php echo $priority; ?>
                        </div>
                            <div class="details">
                            <label>Hours spent on task</label> :<?php echo $task[0]->hours; ?>
                        </div>
                             <div class="details">
                             
                            <label>Expected to Start</label> : <?php echo $expeted_start; ?>
                        </div>
                         <div class="details">
                             
                            <label>Expected to Complete</label> : <?php echo $expectecomplete; ?>
                        </div>
                        <div class="details">
                            <label>Created By</label> :  <?php echo $created_by; ?>
                        </div>
                       
						 <div class="details">
						
                            <label>Started On</label> :  <?php echo $start; ?>
                        </div>
						<div class="details">
						
                            <label>Completed On</label> :  <?php echo $completed;  ?>
                        </div>
						<div class="details">
						<label>Resolution</label> : <?php echo $task[0]->resolution;?>
						</div>
                        </div>
                      
   
                      
                  </div><!-- /col-lg-3 -->
              </div><!--/row -->
          </section>
      </section>
  <div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times</button>
          </div>
            <div class="modal-body">
                <img class="img-responsive" src="" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>    
 <script src="<?php echo base_url(); ?>assets/js/jquery.js"></script>
      <!--main content end-->
 <script type="text/javascript">
     $(document).ready(function () {
        
         $("#addcomment").click(function(){
             var x = document.getElementById('addcomm');
            x.classList.toggle("hidden");
            
			$("#my_form_id").on("submit",function(e){

				var comment = $.trim($("#comment").val());
				if(comment==""){
					alert("Please enter comment!");
					e.preventDefault();
					$("#comment").val('');
					$("#comment").focus();
					return false;
				}
				
				add_loader();
			});	

		// limit the character limit for comment for task
		var maxLength = $("#txt_character_limit_hidden").val();
        $('#comment').keyup(function() {
			var textlen = maxLength - $(this).val().length;
			if (textlen < 0) {
				$(this).val($(this).val().substring(0, maxLength));
			} else {
            	$("#txtarea_character_limit").html(textlen);
			}
        });
    });
    $('img').on('click', function () {
        var image = $(this).attr('src');
        $('#myModal').on('show.bs.modal', function () {
            $(".img-responsive").attr("src", image);
        });
    });
});
     
       $(document).on("click", ".taskdetails", function () {
              var id= $(this).data('id');
              var taskid= $(this).data('taskid');
       
   var form_data = {     
        id: id, 
        taskid:taskid,
          }; 
         $.ajax({
        url: "<?php echo site_url('customerportal/taskdetailsajax'); ?>",
        type: 'POST',
        data: form_data,
        success: function(msg) {
            $('#details').html(msg);
        }

    });
});  
 function addMoreFiles(){
                  var addnum=$('#num').val();
                 var adnum=parseInt(addnum)+parseInt(1);
                  $('#num').val(adnum);
           $("#addfiles").append('<div class="" id=fileadd'+adnum+'><input type="file" name="files[]" id="files[]" class="pull-left"><button class="btn btn-danger btn-xs" onclick="removefile('+adnum+')"><i class="fa fa-trash-o "></i></button></div><br/>');
         
       }
       function removefile(eleId)
    {
        $( "div" ).remove( "#fileadd"+eleId );
       
    }
</script>
  