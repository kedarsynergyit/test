   
      <!-- **********************************************************************************************************************************************************
      MAIN CONTENT
      *********************************************************************************************************************************************************** -->
      <!--main content start-->
		<section id="main-content">
			<section class="wrapper site-min-height">
				<div class="row">
					<div class="col-lg-12">
						<h3><i class="fa fa-chevron-circle-right"></i> <?php echo $project_name; ?></h3>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-6">
						<h6 class="chat_associated_members"><span>Members : </span><?php echo $associated_member_names; ?></h6>
						[ <a href="#" id="add_members_to_chat" data-toggle="modal" data-target="#modalAddMembers">+Add Users</a> ]
						[ <a href="#" id="add_customers_to_chat" data-toggle="modal" data-target="#modalAddCustomers">+Add Members</a> ]
					</div>
					<div class="col-lg-6">
						<div class="input-group" style="width:100%;">
							<input type="text" class="form-control" name="txt_search" id="txt_search" placeholder="Search..">
							<span class="input-group-btn">
						    	<button type="button" name="btn_search" id="btn_search" class="btn btn-primary">Go</button>
							</span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12 main-chart">  
						<?php /* ?><div class="row mt">
							<div class="col-lg-12"><?php */ ?>
                    			<div class="panel panel-default">
			                        <div class="panel-heading">
			                             Messages
			                        </div>
			                        <div class="panel-body">                               
										<div class="col-lg-12 ml-15" id="div_all_chat_messages" style="max-height:300px; overflow: auto;">
											<!-- now display chats -->
											<?php 
											if(isset($chat_data) && !empty($chat_data)){
												foreach ($chat_data as $each_chat){
													?>
													<div class="form-group chat_div <?php echo ((!empty($each_chat['fk_user_id']) && $each_chat['fk_user_id']==$logged_in_userid) || (!empty($each_chat['fk_customer_id']) && $each_chat['fk_customer_id']==$logged_in_userid) || (!empty($each_chat['fk_customer_user_id']) && $each_chat['fk_customer_user_id']==$logged_in_userid))?"pull-right":"pull-left"; ?>">
														<label class="chat_by">
															<?php 
																//echo (!empty($each_chat['fk_user_id']))?$each_chat['first_name']." ".$each_chat['last_name']:$each_chat['companyname'];
																if(!empty($each_chat['fk_user_id'])){
																	echo $each_chat['first_name']." ".$each_chat['last_name'];
																}else if(!empty($each_chat['fk_customer_id'])){
																	echo $each_chat['companyname'];
																}else{
																	echo $each_chat['email'];
																}
															?> 
															<span class="chat_time">(<?php echo date("Y-m-d h:i A",strtotime($each_chat['created_on'])); ?>)</span>
														</label>
														<span class="chat_message">
															<?php if(isset($each_chat['filepath']) && !empty($each_chat['filepath'])){ ?>
																<i class="fa fa-file-o" aria-hidden="true"></i>
																<a href="<?php echo $this->config->item('base_url').$each_chat['filepath']; ?>" target="_blank">
																	<?php echo nl2br($each_chat['message']); ?>
																</a>
															<?php }else{ ?>
																<?php echo nl2br($each_chat['message']); ?>
															<?php } ?>
														</span>
													</div>
													<?php
												}
											}
											?>
										</div>
			                        </div>
								</div>
							<?php /* ?></div>
						</div><!-- /row mt --><?php */ ?>	
						
						<!-- row to add new text message -->
						<div class="row" style="position:fixed; bottom: 15px; width:84%;">
							<div class="col-lg-12">
								<div class="form-group">
									<div class="input-group" style="width:100%;">
										<?php /* ?><textarea class="form-control" rows="1" name="comment" id="comment"></textarea><?php */ ?>
										
										<span class="input-group-btn">
											<input type="file" name="chat_file" id="chat_file" style="display:none;" multiple>
									    	<button type="button" name="add_file" id="add_file" class="btn btn-primary">+</button>
										</span>
										
										<input type="text" class="form-control" name="comment" id="comment">
										<span class="input-group-btn">
									    	<button type="button" name="add_chat" id="add_chat" class="btn btn-primary">send</button>
										</span>
									</div>
								</div>
								<?php /* ?><div class="form-group pull-right">
									<button type="button" name="add_chat" id="add_chat" class="btn btn-round btn-primary btn-sm">Submit</button>
								</div><?php */ ?>
							</div>
						</div>
						
					</div><!-- /col-lg-12 END SECTION MIDDLE -->
				</div><!--/row -->
			</section>
		</section>
		<input type="hidden" name="project_id" id="project_id" value="<?php echo $project_id; ?>">

<div id="modalAddMembers" class="modal fade" role="dialog">
	<div class="modal-dialog" style="width:300px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times</button>
				<h4 class="modal-title" style="color:#000000;">Add new member to chat</h4>
			</div>
			<div align="center" class="modal-body center">
				<select name="add_member_id" id="add_member_id" class="form-control">
					<?php foreach ($add_new_member_list as $member_id=>$member_name){ ?>
						<option value="<?php echo $member_id ?>"><?php echo $member_name ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick="add_selected_member();">Add</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div id="modalAddCustomers" class="modal fade" role="dialog">
	<div class="modal-dialog" style="width:300px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times</button>
				<h4 class="modal-title" style="color:#000000;">Add new customer to chat</h4>
			</div>
			<div align="center" class="modal-body center">
				<input class="form-control" type="email" name="customer_email" id="customer_email" value="" placeholder="email" />
			</div>
			<div align="center" class="modal-body center">
				<input class="form-control" type="password" name="customer_password" id="customer_password" value="" placeholder="password" />
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick="add_selected_customer();">Add</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function () {

		/*$("#add_members_to_chat").on('click',function(){
			$('#modalAddMembers').on('show.bs.modal', function (e) {
				// nothing to do here
			})
		});*/
		
		$("#div_all_chat_messages").animate({ scrollTop: $('#div_all_chat_messages').prop("scrollHeight")}, 1000);
		check_new_chat_project();

		$("#add_chat").on('click',function(){

			if($("#comment").val()==""){
				alert("Please enter message");
				$("#comment").focus();
				return false;
			}
			// submit the message with ajax and reload the content of the messages
			var data={
						"message":encodeURIComponent($("#comment").val()),
						"project_id":$("#project_id").val()
					};
			$.ajax({
				url: "<?php echo site_url('portal/chat_add_project'); ?>",
				type: 'POST',
		        data: data,
		        success: function(msg) {
					$("#comment").val('');
		            $('#div_all_chat_messages').html(msg);

		            // scrill to bottom
		            $("#div_all_chat_messages").animate({ scrollTop: $('#div_all_chat_messages').prop("scrollHeight")}, 1000);
		        }
			});

			return false;
		});

		$("#add_file").on("click",function(){
			$("#chat_file").trigger("click");
		});

		$("#chat_file").on("change",function(){
			add_loader();
			
			var formData = new FormData();
			$(this.files).each(function( index ) {
				formData.append("files[]", this);
			});
			//formData.append("files", this.files[1]);
			formData.append("project_id", $("#project_id").val());
			
			$.ajax({
				type: "POST",
		        url: "<?php echo site_url('portal/upload_project_chat_file_ajax'); ?>",
		        async: true,
		        data: formData,
		        cache: false,
		        contentType: false,
		        processData: false,
		        timeout: 60000,
		        success: function (result) {
		            // callback action
		        	remove_loader();
		        },
		        error: function (error) {
		            // handle error
		            alert("Error uploading file. Please try after some time.");
		        }
			});
		});

		// search
		$("#btn_search").on("click",function(){
			var txt_search = $("#txt_search").val();
			if(txt_search==""){
				$("#txt_search").focus();
			}else{
				// ajax search
				check_new_chat_project();
			}
		});
		
	});

	function check_new_chat_project(){
		var data={
				"project_id":$("#project_id").val(),
				"txt_search":$("#txt_search").val()
			};
		$.ajax({
			url: "<?php echo site_url('portal/chat_check_project'); ?>",
			type: 'POST',
	        data: data,
	        async: true,
	        success: function(msg) {
				$('#div_all_chat_messages').html(msg);

				// scrill to bottom
				var scrolltop = $("#div_all_chat_messages").scrollTop();
				var scrollheight = $('#div_all_chat_messages').prop("scrollHeight");
				var difference = parseInt(scrollheight)-parseInt(scrolltop);
				
				if(difference<500){
					$("#div_all_chat_messages").animate({ scrollTop: $('#div_all_chat_messages').prop("scrollHeight")}, 1000);
				}
	        }
		});

		setTimeout(check_new_chat_project, 3000);
	}

	function add_selected_member(){
		add_loader();

		// hide the modal
		$('#modalAddMembers').modal('toggle');
		
		var selected_member = $("#add_member_id").val();
		var data={
			"project_id":$("#project_id").val(),
			"fk_user_id":selected_member
		};
		$.ajax({
			url: "<?php echo site_url('portal/add_member_to_project_chat'); ?>",
			type: 'POST',
	        data: data,
	        async: true,
	        success: function(msg) {
				location.reload();
				remove_loader();
	        }
		});
	}

	function add_selected_customer(){
		var customer_email = $("#customer_email").val();
		var customer_password = $("#customer_password").val();

		if(customer_email==""){
			alert("Please enter email!");
			$("#customer_email").focus();
			return false;
		}
		if(customer_password==""){
			alert("Please enter password!");
			$("#customer_password").focus();
			return false;
		}

		add_loader();

		// hide the modal
		$('#modalAddCustomers').modal('toggle');

		var data={
				"project_id":$("#project_id").val(),
				"customer_email":customer_email,
				"customer_password":customer_password
		};

		$.ajax({
			url: "<?php echo site_url('portal/add_customer_to_project_chat'); ?>",
			type: 'POST',
	        data: data,
	        async: true,
	        success: function(msg) {
				location.reload();
				remove_loader();
	        }
		});
	}
</script>
  