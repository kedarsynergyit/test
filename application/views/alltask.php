   
      <!-- **********************************************************************************************************************************************************
      MAIN CONTENT
      *********************************************************************************************************************************************************** -->
      <!--main content start-->
      <section id="main-content">
         
          <section class="wrapper site-min-height">
             <div class="row">
                  <div class="col-lg-9 main-chart">  
                       
                       <div class="pull-left"><h3><i class="fa fa-chevron-circle-right"></i> TICKETS</h3></div>
                       
                       <div class="pull-right">
                       	<h3><a href="addtask" class="btn btn-primary btn-sm btn-block" style="color:white;">Add Ticket</a></h3>
                       </div>
                       
              
                  	<div class="row mt">
                  	<div class="col-lg-12">
                            <link href="<?php echo base_url(); ?>assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />

                    <!-- Advanced Tables -->
                    <div class="panel panel-default">
                        <div class="panel-body">
                        	
                        	<!-- category filter start -->
                        	<label>
                        		Category :
	                        	<?php echo form_dropdown('fk_category_id', $categories,$fk_category_id,' id="fk_category_id" class="input-sm" '); ?>
                        	</label>
                        	<div style="clear:both;">&nbsp;</div>
                        	<!-- category filter end -->
                        	
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Priority</th>
											<th>Project</th>
                                            <th>Created On</th>                                           
                                            <th>Status</th>
                                            <td>Details</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php  if(!empty($list)){ foreach($list as $row){ ?>
                                 
                                        <tr>
                                  
                                            <td><a href="#" class="taskdetails" data-prjid="<?php echo $row['prjid']; ?>" data-taskid="<?php echo $row['taskid']; ?>"data-id="<?php echo $row['id']; ?>" data-accountmanager="<?php echo $row['accountmanager']; ?>" data-projectmanager="<?php echo $row['projectmanager']; ?>"><?php echo $row['taskid']; ?></a></td>
                                            <td><?php echo $row['title']; ?></td>  
                                            <td><?php echo $row['category_name']; ?></td>
                                            <td><?php echo $row['priority']; ?></td>
											<td><?php echo $row['prjname']; ?></td>
                                            <td><?php echo $row['created_on']; ?></td>
                                           
                                              <td><?php echo $row['status']; ?></td>
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
        <?php /* ?><div class="desc">
        	<?php
        		$id = "";
        		if(isset($list[0]['prjid']) && !empty($list[0]['prjid'])){
        			$id = $list[0]['prjid'];
        		}else{
        			$id = $this->input->get('id');
        		}
        	?>
            <a href="addtask?id=<?php echo $id; ?>" class="btn btn-primary btn-lg btn-block" style="color:white;">Add Ticket</a>
        </div><?php */ ?>
        <h4>Ticket Details</h4>  
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
			  var accountmanager=$(this).data('accountmanager'); 
              var projectmanager=$(this).data('projectmanager');
		
   var form_data = {     
        id: id, 
        taskid:taskid,
        prjid:prjid,
		accountmanager:accountmanager,
		projectmanager:projectmanager,
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
function successfunc(id,taskid,prjid,accountmanager,projectmanager){
     
   var form_data = {     
        id: id, 
        taskid:taskid,
         prjid:prjid,
		 accountmanager:accountmanager,
		projectmanager:projectmanager,
          }; 
         $.ajax({
        url: "<?php echo site_url('customerportal/taskdetailsajax'); ?>",
        type: 'POST',
        data: form_data,
        success: function(msg) {
          
            $('#details').html(msg);
        }

    });
}

    $('#my_form_id').on('submit', function(e) {
    e.preventDefault();
    var formData = new FormData($(this)[0]);
            var accountmanager=$('#accountmanager').val();
     var projectmanager=$('#projectmanager').val();  
      var  id= $('#id_c').val() ;
      var  taskid=$('#taskid_c').val();
      var  prjid=$('#prjid').val();
    var form = $('#my_form_id');

    var comment= $.trim($("#comment").val());
    if(comment==""){
			alert("Please enter comment!");
			e.preventDefault();
			$("#comment").val('');
			$("#comment").focus();
			return false;
		}
    
 	// add loader to show process
    add_loader();
    
    $.ajax({
        data: formData,
        async: true,
        cache: false,
        processData: false,
        contentType: false,
        url: "<?php echo site_url('customerportal/addtaskcommentsajax'); ?>",
        type: 'POST',        
        success: function(response) {
       
      successfunc(id,taskid,prjid,accountmanager,projectmanager)
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

$(document).ready(function () {
	$("#fk_category_id").on("change",function(){
		location.href=location.pathname+"?fk_category_id="+$(this).val()+"&ticket=open";
	});
});
</script>
  