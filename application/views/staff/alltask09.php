   
      <!-- **********************************************************************************************************************************************************
      MAIN CONTENT
      *********************************************************************************************************************************************************** -->
      <!--main content start-->
      <section id="main-content">
         
          <section class="wrapper site-min-height">
             <div class="row">
                  <div class="col-lg-9 main-chart">  
                       <h3><i class="fa fa-chevron-circle-right"></i> PROJECT / SERVICE
                  </h3>
              
                  	<div class="row mt">
                  	<div class="col-lg-12">
                            <link href="<?php echo base_url(); ?>assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />

                    <!-- Advanced Tables -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                             Task / Tickets 
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Priority</th>
											<th>Project</th>
                                            <th>Created On</th>                                           
                                            <th>Status</th>
											<th>Show/Don't Show</th>
                                            <td>Details</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php  if(!empty($list)){ foreach($list as $row){ ?>
                                 
                                        <tr>
                                  
                                            <td><a href="#" class="taskdetails"  data-show="<?php echo $row['show_customer']; ?>"  data-prjid="<?php echo $row['prjid']; ?>" data-taskid="<?php echo $row['taskid']; ?>" data-id="<?php echo $row['id']; ?>" data-accountmanager="<?php echo $row['accountmanager']; ?>" data-projectmanager="<?php echo $row['projectmanager']; ?>"><?php echo $row['taskid']; ?></a></td>
                                            <td><?php echo $row['title']; ?></td>  
                                            <td><?php echo $row['priority']; ?></td>
											<td><?php echo $row['prjname']; ?></td>
                                            <td><?php echo $row['created_on']; ?></td>
                                           
                                              <td><?php echo $row['status']; ?></td>
											  <td><?php if($row['show_customer']==0) echo 'Show'; else echo "Don't Show"; ?></td>
                                              <td class="text-center">               	
                                                  <a href ="taskdetails?id=<?php echo $row['id']; ?>" > <i class="fa fa-bars"></i></a></td>
                                              
                                        </tr>
                                 <?php } } ?>
                                      
                                    </tbody>
                                </table>
                            </div>
                            
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
        <div class="desc">
            <a href="addtask?id=<?php echo $list[0]['prjid']; ?>" class="btn btn-primary btn-lg btn-block" style="color:white;">Add Task / Ticket</a>
			<!--<a href="addtask?id=<?php echo $this->input->get('id'); ?>" class="btn btn-primary btn-lg btn-block" style="color:white;">Add Task / Ticket</a>-->
        </div>
        <h4>Task / Ticket Details</h4>  
         <?php echo form_open_multipart('customerportal/addtaskcomments','class="form-horizontal" id="my_form_id"');?> 
        <br/>
		
        <div  id="details">                    
        <div class="details">
              <p><muted></muted><br/>
                 Select task For Details<br/>
              </p>
        </div>
        </div>
    </div><!-- /col-lg-3 -->
</div><! --/row -->
          </section>
      </section>
 <script src="<?php echo base_url(); ?>assets/js/jquery.js"></script>
      <!--main content end-->
 <script type="text/javascript">
       $(document).on("click", ".taskdetails", function () {
              var id= $(this).data('id');
              var taskid= $(this).data('taskid');
              var prjid=$(this).data('prjid');
              var show=$(this).data('show');
			   var accountmanager=$(this).data('accountmanager'); 
              var projectmanager=$(this).data('projectmanager'); 
   var form_data = {     
        id: id, 
        taskid:taskid,
        prjid:prjid,
        show:show,
		accountmanager:accountmanager,
		projectmanager:projectmanager,
          }; 
         $.ajax({
        url: "<?php echo site_url('portal/taskdetailsajax'); ?>",
        type: 'POST',
        data: form_data,
        success: function(msg) {
            $('#details').html(msg);
        }

    });
});  
function successfunc(id,taskid,prjid,show,accountmanager,projectmanager){
     
   var form_data = {     
        id: id, 
        taskid:taskid,
         prjid:prjid,
		  show:show,
		 accountmanager:accountmanager,
		projectmanager:projectmanager,
          }; 
         $.ajax({
        url: "<?php echo site_url('portal/taskdetailsajax'); ?>",
        type: 'POST',
        data: form_data,
        success: function(msg) {
          
            $('#details').html(msg);
        }

    });
}
/*$(document).on("click", "#submits", function (e) {
       //e.preventDefault();
    //var form_data = new FormData(this); 
       var id= $('#taskid_c').val();
       var comment= $('#comment').val();
        var files= $('#files');
   var form_data = {     
        taskid_c: id, 
        comment:comment,
        files:files,
        show_customer:0,
        commented_by:1
          }; 
        alert('hi'); 
    $.ajax({
        url: "<?php //echo site_url('customerportal/addtaskcommentsajax'); ?>",
        type: 'POST',
        data: form_data,
        success: function(msg) {
            alert(msg);
      
    }

    });
});  */
    $('#my_form_id').on('submit', function(e) {
		var show=$('#shows').val();
    e.preventDefault();
    var formData = new FormData($(this)[0]);
     var accountmanager=$('#accountmanager').val();
     var projectmanager=$('#projectmanager').val();    
      var  id= $('#id_c').val() ;
      var  taskid=$('#taskid_c').val();
      var  prjid=$('#prjid').val();
      var custshow=$('#custshow').val();
    var form = $('#my_form_id');

    // add loader to show process
    add_loader();
    
    $.ajax({
        data: formData,
        async: true,
        cache: false,
        processData: false,
        contentType: false,
        url: "<?php echo site_url('portal/addtaskcommentsajax'); ?>",
        type: 'POST',        
        success: function(response) {
			//alert(response);
      		successfunc(id,taskid,prjid,show,accountmanager,projectmanager)   
      		
      		// hide loader now
      		remove_loader();
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