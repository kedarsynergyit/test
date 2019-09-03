   
      <!-- **********************************************************************************************************************************************************
      MAIN CONTENT
      *********************************************************************************************************************************************************** -->
      <!--main content start-->
      <section id="main-content">
         
          <section class="wrapper site-min-height">
             <div class="row">
                  <div class="<?php echo ($urole!=1)?'col-lg-9':'col-lg-12'; ?> main-chart">  
					<div class="pull-left"><h3><?php /* ?><i class="fa fa-chevron-circle-right"></i><?php */ ?> PROJECT
						<i class="fa fa-chevron-circle-right"></i> <?php echo $prjname;  ?><sup><span class="badge bg-info"> <?php  echo $nooftask;?> </span></sup>
						<i class="fa fa-chevron-circle-right"></i> <?php echo $task[0]->taskid;  ?></h3></div>
                 
					<div class="pull-right"><h3><a class="btn btn-primary btn-sm btn-block" href="javascript:history.back();" >Back</a></h3></div>
          
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
                             <div class="pull-left"><button class="btn btn-primary btn-sm btn-block" id="addcomment"><i class="fa fa-plus"></i>  Add Comment</button></div>
                        </div>
                   
                        
                                                   
                      
                        <div class="panel-body hidden" id="addcomm" >   
                         <?php echo form_open_multipart('portal/addtaskcomments','class="form-horizontal" id="my_form_id"');?> 
                             <div class="col-lg-12 ml-5">
                                        <div class="form-group">
                                            <label>Comment</label>
                                          
                                       
                                 <input type="hidden" value="<?php echo  $task[0]->taskid; ?>"  id="taskid_c" name="taskid_c">
                                <input type="hidden" value="<?php echo  $task[0]->id; ?>"  id="id_c" name="id_c">
                                  <input type="hidden" value="<?php echo  $task[0]->projectid; ?>"  id="prjid" name="prjid">
								  	<input type="hidden" class="form-control" name='accountmanager' id='accountmanager' value="<?php echo $accountmanager; ?>">
											<input type="hidden" class="form-control" name='projectmanager' id='projectmanager' value="<?php echo $projectmanager; ?>">
                                <textarea class="form-control" rows="4" name="comment" id="comment"></textarea>
                                	<label class="textarea-character-limit pull-right"><span id="txtarea_character_limit">300</span> characters left</label><br />
                            	<input type="hidden" value="300" id="txt_character_limit_hidden">
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
                                <button type="button" class="btn btn-primary btn-xs" onclick="addMoreFiles()">Add More</button>
                                  </div>
                              <?php   if($task[0]->show_customer=="0"){ ?> 
                                <div class="form-group">
                                            <label>Show Customer</label><br/>
                                            <input type="radio" name="cust_show" id="cust_show" value="0" checked="checked" >Yes &nbsp;
                                            <input type="radio" name="cust_show" id="cust_show" value="1" >No
                                            <div class="text-right hidden" id="e-desc"><span class="alert-danger">Required Field</span></div>
                                        </div>  
                              <?php } ?>
                              
                              	<div class="form-group">
	                                <div class="pull-left"><button type="submit" name="submits" id="submits" class="btn btn-primary btn-sm">Submit</button></div>
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
                  
                  <?php if($urole!=1){ ?>
                  <div class="col-lg-3 ds" id='task-details' >
                    <!--COMPLETED ACTIONS DONUTS CHART-->
                     <?php /*if($internal_user_external_user!=2){ ?>
                     <div class="desc">
                         <a href="addtask?id=<?php echo $prjid; ?>" class="btn btn-primary btn-lg btn-block" style="color:white;">Add Ticket</a>
        
                  </div>
                  <?php }*/ ?>
                     <div class="pull-right" ><?php if($urole!=1){ ?><a class="btn btn-primary btn-sm" id="editdetails" style="color: #FFFFFF;">Edit</a><?php } ?></div>  
                    <div class=" text-center" > <br/><b>Details</b></div>      
                    <div class="details">
                            <label>Created On</label> : <?php echo date('Y-m-d  ',strtotime($task[0]->created_on)); ?>
                        </div>
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
				
                       <div class="desc hidden " id="detailsedit"> 
                             <input type="hidden" value="<?php echo  $task[0]->id; ?>"  id="taskid" name="taskid">
							
							<div class="detail" style="width: 250px;">
								<label>Category</label>
								<?php  echo form_dropdown('fk_category_id', $categories,$task[0]->fk_category_id,'class="form-control" id="fk_category_id"'.$dropdown_extra_attributes); ?>
							</div>
							
							<div class="detail" style="width: 250px;">
                            <label>Show Customer</label> : <input type="radio" name="task_cust_show" id="task_cust_show" value="0" <?php echo ($task[0]->show_customer=="0")?'checked="checked"':""; ?> <?php echo ($internal_user_external_user==2)?'disabled="disabled"':""; ?>>Show&nbsp;
                                            <input type="radio" name="task_cust_show" id="task_cust_show" value="1" <?php echo ($task[0]->show_customer=="1")?'checked="checked"':""; ?> <?php echo ($internal_user_external_user==2)?'disabled="disabled"':""; ?>>Don't Show
                        </div> 
                               <div class="details">
                            <?php $dropdown_extra_attributes = ($internal_user_external_user==2)?' disabled="disabled"':' '; ?>
                            <label>Assigned To</label> :
                            <?php  
                            	//echo form_dropdown('assignedto', $assignedto,$task[0]->assigned_to,'class="form-control" id="assignedto"'.$dropdown_extra_attributes); 
                            	echo form_dropdown('assignedto', $assignedto,$task[0]->assigned_to,'class="form-control" id="assignedto"');
                            ?>
                        </div>
                        <div class="details">
                            <label>Status</label> : <?php  echo form_dropdown('status', $status,$task[0]->status,'class="form-control" id="status"'); ?>
                        </div>
                        <div class="details">
                            <label>Priority</label> :  <?php echo form_dropdown('priority', $priority,$task[0]->priority,' id="priority"  class="form-control " '.$dropdown_extra_attributes); ?>
                        </div>
                            <div class="details">
                                <label>Hours spent on task</label> :
                                <input type="text" name="hrspent" id="hrspent"  class="form-control " value="<?php echo $task[0]->hours; ?>" />
                        </div>
                             <div class="details">
                               
                            <label>Expected to Start</label> :  
                            <input type="text" name="dob" value='<?php echo ($expeted_start=="0000-00-00 00:00:00" || $expeted_start=="--")?date("Y-m-d"):$expeted_start; ?>' class="datefieldwidth" style="width:92%" id="dob" placeholder="Date" <?php echo $dropdown_extra_attributes; ?>><?php if($internal_user_external_user!=2){ ?>&nbsp;<i class="fa fa-calendar" id="dobi"></i><?php } ?>  
                        </div>
                         <div class="details">
                             
                            <label>Expected to Complete</label> :
                            <input type="text" name="doi" class="datefieldwidth" value='<?php echo ($expectecomplete=="0000-00-00 00:00:00" || $expectecomplete=="--")?date("Y-m-d"):$expectecomplete; ?>' style="width:92%"  id="doi" placeholder="Date" <?php echo $dropdown_extra_attributes; ?>><?php if($internal_user_external_user!=2){ ?>&nbsp;<i class="fa fa-calendar" id="doii"></i><?php } ?>
                        </div>
                        
						 <div class="details">
                               
                            <label> Start on</label> :  
                            <input type="text" name="news_from_date" value='<?php echo ($start=="0000-00-00 00:00:00" || $start=="--")?date("Y-m-d"):$start; ?>' class="datefieldwidth" style="width:92%" id="news_from_date" placeholder="Date">&nbsp;<i class="fa fa-calendar" id="news_from_datei"></i>  
                        </div>
						 <?php  if($completed!='--' ){    ?>
                         <div class="details">                       
                            <label> Completed on</label> :
                            <input type="text" name="news_to_date" class="datefieldwidth" value='<?php echo $completed; ?>' style="width:92%"  id="news_to_date" placeholder="Date" <?php echo $dropdown_extra_attributes; ?>><?php if($internal_user_external_user!=2){ ?>&nbsp;<i class="fa fa-calendar" id="news_to_datei"></i><?php } ?>
                        </div>
						 <?php } ?>
						 <div class="details">
                             
                            <label>Resolution</label> :
                            <textarea class="form-control" rows="5" name="resolution" id="resolution"><?php echo $task[0]->resolution;?></textarea>
                        </div>
						 <input type="hidden" name="modifiedon" value='<?php echo $task[0]->modified_on; ?>' id="modifiedon" placeholder="Date">
                        	
                        	<div class="details form-group">
                            	<label>Workorder File</label>
                            	<div class="" id="fileadd0">
									<input type="file" name="wofile" id="wofile" class="pull-left" value="">
									<?php /* ?><button class="btn btn-danger btn-xs" onclick="removefile(0)"><i class="fa fa-trash-o "></i></button><?php */ ?> 
								</div>
                            </div>
                        	
                        	<div class="form-group">
	                        	<div class="pull-left" style="margin-top: 10px;"> 
	                                <button type="submit" name="submiting" id="submiting" class="btn btn-primary btn-sm">Submit</button>
	                            </div>
                            </div>
                            
                            
                        </div>
                        
                    
                    
                        <div class="show" id="taskdetails"> 
						 
						 <div class="details">
							<label>Category</label> :<?php echo $category_name; ?>
						</div>
						 
                           <div class="details">
                            <label>Show Customer</label> :<?php if($task[0]->show_customer=="1"){ echo 'No'; } else{echo 'Yes'; } ?>
                        </div> 
                            <div class="details">
                            <label>Assigned To</label> :<?php echo $assigned_to ?>
                        </div>
                        <div class="details">
                            <label>Status</label> :<?php echo $statuss ?>
                        </div>
                        <div class="details">
                            <label>Priority</label> :<?php echo $prioritys; ?>
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
						
						<?php if(isset($list_wo_files)){ ?>
							<div class="details">
								<label>Workorder Files</label> : <br />
								<?php foreach ($list_wo_files as $each_wo_file){ ?>
									<a href="<?php echo base_url().$each_wo_file->file; ?>" target="_blank">View/Download File</a><br />
								<?php } ?>
							</div>
						<?php } ?>
						
                        </div>
   
                  </div><!-- /col-lg-3 -->
                  
                  <?php } ?>
                  
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
    	});

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
      	
          $("#editdetails").click(function(){
             var x = document.getElementById('detailsedit');
            x.classList.toggle("hidden");
             var y = document.getElementById('taskdetails');
            y.classList.toggle("hidden");
  
    });
    $('img').on('click', function () {
        var image = $(this).attr('src');
        $('#myModal').on('show.bs.modal', function () {
            $(".img-responsive").attr("src", image);
        });
    });
});
     
       $(document).on("click", "#submiting", function () {
         if(($('#status').val()=="4")&&($('#hrspent').val()=="")){
             alert("Please Enter Hours Spent On Task!!")
             $('#hrspent').focus();
             return false;
         }

         if($('#fk_category_id').val()=="")
         {
            alert("Please Select Category!!")
             $('#fk_category_id').focus();
             return false;  
         }
         
         if($('#assignedto').val()=="")
         {
            alert("Please Select Assigned To!!")
             $('#assignedto').focus();
             return false;  
         }

         add_loader();
            
   /*var form_data = {     
       
taskid:$('#taskid').val(),
cust_show:$('input[name="task_cust_show"]:checked').val(),
status:$('#status').val(),
priority:$('#priority').val(),
hrspent:$('#hrspent').val(),
expeted_start:$('#dob').val(),
expected_end:$('#doi').val(),
startdate:$('#news_from_date').val(),
completed:$('#news_to_date').val(),
assignedto:$('#assignedto').val(),
modifiedon:$('#modifiedon').val(),
resolution:$('#resolution').val(),
          }; 
         $.ajax({
        url: "<?php echo site_url('portal/updatetaskdetails'); ?>",
        type: 'POST',
        data: form_data,
        success: function(msg) {
			//alert(msg);
         	location.reload();
        }

    });*/

	var file_data = $('#wofile').prop('files')[0];
	var form_data = new FormData();
	form_data.append('wofile', file_data);
	form_data.append('taskid', $('#taskid').val());
	form_data.append('cust_show', $('input[name="task_cust_show"]:checked').val());
	form_data.append('status', $('#status').val());
	form_data.append('priority', $('#priority').val());
	form_data.append('hrspent', $('#hrspent').val());
	form_data.append('expeted_start', $('#dob').val());
	form_data.append('expected_end', $('#doi').val());
	form_data.append('startdate', $('#news_from_date').val());
	form_data.append('completed', $('#news_to_date').val());
	form_data.append('fk_category_id', $('#fk_category_id').val());
	form_data.append('assignedto', $('#assignedto').val());
	form_data.append('modifiedon', $('#modifiedon').val());
	form_data.append('resolution', $('#resolution').val());

	$.ajax({
        url: "<?php echo site_url('portal/updatetaskdetails'); ?>",
        type: 'POST',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        success: function(msg) {
			//alert(msg);
         	location.reload();
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
  