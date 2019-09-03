<!-- create a div for showing loading image -->
<div id="page_loader_img" style="display:none;"><img style="padding-left: 46%; padding-top: 20%" src="<?php echo base_url()."assets/img/loading.gif"; ?>" /></div>

     <!--footer start-->
     <footer class="site-footer" >
          <div class="text-center">
              <?php echo date("Y"); ?> - @SynergyIT
              <a href="#main-content" class="go-top">
                  <i class="fa fa-angle-up"></i>
              </a>
          </div>
      </footer>
      <!--footer end-->
  </section>

    <!-- js placed at the end of the document so the pages load faster -->
    <?php /* ?><script src="<?php echo base_url(); ?>assets/js/jquery.js"></script><?php */ ?>
    <script src="<?php echo base_url(); ?>assets/js/jquery-ui-1.9.2.custom.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
    <script class="include" type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.dcjqaccordion.2.7.js"></script>
    
    <script src="<?php echo base_url(); ?>assets/js/jquery.scrollTo.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery.nicescroll.js" type="text/javascript"></script>
    
    <!--common script for all pages-->
    <script src="<?php echo base_url(); ?>assets/js/common-scripts.js"></script>
   
    <script src="<?php echo base_url(); ?>assets/js/dataTables/jquery.dataTables.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/dataTables/dataTables.bootstrap.js"></script>
    <script src="<?php echo base_url();?>assets/jquery-ui-1.12.0/jquery-ui.js"></script>
    <script>
$(document).ready(function () {
    $('#dataTables-example').dataTable({
        "order": []
    });

    var maxSearchLength = 30;
    $('input[aria-controls="dataTables-example"]').keyup(function() {
        var textlen = maxSearchLength - $(this).val().length;
		if (textlen < 0) {
			$(this).val($(this).val().substring(0, maxSearchLength));
		}
    });
});
$( function() {
  $( "#datepicker" ).datepicker();
});
$(function() {
	var dateToday = new Date();

	// set the 1st date of last month
	dateToday.setMonth(dateToday.getMonth()-1);
	dateToday.setDate(1);
	
    $("#dob").datepicker({
        changeMonth: true,
    	changeYear: true,
    	minDate: dateToday,
    });

    $("#doi").datepicker({
        changeMonth: true,
    	changeYear: true,
    	minDate: dateToday,
    });

    $("#news_from_date").datepicker({
        changeMonth: true,
    	changeYear: true,
    });

    $("#news_to_date").datepicker({
        changeMonth: true,
    	changeYear: true,
    });
});
$("#dobi").click(function() {
    $("#dob").datepicker("show" );
});

$("#news_from_datei").click(function() {
    $("#news_from_date").datepicker("show" );
});

$("#news_to_datei").click(function() {
    $("#news_to_date").datepicker("show" );
});

$("#doii").click(function() {
    $("#doi").datepicker("show" );
});
    </script>
         <!-- Custom Js -->
  </body>
</html>
<?php //$this->output->enable_profiler(TRUE); ?>