<?php
function get_url( $url,$timeout = 5 ){
        $url = str_replace( "&amp;", "&", urldecode(trim($url)) );
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_ENCODING, "" );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
        curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
        curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
        $content = curl_exec( $ch );
        //$response = curl_getinfo( $ch ); 
        curl_close ( $ch );
        return $content;
    }
 function extractDocxText($url,$file_name){
 	
 	$arrfile_name = explode("/", $file_name);
 	$file_name = $arrfile_name[1];
 	
        $docx = get_url($url);
        file_put_contents("tempf.docx",$docx);
        $xml_filename = "word/document.xml"; //content file name
        $zip_handle = new ZipArchive;
        $output_text = "";
        if(true === $zip_handle->open("tempf.docx")){
            if(($xml_index = $zip_handle->locateName($xml_filename)) !== false){
                $xml_datas = $zip_handle->getFromIndex($xml_index);
                //file_put_contents($input_file.".xml",$xml_datas);
                $replace_newlines = preg_replace('/<w:p w[0-9-Za-z]+:[a-zA-Z0-9]+="[a-zA-z"0-9 :="]+">/',"\n\r",$xml_datas);
                $replace_tableRows = preg_replace('/<w:tr>/',"\n\r",$replace_newlines);
                $replace_tab = preg_replace('/<w:tab\/>/',"\t",$replace_tableRows);
                $replace_paragraphs = preg_replace('/<\/w:p>/',"\n\r",$replace_tab);
                $replace_other_Tags = strip_tags($replace_paragraphs);          
                $output_text = $replace_other_Tags;
            }else{
                $output_text .="";
            }
            $zip_handle->close();
        }else{
        $output_text .=" ";
        }
        chmod("tempf.docx", 0777);  unlink(realpath("tempf.docx"));
        //save to file or echo content
        //file_put_contents($file_name,$output_text);
        echo nl2br($output_text);
    } 
?>   
      <!-- **********************************************************************************************************************************************************
      MAIN CONTENT
      *********************************************************************************************************************************************************** -->
      <!--main content start-->
      <section id="main-content">
         
          <section class="wrapper site-min-height">
             <div class="row">
                  <div class="col-lg-9 main-chart">  
                       
                 <p class="text-left"><a href="dashboard" ><span class="badge bg-theme">Back</span></a></p>
          
                  	<div class="row mt">
                  	<div class="col-lg-12">
                            
                    <!-- Advanced Tables -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                             Blog Details
                        </div>
                        <div class="panel-body">                               
                             <div class="col-lg-12 ml-15">
                                        <div class="form-group">
                                            <?php /* ?><div>Description</div><?php */ ?>
                                            <h4 class="text-overflow-wrap"><?php  echo $news['news_text']; ?></h4>
                                        </div>
                                        
                                        <!-- displaying task images -->
                                        <?php  if(!empty($news['image'])) { ?>
	                                   <div class="table-responsive">
	                                       <table class="table table-striped  table-hover"  >
	                                        <tr>
	                                        	<td class="text-center imagetd" style="border-top:0px;">
	                                        		<?php
	                                        		$endexdt= pathinfo($news['image'], PATHINFO_EXTENSION);
	                                        		if($endexdt=='jpg'||$endexdt=='jpeg'||$endexdt=='png'||$endexdt=='gif'){ 
	                                        		?>
	                                        			<img data-toggle="modal" data-target="#myModal" src="<?php echo base_url(); ?><?php echo $news['image']; ?>"  width="100" height="100">
	                                        		<?php
	                                        		}else{
	                                        			$mimetype = mime_content_type($news['image']);
	                                        			if(preg_match('/video\/*/',$mimetype)){
	                                        				?>
	                                        				 <video width="320" height="240" controls>
															  <source src="<?php echo base_url().$news['image']; ?>" type="<?php echo $mimetype; ?>">
																Your browser does not support the video tag.
															</video> 
	                                        				<?php
	                                        			}
	                                        		} /*else { ?>
														<i  class="fa fa-file fa-3" aria-hidden="true"></i> <a target="_blank" href="<?php echo base_url().$news['image']; ?>"><b>Download <?php echo strtoupper($endexdt); ?> File</b></a>
														<?php
	                                        		}*/ 
													?>
	                                        	</td>
	                                        </tr>
	                                    </table>
	                                    
	                                    <?php
	                                    if($endexdt=="pdf"){
											// view it in iframe
											?>
											<iframe src="<?php echo base_url().$news['image']; ?>" width="100%" style="height:500px;"></iframe>
											<?php
										}else if($endexdt=="docx"){
											//echo "<br /><b><h3><u>File Content</u></h3></b><br />";
											extractDocxText(base_url().$news['image'],$news['image']);
										} 
	                                    ?>
	                                    
	                                   </div>
	                                    <?php } ?>
	                                    
	                                    <div class="form-group">
                                            <div class="pull-right"><h6><?php echo "- created by ".$news['created_by']." on ".date("Y-m-d h:iA",strtotime($news['created_on'])); ?></h6></div>
                                        </div>
                                        
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
                  
              </div><!--/row -->
          </section>
      </section>
  <div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times</button>
          </div>
            <div class="modal-body">
                <img class="img-responsive" src="" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>    
 <script src="<?php echo base_url(); ?>assets/js/jquery.js"></script>
      <!--main content end-->
 <script type="text/javascript">
$(document).ready(function () {
    $('img').on('click', function () {
        var image = $(this).attr('src');
        $('#myModal').on('show.bs.modal', function () {
            $(".img-responsive").attr("src", image);
        });
    });
});
</script>
  