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
      <?php /* ?><div class=" pull-right mr-15 mt" ><a class="btn btn-theme btn-block "  href="<?php echo base_url(); ?>index.php/portal/index">Employee Login</a></div><?php */ ?>
	  <div id="login-page">
	  	<div class="container">
	  	
	  			<div style="margin: 0 auto; max-width: 330px; margin-top: 75px;">
  					<img src="<?php echo base_url(); ?>assets/img/login_logo.png"/>
  				</div>
  				
	  			<form class="form-login" action="<?php echo base_url(); ?>index.php/customerportal/user_login" method="post" id="frm-login">
	  			<h2 class="form-login-heading">sign in</h2>
				<?php
					if($this->session->flashdata('error_message')) { ?>
					<br /><center><span class="error_flash"><?php echo $this->session->flashdata('error_message'); ?></span></center>
				<?php 
					}
				?>
                        
		        <div class="login-wrap">
		        
		        	<!-- dropdown for customer / employee login -->
		        	<?php /* ?><select class="form-control" name="login_as" id="login_as">
		        		<option value="1">Customer</option>
		        		<option value="2">Employee</option>
		        	</select>
		        	<br /><?php */ ?>
                            <input type="text" class="form-control" placeholder="Email" autofocus name="userid" id="userid">
		            <br>
                            <input type="password" class="form-control" placeholder="Password" name="pswd" id="pswd">
		            <label class="checkbox">
		                <span class="pull-right">
		                    <!--<a data-toggle="modal" href="login.html#myModal"> Forgot Password?</a>
                                        <a data-toggle="modal" href="#"> Forgot Password?</a>-->
							<a id="link_forgot_password" data-toggle="modal" data-target="#myModalForgotPassword" href="#"> Forgot Password?</a>
		                </span>
		            </label>
		            	<center>
		            		<button class="btn btn-default" href="index.html" type="submit">SUBMIT</button>
		            		<button class="btn btn-default" type="reset">RESET</button>
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

<!-- Modal -->
          <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModalForgotPassword" class="modal fade">
              <div class="modal-dialog">
                  <div class="modal-content">
                  <form id="form-forgotpassword" class="form-forgotpassword" action="<?php echo base_url(); ?>index.php/customerportal/forgotpassword" method="post">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                          <h4 class="modal-title" style="color: #000000;">Forgot Password ?</h4>
                      </div>
                      <div class="modal-body">
                          <p>Enter your registered email address below to reset your password.</p>
                          <input type="text" name="email_forgotpassword" id="email_forgotpassword" placeholder="Email" autocomplete="off" class="form-control placeholder-no-fix">

                      </div>
                      <div class="modal-footer">
                          <button data-dismiss="modal" class="btn btn-default" type="button">Cancel</button>
                          <button class="btn btn-theme" type="submit">Submit</button>
                      </div>
                     </form>
                  </div>
              </div>
          </div>
          <!-- modal -->

    <!-- js placed at the end of the document so the pages load faster -->
    <script src="<?php echo base_url(); ?>assets/js/jquery.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>

    <!--BACKSTRETCH-->
    <!-- You can use an image of whatever size. This script will stretch to fit in any screen size.-->
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.backstretch.min.js"></script>
    <script>
         //  $.backstretch("<?php echo base_url(); ?>assets/img/logincustomer.png", {speed: 500});
    </script>


  </body>

<script>
$(document).ready(function () {
	$("#login_as").on("change",function(){
		if($(this).val()==2){
			// change form submit to employee login
			$("#frm-login").attr('action','<?php echo base_url(); ?>index.php/portal/user_login');

			// change forgot password url as well.
			$("#form-forgotpassword").attr('action','<?php echo base_url(); ?>index.php/portal/forgotpassword');
		}else{
			// keep customer login
			$("#frm-login").attr('action','<?php echo base_url(); ?>index.php/customerportal/user_login');

			// change forgot password url as well.
			$("#form-forgotpassword").attr('action','<?php echo base_url(); ?>index.php/customerportal/forgotpassword');
		}
		//alert($("#frm-login").attr('action'));
		//alert($("#form-forgotpassword").attr('action'));
	});

	
});
</script>

</html>
<?php //$this->output->enable_profiler(TRUE); ?>