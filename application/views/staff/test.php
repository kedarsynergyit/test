     <section id="main-content">
          <section class="wrapper site-min-height">
           <h3>
               <i class="fa fa-chevron-circle-right"></i> PROJECT
                  <i class="fa fa-angle-right"></i> <?php echo $prjname;  ?><sup><span class="badge bg-info"> <?php  echo $nooftask;?> </span></sup></h3>
                  <p class="text-right"><a href="task?id=<?php echo $this->input->get('id'); ?>" ><span class="badge bg-theme"> <i class="fa fa-hand-o-right"></i>Back</span></a></p>
           <div id="page-inner"> 
              <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Add Task / Ticket
                        </div>
                        <div class="panel-body">
                            <?php echo form_open_multipart('customerportal/insert_task','class="form-horizontal" id="myform"');?> 
                            <div class="row">
                                
                                <div class="col-lg-6 ml-15">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <input type="hidden" class="form-control" name='projectid' id='projectid' value="<?php echo $this->input->get('id'); ?>">
                                            <input class="form-control" name='title' id='title'>
                                            <div class="text-right hidden" id="e-title"><span class="alert-danger">Required Field</span></div>
                                        </div>
                                      
                                          <div class="form-group">
                                            <label>Priority</label>
                                            <?php echo form_dropdown('priority', $priority,'',' id="priority"  class="form-control " '); ?>
                                            <div class="text-right hidden" id="e-priority" ><span class="alert-danger">Required Field</span></div>
                                        </div>
                                         <div class="form-group">
              <label>Task Start Date</label>
             <input type="text" name="dob" class="datefield form-control hasDatepicker"  id="dob" placeholder="Date">
                <?php //echo $result[0]['start']; ?>
                  <span class="add-on"><i class="icon-th"></i></span> </div>
                    <div class="text-right hidden" id="e-priority" ><span class="alert-danger">Required Field</span></div>
             
            </div>
                           <div class="form-group">
              <label >Task Completed Expected Date</label>
              
                <div  data-date="12-02-2012" class="input-append date datepicker">
                  <input type="text"  id="enddate" data-date-format="mm-dd-yyyy" class="span11" value="<?php echo $result[0]['expected_complete']; ?>" >                   
                  <span class="add-on"><i class="icon-th"></i></span> </div>
                    <div class="text-right hidden" id="e-priority" ><span class="alert-danger">Required Field</span></div>
              
            </div>
                 <div class="form-group">
              <label >Developer :</label>
              
                 <?php echo form_dropdown('developer', $developer,$result[0]["assigned_to"],'class="form-control" id="developer"'); ?>
                  <div class="text-right hidden" id="e-priority" ><span class="alert-danger">Required Field</span></div>
                  
                        
            </div>
         
               <div class="form-group">
              <label >Status:</label>
      
                <?php echo form_dropdown('statuss', $status,$result[0]["status"],'class="form-control" id="statuss"'); ?>
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
                                            <button type="button" class="btn btn-default btn-xs" onclick="addMoreFiles()">Add More</button>
                                        </div>
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea class="form-control" rows="3" name="desc" id="desc"></textarea>
                                            <div class="text-right hidden" id="e-desc"><span class="alert-danger">Required Field</span></div>
                                        </div>
                                       
                                       <div style="float: left;padding-right:2%;" >
                                                    <input type="text" name="dob" class="form-control" id='dob' placeholder="Date of Intake" style="float: left" />
                                                </div>
                                     
                                    <div  class="text-center">
                                        <button type="submit" name="submit" id="submit" class="btn btn-round btn-success btn-sm">Submit</button>
                                        <button type="reset" class="btn btn-round btn-warning btn-sm">Reset</button>
                                    </div>
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                                <input type="text" id="datepicker">
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
<script src="<?php echo base_url('assets/jquery-ui-1.12.0/jquery-ui.js'); ?>"></script>

        <script>
            $(document).ready(function () {
                $('#dataTables-example').dataTable();
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
    
        <script>
          
             $( function() {
    $( "#datepicker" ).datepicker();
  } );
 //$("#dobi").click(function() {
////$("#dob").datepicker("show" );
//});
$(function() {
       $("#dob").datepicker();
});
    </script>   
       
    
    

</body>
</html>
