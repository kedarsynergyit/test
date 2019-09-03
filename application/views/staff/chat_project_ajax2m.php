<?php
if(isset($chat_data) && !empty($chat_data)){
	foreach ($chat_data as $each_chat){
		?>
		<div class="form-group chat_div <?php echo ($each_chat['fk_user_id']==$logged_in_userid)?"pull-right":"pull-left"; ?>">
			<label class="chat_by"><?php echo $each_chat['first_name']." ".$each_chat['last_name']; ?> <span class="chat_time">(<?php echo date("Y-m-d h:i A",strtotime($each_chat['created_on'])); ?>)</span></label>
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
}else{
	?>
	<div class="form-group chat_div">
		<label>No past chats! Start your conversation now...</label>
	</div>
	<?php
}
?>