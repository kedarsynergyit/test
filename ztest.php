<?php
$post_data = "";
$post_url = "";
$result_data = "";

if($_SERVER['REQUEST_METHOD'] == "POST") {
	$post_data = $_POST['data'];
	$post_url = $_POST['url'];
	
	if(get_magic_quotes_gpc()) {
		$post_data = stripslashes($post_data);
		$post_url = stripslashes($post_url);
	}
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $post_url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result_data = curl_exec($ch);
	curl_close($ch);
}
?>
<html>
<head>
</head>
<body>
<form name="frm" id="frm" method="POST">
<table width="100%" cellpadding="5">
	<tr>
		<td>
		<input type="text" style="width:100%;" name="url" value="<?php echo $post_url; ?>" />
		</td>
	</tr>
	<tr>
		<td>
		<textarea style="width:100%;" rows="7" name="data"><?php echo $post_data; ?></textarea>
		</td>
	</tr>
	<tr>
		<td>
		<textarea style="width:100%;" rows="7" name="result"><?php echo $result_data; ?></textarea>
		</td>
	</tr>
	<tr>
		<td>
		<input type="submit" name="submitbtn" value="Submit" />
		</td>
	</tr>
</table>
</form>
</body>
</html>