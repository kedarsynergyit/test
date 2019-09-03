<?php
if (!ini_get('date.timezone')) {
    date_default_timezone_set('UTC');
} else {
    
}
?>


<?php ob_start(); ?>



<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Synergy Portal
        </title>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-responsive.min.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/fullcalendar.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/matrix-style.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/matrix-media.css" />
        <link href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/jquery.gritter.css" />
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-theme.min.css">

        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-wysihtml5.css" />

        <script src="<?php echo base_url(); ?>assets/js/jquery.min.js">
        </script> 
        <style>
            .welcome{
                color:white;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <!--Header-part-->
        <div id="header">
            <h1>
                <a href="<?php echo base_url(); ?>index.php/portal/customerdashboard">Synergy Portal
                </a>
            </h1>
        </div>
        <!--close-Header-part--> 
        <!--top-Header-menu-->

        <div id="user-nav" class="navbar navbar-inverse">

            <ul class="nav">
                <li  class="dropdown" id="profile-messages" >

                    <a title="" href="#" data-toggle="dropdown" data-target="#profile-messages" class="dropdown-toggle">
                        <i class="icon icon-user">
                        </i>  
                        <span class="text">My Account 

                        </span>

                        <b class="caret">
                        </b>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?php echo base_url(); ?>index.php/portal/customerprofile">
                                <i class="icon-user">
                                </i> My Profile
                            </a>
                        </li>
                        <li class="divider">
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>index.php/portal/logout">
                                <i class="icon-key">
                                </i> Log Out
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <!--close-top-Header-menu-->
        <!--sidebar-menu-->
        <div id="sidebar">
            <p class="welcome">Welcome, <?php echo $this->session->userdata('name'); ?> !</p>
            <a href="<?php echo base_url(); ?>index.php/portal/customerdashboard" class="visible-phone">
                <i class="icon icon-home">
                </i> Dashboard
            </a>
            <ul>
                <li  class="<?php if ($this->uri->uri_string() == 'portal/customerdashboard') {
    echo 'active';
} ?>">
                    <a href="<?php echo base_url(); ?>index.php/portal/customerdashboard">
                        <i class="icon icon-home">
                        </i> 
                        <span>Dashboard
                        </span>
                    </a> 
                </li>
                <li  class="<?php if ($this->uri->uri_string() == 'portal/customerprojects') {
    echo 'active';
} ?>">
                    <a href="<?php echo base_url(); ?>index.php/portal/customerprojects">
                        <i class="icon icon-inbox">
                        </i> 
                        <span>Projects
                        </span>
                    </a> 
                </li>
                <li  class="<?php if ($this->uri->uri_string() == 'portal/customertasks') {
    echo 'active';
} ?>"> 
                    <a href="<?php echo base_url(); ?>index.php/portal/customertasks">
                        <i class="icon icon-inbox">
                        </i> 
                        <span>Tickets
                        </span>
                    </a> 
                </li>   
            </ul>
        </div>
        <!--sidebar-menu-->

        <link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css"
              rel = "stylesheet">
        <script src = "https://code.jquery.com/jquery-1.10.2.js"></script>
        <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>


        <script type="text/javascript">
                $(function () {
                    $("#start_add").datepicker({
                        dateFormat: 'yy-mm-dd'
                    });
                    $("#end_add").datepicker({
                        dateFormat: "yy-mm-dd"
                    });

                });
        </script>

        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>

<?php //$this->output->enable_profiler(TRUE); ?>