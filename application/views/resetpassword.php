<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">  

    <style>
        .form-vertical, .form-actions{
            border-top:none;
        }
    </style> 
  
        <title>SynergyInteract</title><meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Bootstrap core CSS -->
    <link href="<?php echo base_url(); ?>assets/css/bootstrap.css" rel="stylesheet">
    <!--external css-->
    <link href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
        
    <!-- Custom styles for this template -->
    <link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/style-responsive.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="<?php echo base_url(); ?>assets/js/jquery.js"></script>
    <script>
    $(document).bind("contextmenu",function(e) {
		e.preventDefault();
	});
    $(document).keydown(function(e){
        if(e.which === 123){
           return false;
        }
    });
    </script>
    
  </head>

  <body style="background-color: #f0f5f8;">

      <!-- **********************************************************************************************************************************************************
      MAIN CONTENT
      *********************************************************************************************************************************************************** -->

	  <div id="login-page">
	  	<div class="container">
	  	
	  		<div style="margin: 0 auto; max-width: 330px; margin-top: 75px;">
  				<img src="<?php echo base_url(); ?>assets/img/login_logo.png"/>
  			</div>

			<form class="form-login" action="<?php echo base_url(); ?>index.php/customerportal/updatepassword" method="post">
				<h2 class="form-login-heading">reset password</h2>
                   
				<?php if($this->session->flashdata('error_message')) { ?>
					<br /><center><span class="error_flash"><?php echo $this->session->flashdata('error_message'); ?></span></center>
				<?php } ?>
				
		        <div class="login-wrap">
					
					<input type="password" class="form-control" placeholder="New Password" name="new_password" id="new_password">
		            <br />
					
					<input type="password" class="form-control" placeholder="Confirm Password" name="confirm_password" id="confirm_password">
		            <br />
            		
					<?php if(isset($customer_id)){ ?>
            			<input type="hidden" name="customer_id" id="customer_id" value="<?php echo $customer_id; ?>">
            		<?php }else if(isset($customer_user_id)){ ?>
            			<input type="hidden" name="customer_user_id" id="customer_user_id" value="<?php echo $customer_user_id; ?>">
            		<?php }else if(isset($user_id)){ ?>
            			<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>">
            		<?php } ?>
            		
            		<center>
            			<button class="btn btn-default" type="submit">SUBMIT</button>
            		</center>
		            
		            <hr>
		            
		            <div class="registration">
		                @Powered By<br/>
		                <a class="" target="_blank" href="http://www.synergyit.ca/">
		                   SynergyIT
		                </a>
		            </div>
		
		        </div>
		        
			</form>
		    
		</div>
	</div>

    <!-- js placed at the end of the document so the pages load faster -->
    <?php /* ?><script src="<?php echo base_url(); ?>assets/js/jquery.js"></script><?php */ ?>
    <script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>

    <!--BACKSTRETCH-->
    <!-- You can use an image of whatever size. This script will stretch to fit in any screen size.-->
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.backstretch.min.js"></script>
    <script>
       // $.backstretch("<?php echo base_url(); ?>assets/img/login-bg.jpg", {speed: 500});
       // $.backstretch("<?php echo base_url(); ?>assets/img/stafflogin-bg.png", {speed: 500});
    </script>


  </body>
</html>
<?php // $this->output->enable_profiler(TRUE); ?>