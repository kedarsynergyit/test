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
  
        <title>Synergy Portal</title><meta charset="UTF-8" />
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
  </head>

  <body>

      <!-- **********************************************************************************************************************************************************
      MAIN CONTENT
      *********************************************************************************************************************************************************** -->

	  <div id="login-page">
	  	<div class="container">
	  	
                    <form class="form-login" action="<?php echo base_url(); ?>index.php/portal/updatepassword" method="post">
		        <h2 class="form-login-heading">reset password (Employee)</h2>
                   <?php     if($this->session->flashdata('error_message')) {
				   			?><br /><center><span class="error_flash"><?php echo $this->session->flashdata('error_message'); ?></span></center><?php 
                   }
?>
		        <div class="login-wrap">
                            <input type="password" class="form-control" placeholder="New Password" name="new_password" id="new_password">
		            <br />
                            <input type="password" class="form-control" placeholder="Confirm Password" name="confirm_password" id="confirm_password">
		            <br />
		            		<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>">
		            		<button class="btn btn-theme btn-block" type="submit">SUBMIT</button>
		            <hr>
		            
		         
		            <div class="registration">
		                @Powered By<br/>
		                <a class="" href="#">
		                   Platinait
		                </a>
		            </div>
		
		        </div>
		        </form>
		      	  	
	  	
	  	</div>
	  </div>

    <!-- js placed at the end of the document so the pages load faster -->
    <script src="<?php echo base_url(); ?>assets/js/jquery.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>

    <!--BACKSTRETCH-->
    <!-- You can use an image of whatever size. This script will stretch to fit in any screen size.-->
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.backstretch.min.js"></script>
    <script>
       // $.backstretch("<?php echo base_url(); ?>assets/img/login-bg.jpg", {speed: 500});
         $.backstretch("<?php echo base_url(); ?>assets/img/stafflogin-bg.png", {speed: 500});
    </script>


  </body>
</html>
<?php // $this->output->enable_profiler(TRUE); ?>