     <section id="main-content">
          <section class="wrapper site-min-height">
			<div class="pull-left"><h3><i class="fa fa-chevron-circle-right"></i><?php /* ?> PROJECT <i class="fa fa-angle-right"></i><?php */ ?> Add Ticket <?php /* ?><?php if(isset($prjname) && !empty($prjname)){ ?><i class="fa fa-angle-right"></i> <?php echo $prjname;  ?><sup><span class="badge bg-info"> <?php  echo $nooftask;?> </span></sup><?php } ?><?php */ ?></h3></div>
			<?php /* ?><p class="text-right"><a href="task?id=<?php echo $this->input->get('id'); ?>" ><span class="badge bg-theme"> <i class="fa fa-hand-o-right"></i>Back</span></a></p><?php */ ?>
			<div class="pull-right"><h3><a class="btn btn-primary btn-sm btn-block" onclick="window.history.go(-1);" href="#" >Back</a></h3></div>
           <div id="page-inner"> 
              <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Add Ticket
                        </div>
                        <div class="panel-body">
                            <?php echo form_open_multipart('customerportal/insert_task','class="form-horizontal" id="myform"');?> 
                            <div class="row">
                                
                                <div class="col-lg-6 ml-15">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <?php /* ?><input type="hidden" class="form-control" name='projectid' id='projectid' value="<?php echo $this->input->get('id'); ?>">
											<input type="hidden" class="form-control" name='accountmanager' id='accountmanager' value="<?php echo $accountmanager; ?>">
											<input type="hidden" class="form-control" name='projectmanager' id='projectmanager' value="<?php echo $projectmanager; ?>"><?php */ ?>
										
                                            <input class="form-control" name='title' id='title'>
                                            <div class="text-right hidden" id="e-title"><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Category</label>
                                            <?php echo form_dropdown('fk_category_id', $categories,'',' id="fk_category_id" class="form-control" '); ?>
                                            <div class="text-right hidden" id="e-fk_category_id" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        
										<div class="form-group">
                                            <label>Project</label>
                                            <?php echo form_dropdown('projectid', $project,'',' id="projectid"  class="form-control " '); ?>
                                            <div class="text-right hidden" id="e-projectid" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                          <div class="form-group">
                                            <label>Priority</label>
                                            <?php echo form_dropdown('priority', $priority,'',' id="priority"  class="form-control " '); ?>
                                            <div class="text-right hidden" id="e-priority" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                    <div class="form-group">
                                            <label>Expected To Start </label>
                                            <input type="text" name="dob"  class="datefieldwidth" id="dob" placeholder="Date" value="<?php echo date("m/d/Y"); ?>">&nbsp;<i class="fa fa-calendar" id="dobi"></i>            
                                            <div class="text-right hidden" id="e-priority" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                        <div class="form-group">
                                            <label >Expected To Complete</label>
                                            <input type="text" name="doi" class="datefieldwidth" id="doi" placeholder="Date" value="<?php echo date("m/d/Y"); ?>">&nbsp;<i class="fa fa-calendar" id="doii"></i>              
                                            <div class="text-right hidden" id="e-priority" ><span class="alert-danger">Required Field</span></div>              
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
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea class="form-control" rows="3" name="desc" id="desc"></textarea>
                                            <div class="text-right hidden" id="e-desc"><span class="alert-danger">Required Field</span></div>
                                        </div>
                                       
                                      
                                     
                                    <div class="form-group">
                                        <div class="pull-left"><button type="submit" name="submit" id="submit" class="btn btn-block btn-primary btn-sm">Submit</button></div>
                                        <div class="pull-left" style="margin-left: 10px;"><button type="reset" class="btn btn-block btn-primary btn-sm">Reset</button></div>
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
       
        <script>
            $(document).ready(function () {
                $('#dataTables-example').dataTable();

                $("#title").focus();
            });
             function addMoreFiles(){
                  var addnum=$('#num').val();
                 var adnum=parseInt(addnum)+parseInt(1);
                  $('#num').val(adnum);
           $("#addfiles").append('<div class="form-group ml-15" id=fileadd'+adnum+'><input type="file" name="files[]"  class="pull-left"><button class="btn btn-danger btn-xs" onclick="removefile('+adnum+')"><i class="fa fa-trash-o "></i></button></div>');
         
       }
       $("#submit").click(function() {   
    var validate="no";
    if($('#title').val()==""){ $('#e-title').removeClass("hidden"); validate="yes";} else{$('#e-title').addClass("hidden");}
    if($('#fk_category_id').val()==""){ $('#e-fk_category_id').removeClass("hidden"); validate="yes";} else{$('#e-fk_category_id').addClass("hidden");} 
	if($('#projectid').val()=="" || $('#projectid').val()=="0"){ $('#e-projectid').removeClass("hidden");validate="yes";}  else{$('#e-projectid').addClass("hidden");}
     if($('#priority').val()==""){ $('#e-priority').removeClass("hidden");validate="yes";}  else{$('#e-priority').addClass("hidden");} 
    if($('#desc').val()==""){ $('#e-desc').removeClass("hidden");validate="yes";}  else{$('#e-desc').addClass("hidden");}
    if(validate=='yes'){ return false; }else{  return true;
    $('#myform').submit();
   }
    });   
    function removefile(eleId)
    {
        $( "div" ).remove( "#fileadd"+eleId );
       
    }
    
    </script>
         <!-- Custom Js -->
      
   
</body>
</html>
