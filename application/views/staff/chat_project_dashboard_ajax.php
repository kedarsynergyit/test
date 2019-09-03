<?php
if(isset($chat_data) && !empty($chat_data)){
	foreach ($chat_data as $each_chat){
		?>
		<div class="desc">
			<p>
				<?php if(isset($each_chat['filepath']) && !empty($each_chat['filepath'])){ ?>
					<i class="fa fa-file-o" aria-hidden="true"></i>
					<a href="<?php echo $this->config->item('base_url').$each_chat['filepath']; ?>" target="_blank">
						<?php echo nl2br($each_chat['message']); ?>
					</a>
				<?php }else{ ?>
					<?php echo nl2br($each_chat['message']); ?>
				<?php } ?>
			</p>
			<p class="pull-right">
				<i>- <?php 
					//echo (!empty($each_chat['fk_user_id']))?$each_chat['first_name']." ".$each_chat['last_name']:$each_chat['companyname'];
					if(!empty($each_chat['fk_user_id'])){
						echo $each_chat['first_name']." ".$each_chat['last_name'];
					}else if(!empty($each_chat['fk_customer_id'])){
						echo $each_chat['companyname'];
					}else{
						echo $each_chat['email'];
					}
				?>
				<br /><?php echo date("Y-m-d h:i A",strtotime($each_chat['created_on'])); ?></i></p>
		</div>
		<?php
	}
}else{
	?>
	<div class="desc">
		<p>No project chat!</p>
	</div>
	<?php
}
?>