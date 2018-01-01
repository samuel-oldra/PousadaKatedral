<?php
function html_showportfolios( $rows,  $pageNav,$sort,$cat_row){
	global $wpdb;
	?>
    <script language="javascript">
		function ordering(name,as_or_desc)
		{
			document.getElementById('asc_or_desc').value=as_or_desc;		
			document.getElementById('order_by').value=name;
			document.getElementById('admin_form').submit();
		}
		function saveorder()
		{
			document.getElementById('saveorder').value="save";
			document.getElementById('admin_form').submit();
			
		}
		function listItemTask(this_id,replace_id)
		{
			document.getElementById('oreder_move').value=this_id+","+replace_id;
			document.getElementById('admin_form').submit();
		}
		function doNothing() {  
			var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
			if( keyCode == 13 ) {


				if(!e) var e = window.event;

				e.cancelBubble = true;
				e.returnValue = false;

				if (e.stopPropagation) {
						e.stopPropagation();
						e.preventDefault();
				}
			}
		}
	</script>


<div class="wrap">
	<?php $path_site2 = plugins_url("../images", __FILE__); ?>
		<div class="slider-options-head">
		<div style="float: left;">
			<div><a href="http://huge-it.com/wordpress-plugins-portfolio-gallery-user-manual/" target="_blank">User Manual</a></div>
			<div>This section allows you to configure the Portfolio/Gallery options. <a href="http://huge-it.com/wordpress-plugins-portfolio-gallery-user-manual/" target="_blank">More...</a></div>
		</div>
		<div style="float: right;">
			<a class="header-logo-text" href="http://huge-it.com/portfolio-gallery/" target="_blank">
				<div><img width="250px" src="<?php echo $path_site2; ?>/huge-it1.png" /></div>
				<div>Get the full version</div>
			</a>
		</div>
	</div>
	<div id="poststuff">
		<div id="portfolios-list-page">
			<form method="post"  onkeypress="doNothing()" action="admin.php?page=portfolios_huge_it_portfolio" id="admin_form" name="admin_form">
			<h2>Huge-IT Portfolios
				<a onclick="window.location.href='admin.php?page=portfolios_huge_it_portfolio&task=add_cat'" class="add-new-h2" >Add New Portfolio</a>
			</h2>
			<?php
			$serch_value='';
			if(isset($_POST['serch_or_not'])) {if($_POST['serch_or_not']=="search"){ $serch_value=esc_html(stripslashes($_POST['search_events_by_title'])); }else{$serch_value="";}} 
			$serch_fields='<div class="alignleft actions"">
				<label for="search_events_by_title" style="font-size:14px">Filter: </label>
					<input type="text" name="search_events_by_title" value="'.$serch_value.'" id="search_events_by_title" onchange="clear_serch_texts()">
			</div>
			<div class="alignleft actions">
				<input type="button" value="Search" onclick="document.getElementById(\'page_number\').value=\'1\'; document.getElementById(\'serch_or_not\').value=\'search\';
				 document.getElementById(\'admin_form\').submit();" class="button-secondary action">
				 <input type="button" value="Reset" onclick="window.location.href=\'admin.php?page=portfolios_huge_it_portfolio\'" class="button-secondary action">
			</div>';
			?>
			<table class="wp-list-table widefat fixed pages" style="width:95%">
				<thead>
				 <tr>
					<th scope="col" id="id" style="width:30px" ><span>ID</span><span class="sorting-indicator"></span></th>
					<th scope="col" id="name" style="width:85px" ><span>Name</span><span class="sorting-indicator"></span></th>
					<th scope="col" id="prod_count"  style="width:75px;" ><span>Images</span><span class="sorting-indicator"></span></th>
					<th style="width:40px">Delete</th>
				 </tr>
				</thead>
				<tbody>
				 <?php 
				 $trcount=1;
				  for($i=0; $i<count($rows);$i++){
					$trcount++;
					$ka0=0;
					$ka1=0;
					if(isset($rows[$i-1]->id)){
						  if($rows[$i]->sl_width==$rows[$i-1]->sl_width){
						  $x1=$rows[$i]->id;
						  $x2=$rows[$i-1]->id;
						  $ka0=1;
						  }
						  else
						  {
							  $jj=2;
							  while(isset($rows[$i-$jj]))
							  {
								  if($rows[$i]->sl_width==$rows[$i-$jj]->sl_width)
								  {
									  $ka0=1;
									  $x1=$rows[$i]->id;
									  $x2=$rows[$i-$jj]->id;
									   break;
								  }
								$jj++;
							  }
						  }
						  if($ka0){
							$move_up='<span><a href="#reorder" onclick="return listItemTask(\''.$x1.'\',\''.$x2.'\')" title="Move Up">   <img src="'.plugins_url('images/uparrow.png',__FILE__).'" width="16" height="16" border="0" alt="Move Up"></a></span>';
						  }
						  else{
							$move_up="";
						  }
					}else{$move_up="";}
					
					
					if(isset($rows[$i+1]->id)){
						
						if($rows[$i]->sl_width==$rows[$i+1]->sl_width){
						  $x1=$rows[$i]->id;
						  $x2=$rows[$i+1]->id;
						  $ka1=1;
						}
						else
						{
							  $jj=2;
							  while(isset($rows[$i+$jj]))
							  {
								  if($rows[$i]->sl_width==$rows[$i+$jj]->sl_width)
								  {
									  $ka1=1;
									  $x1=$rows[$i]->id;
									  $x2=$rows[$i+$jj]->id;
									  break;
								  }
								$jj++;
							  }
						}
						
						if($ka1){
							$move_down='<span><a href="#reorder" onclick="return listItemTask(\''.$x1.'\',\''. $x2.'\')" title="Move Down">  <img src="'.plugins_url('images/downarrow.png',__FILE__).'" width="16" height="16" border="0" alt="Move Down"></a></span>';
						}else{
							$move_down="";	
						}
					}

					$uncat=$rows[$i]->par_name;
					if(isset($rows[$i]->prod_count))
						$pr_count=$rows[$i]->prod_count;
					else
						$pr_count=0;


					?>
					<tr <?php if($trcount%2==0){ echo 'class="has-background"';}?>>
						<td><?php echo $rows[$i]->id; ?></td>
						<td><a  href="admin.php?page=portfolios_huge_it_portfolio&task=edit_cat&id=<?php echo $rows[$i]->id?>"><?php echo esc_html(stripslashes($rows[$i]->name)); ?></a></td>
						<td>(<?php if(!($pr_count)){echo '0';} else{ echo $rows[$i]->prod_count;} ?>)</td>
						<td><a  href="admin.php?page=portfolios_huge_it_portfolio&task=remove_cat&id=<?php echo $rows[$i]->id?>">Delete</a></td>
					</tr> 
				 <?php } ?>
				</tbody>
			</table>
			 <input type="hidden" name="oreder_move" id="oreder_move" value="" />
			 <input type="hidden" name="asc_or_desc" id="asc_or_desc" value="<?php if(isset($_POST['asc_or_desc'])) echo $_POST['asc_or_desc'];?>"  />
			 <input type="hidden" name="order_by" id="order_by" value="<?php if(isset($_POST['order_by'])) echo $_POST['order_by'];?>"  />
			 <input type="hidden" name="saveorder" id="saveorder" value="" />

			 <?php
			?>
			
			
		   
			</form>
		</div>
	</div>
</div>
    <?php

}
function Html_editportfolio($ord_elem, $count_ord,$images,$row,$cat_row, $rowim, $rowsld, $paramssld, $rowsposts, $rowsposts8, $postsbycat)

{
 global $wpdb;
	
	if(isset($_GET["addslide"])){
	if($_GET["addslide"] == 1){
	header('Location: admin.php?page=portfolios_huge_it_portfolio&id='.$row->id.'&task=apply');
	}
	}
		
	
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	if(!document.getElementById('name').value){
	alert("Name is required.");
	return;
	
	}
	
	document.getElementById("adminForm").action=document.getElementById("adminForm").action+"&task="+pressbutton;
	document.getElementById("adminForm").submit();
	
}
function change_select()
{
		submitbutton('apply'); 
	
}
jQuery(function() {
	jQuery( "#images-list" ).sortable({
	  stop: function() {
			jQuery("#images-list > li").removeClass('has-background');
			count=jQuery("#images-list > li").length;
			for(var i=0;i<=count;i+=2){
					jQuery("#images-list > li").eq(i).addClass("has-background");
			}
			jQuery("#images-list > li").each(function(){
				jQuery(this).find('.order_by').val(jQuery(this).index());
			});
	  },
	  revert: true
	});
   // jQuery( "ul, li" ).disableSelection();
	});
</script>

<!-- GENERAL PAGE, ADD IMAGES PAGE -->

	
<div class="wrap">
<?php $path_site2 = plugins_url("../images", __FILE__); ?>
	<div class="slider-options-head">
		<div style="float: left;">
			<div><a href="http://huge-it.com/wordpress-plugins-portfolio-gallery-user-manual/" target="_blank">User Manual</a></div>
			<div>This section allows you to configure the Portfolio/Gallery options. <a href="http://huge-it.com/wordpress-plugins-portfolio-gallery-user-manual/" target="_blank">More...</a></div>
		</div>
		<div style="float: right;">
			<a class="header-logo-text" href="http://huge-it.com/portfolio-gallery/" target="_blank">
				<div><img width="250px" src="<?php echo $path_site2; ?>/huge-it1.png" /></div>
				<div>Get the full version</div>
			</a>
		</div>
	</div>
<form action="admin.php?page=portfolios_huge_it_portfolio&id=<?php echo $row->id; ?>" method="post" name="adminForm" id="adminForm">
	<div id="poststuff" >
	<div id="portfolio-header">
		<ul id="portfolios-list">
			
			<?php
			foreach($rowsld as $rowsldires){
				if($rowsldires->id != $row->id){
				?>
					<li>
						<a href="#" onclick="window.location.href='admin.php?page=portfolios_huge_it_portfolio&task=edit_cat&id=<?php echo $rowsldires->id; ?>'" ><?php echo $rowsldires->name; ?></a>
					</li>
				<?php
				}
				else{ ?>
					<li class="active" style="background-image:url(<?php echo plugins_url('../images/edit.png', __FILE__) ;?>)">
						<input class="text_area" onfocus="this.style.width = ((this.value.length + 1) * 8) + 'px'" type="text" name="name" id="name" maxlength="250" value="<?php echo esc_html(stripslashes($row->name));?>" />
					</li>
				<?php	
				}
			}
		?>
			<li class="add-new">
				<a onclick="window.location.href='admin.php?page=portfolios_huge_it_portfolio&amp;task=add_cat'">+</a>
			</li>
		</ul>
		</div>
		<div id="post-body" class="metabox-holder columns-2">
			<!-- Content -->
			<div id="post-body-content">


			<?php add_thickbox(); ?>

				<div id="post-body">
					<div id="post-body-heading">
						<h3>Projects / Images</h3>
							<script>
jQuery(document).ready(function($){


	 

  jQuery('.huge-it-newuploader .button').click(function(e) {
    var send_attachment_bkp = wp.media.editor.send.attachment;
	
    var button = jQuery(this);
    var id = button.attr('id').replace('_button', '');
    _custom_media = true;

	jQuery("#"+id).val('');
	wp.media.editor.send.attachment = function(props, attachment){
      if ( _custom_media ) {
	     jQuery("#"+id).val(attachment.url+';;;'+jQuery("#"+id).val());
		 jQuery("#save-buttom").click();
      } else {
        return _orig_send_attachment.apply( this, [props, attachment] );
      };
    }
  
    wp.media.editor.open(button);
	 
    return false;
  });
  
  	/*#####HIDE NEW UPLOADER'S LEFT MENU######*/  
										jQuery(".wp-media-buttons-icon").click(function() {
											jQuery(".media-menu .media-menu-item").css("display","none");
											jQuery(".media-menu-item:first").css("display","block");
											jQuery(".separator").next().css("display","none");
											jQuery('.attachment-filters').val('image').trigger('change');
											jQuery(".attachment-filters").css("display","none");
										});

});
</script>

						<input type="hidden" name="imagess" id="_unique_name" />
						<span class="wp-media-buttons-icon"></span>
						<div class="huge-it-newuploader uploader button button-primary add-new-image">
						<input type="button" class="button wp-media-buttons-icon" name="_unique_name_button" id="_unique_name_button" value="Add Project / Image" />
						</div>
				
					</div>
					<ul id="images-list">
                                        <?php
                                        $j=2;
					                                        
                                        $myrows = explode(",",$row->categories);

					foreach ($rowim as $key=>$rowimages){ ?>
                                            
						<li <?php if($j%2==0){echo "class='has-background'";}$j++; ?>>
							<input class="order_by" type="hidden" name="order_by_<?php echo $rowimages->id; ?>" value="<?php echo $rowimages->ordering; ?>" />
							<div class="image-container">
								<ul class="widget-images-list">
									<?php $imgurl=explode(";",$rowimages->image_url);
									array_pop($imgurl);
									$i=0;
									//$imgurl = array_reverse($imgurl);
									foreach($imgurl as $key1=>$img)
									{	?>
										<li class="editthisimage<?php echo $key; ?> <?php if($i==0){echo 'first';} ?>">
											<img src="<?php echo $img; ?>" />
											<input type="button" class="edit-image"  id="" value="Edit" />
											<a href="#remove" class="remove-image">remove</a>	
										</li>
									<?php $i++; } ?>

									<li class="add-image-box">
										<img src="<?php echo plugins_url( '../images/plus.png', __FILE__ ) ?>" class="plus" alt="" />
										<input type="hidden" name="imagess<?php echo $rowimages->id; ?>" id="unique_name<?php echo $rowimages->id; ?>" class="all-urls" value="<?php echo $rowimages->image_url; ?>" />
										<input type="button" class="button<?php echo $rowimages->id; ?> wp-media-buttons-icon add-image"  id="unique_name_button<?php echo $rowimages->id; ?>" value="+" />	
									</li>
								</ul>
								<script>
									jQuery(document).ready(function($){
										function secondimageslistlisize(){
											var lisaze = jQuery('#images-list').width();
											lisaze=lisaze*0.06;
											jQuery('#images-list .widget-images-list li').not('.add-image-box').not('.first').height(lisaze);
										}
                                                                                secondimageslistlisize();
										jQuery(".wp-media-buttons-icon").click(function() {
											jQuery(".attachment-filters").css("display","none");
										});
									  var _custom_media = true,
										  _orig_send_attachment = wp.media.editor.send.attachment;
										 
										/*#####ADD NEW PROJECT######*/ 
										jQuery('.huge-it-newuploader .button').click(function(e) {
											var send_attachment_bkp = wp.media.editor.send.attachment;
											var button = jQuery(this);
											var id = button.attr('id').replace('_button', '');
											_custom_media = true;

											jQuery("#"+id).val('');
											wp.media.editor.send.attachment = function(props, attachment){
											  if ( _custom_media ) {
												 jQuery("#"+id).val(attachment.url+';;;'+jQuery("#"+id).val());
												 jQuery("#save-buttom").click();
											  } else {
												return _orig_send_attachment.apply( this, [props, attachment] );
											  };
											}
											wp.media.editor.open(button);
											return false;
										});
										  
										/*#####EDIT IMAGE######*/  
										jQuery('.widget-images-list').on('click','.edit-image',function(e) {
											var send_attachment_bkp = wp.media.editor.send.attachment;
											var button = jQuery(this);
											var id = button.parents('.widget-images-list').find('.all-urls').attr('id');
											var img= button.prev('img');
											_custom_media = true;
											jQuery(".media-menu .media-menu-item").css("display","none");
											jQuery(".media-menu-item:first").css("display","block");
											jQuery(".separator").next().css("display","none");
											jQuery('.attachment-filters').val('image').trigger('change');
											jQuery(".attachment-filters").css("display","none");
											wp.media.editor.send.attachment = function(props, attachment){
											  if ( _custom_media ) {	 
												 img.attr('src',attachment.url);
												 var allurls ='';
												 img.parents('.widget-images-list').find('img').not('.plus').each(function(){
													allurls = allurls+jQuery(this).attr('src')+';';
												 });
												 jQuery("#"+id).val(allurls);
												 secondimageslistlisize();
												 //jQuery("#save-buttom").click();
											  } else {
												return _orig_send_attachment.apply( this, [props, attachment] );
											  };
											}
											wp.media.editor.open(button);
											return false;
										});

										jQuery('.add_media').on('click', function(){
											_custom_media = false;
										});
										
										 /*#####ADD IMAGE######*/  
										jQuery('.add-image.button<?php echo $rowimages->id; ?>').click(function(e) {
											var send_attachment_bkp = wp.media.editor.send.attachment;

											var button = jQuery(this);
											var id = button.attr('id').replace('_button', '');
											_custom_media = true;

											wp.media.editor.send.attachment = function(props, attachment){
											  if ( _custom_media ) {
													jQuery("#"+id).parent().before('<li class="editthisimage1 "><img src="'+attachment.url+'" alt="" /><input type="button" class="edit-image"  id="" value="Edit" /><a href="#remove" class="remove-image">remove</a></li>');
													//alert(jQuery("#"+id).val());
													jQuery("#"+id).val(jQuery("#"+id).val()+attachment.url+';');
													
													secondimageslistlisize();

											  } else {
												return _orig_send_attachment.apply( this, [props, attachment] );
											  };
											}

											wp.media.editor.open(button);
											 
											return false;
										});

										
										/*#####REMOVE IMAGE######*/  
										jQuery("ul.widget-images-list").on('click','.remove-image',function () {	
											jQuery(this).parent().find('img').remove();
											
											var allUrls="";
											
											jQuery(this).parents('ul.widget-images-list').find('img').not('.plus').each(function(){
												allUrls=allUrls+jQuery(this).attr('src')+';';
												jQuery(this).parent().parent().parent().find('input.all-urls').val(allUrls);
												secondimageslistlisize();
											});					
											jQuery(this).parent().remove();
											return false;
										});
										

										/*#####HIDE NEW UPLOADER'S LEFT MENU######*/  
										jQuery(".wp-media-buttons-icon").click(function() {
											jQuery(".media-menu .media-menu-item").css("display","none");
											jQuery(".media-menu-item:first").css("display","block");
											jQuery(".separator").next().css("display","none");
											jQuery('.attachment-filters').val('image').trigger('change');
											jQuery(".attachment-filters").css("display","none");
										});
									});
								</script>
							</div>
							<div class="image-options">
								<div class="options-container">
									<div>
										<label for="titleimage<?php echo $rowimages->id; ?>">Title:</label>
										<input  class="text_area" type="text" id="titleimage<?php echo $rowimages->id; ?>" name="titleimage<?php echo $rowimages->id; ?>" id="titleimage<?php echo $rowimages->id; ?>"  value="<?php echo $rowimages->name; ?>">
									</div>
									<div class="description-block">
										<label for="im_description<?php echo $rowimages->id; ?>">Description:</label>
										<textarea id="im_description<?php echo $rowimages->id; ?>" name="im_description<?php echo $rowimages->id; ?>" ><?php echo $rowimages->description; ?></textarea>
									</div>
									<div class="link-block">
										<label for="sl_url<?php echo $rowimages->id; ?>">URL:</label>
										<input class="text_area url-input" type="text" id="sl_url<?php echo $rowimages->id; ?>" name="sl_url<?php echo $rowimages->id; ?>"  value="<?php echo $rowimages->sl_url; ?>" >
										<label class="long" for="sl_link_target<?php echo $rowimages->id; ?>">
											<span>Open in new tab</span>
											<input type="hidden" name="sl_link_target<?php echo $rowimages->id; ?>" value="" />
											<input  <?php if($rowimages->link_target == 'on'){ echo 'checked="checked"'; } ?>  class="link_target" type="checkbox" id="sl_link_target<?php echo $rowimages->id; ?>" name="sl_link_target<?php echo $rowimages->id; ?>" />
										</label>
									</div>
								</div>
								<div class="category-container">
                                                                    <strong>Select Categories</strong>
                                                                    <em>(Press Ctrl And Select multiply)</em>
                                                                    <select id="multipleSelect" multiple="multiple" disabled="disabled" >
                                                                            <?php
                                                                            $huge_cat = explode(",",$rowimages->category);
                                                                            foreach ($myrows as $value) {
                                                                                if(!empty($value))
                                                                                { ?>
                                                                                    <option <?php if(in_array(str_replace(' ','_',$value),str_replace(' ','_',$huge_cat))) { echo "selected='selected' "; } ?> value="<?php echo str_replace(' ','_',$value); ?>" > <!-- attrForDelete="<?php// echo str_replace(" ","_",$value); ?>" -->
                                                                                        <?php echo str_replace('_',' ',$value); ?>
                                                                                    </option>
                                                                                <?php
                                                                                }
                                                                            }     ?>
                                                                    }
                                                                    </select>
                                                                    <input type="hidden" id="category<?php echo $rowimages->id; ?>" name="category<?php echo $rowimages->id; ?>" value="<?php echo str_replace(' ','_',$rowimages->category); ?>"/>
								</div>
								<div class="remove-image-container">
									<a class="button remove-image" href="admin.php?page=portfolios_huge_it_portfolio&id=<?php echo $row->id; ?>&task=apply&removeslide=<?php echo $rowimages->id; ?>">Remove Project</a>
								</div>
							</div>                 
							<div class="clear"></div>
						</li>
					<?php } ?>
					</ul>
				</div>

			</div>
				
                        <script>
//                                    jQuery('.category-container select').change(function(){
//                                    var cat_new_val = jQuery(this).val();
//                                    var new_cat_name = jQuery(this).parent().find('input').attr('name');
//                                    jQuery('#'+new_cat_name).attr('value',cat_new_val+',');
//                                    //console.log(cat_new_val);  console.log(new_cat_name);
//                                    });
                                      //ok  
//                                    jQuery(document).on('click', '#add_new_cat_buddon', function () {
//                                       var newCatVal =  jQuery('.inside #add_cat_input input').val(); 
//                                       if(newCatVal !== "") {
//                                           var oldValue = jQuery('.inside input:hidden').val()
//                                           var newValue = oldValue + newCatVal + ',';
//                                           //console.log(newCatVal); console.log(newValue); console.log(oldValue);
//                                           jQuery('.inside input:hidden').val(newValue.replace(/ /g,"_"));
//                                           jQuery('.inside #add_cat_input input').val('');
//                                           jQuery('.inside ul').find('#allCategories').before("\n\
//                                                        <span style='display: block;'>\n\
//                                                            <li class='hndle'>\n\
//                                                                <input class='del_val' value='"+newCatVal+"' style=''>\n\
//                                                                <span id='delete_cat' style='' value='a'>\n\
//                                                                    <img src='../wp-content/plugins/portfolio-gallery/images/delete1.png' width='9' height='9' value='a'>\n\
//                                                                </span>\n\
//                                                                <span id='edit_cat' style=''>\n\
//                                                                    <img src='../wp-content/plugins/portfolio-gallery/images/edit3.png' width='10' height='10'>\n\
//                                                                </span>\n\
//                                                            </li>\n\
//                                                       </span>");
//                                                                
//                                          jQuery('.category-container #multipleSelect').each(function(){
//                                              jQuery(this).append("<option attrForDelete='"+newCatVal+"'>"+newCatVal+"</option>");
//                                          });
//                                       }
//                                       else { alert("Please fill the line"); }
//                                    });

//                                        jQuery(document).on('click', '#delete_cat', function (){
//                                            var del_val = jQuery(this).parent().find('.del_val').val().replace(/ /g, '_');
//                                            del_val = del_val + ",";
//                                            var old_val_for_delete = jQuery('.inside input:hidden').val();
//                                            var newValue = old_val_for_delete.replace(del_val, "");
//                                            jQuery('.inside input:hidden').val(newValue);
//                                            jQuery(this).parent().parent().find('.hndle').remove();
//                                            var valForDelete = del_val.replace(',', '').replace(/ /g, '_');
//                                            jQuery('.category-container').each(function(){
//                                                jQuery(this).find('option[value='+valForDelete+']').remove();
//                                            });
//                                             //console.log(del_val); console.log(old_val_for_delete); console.log(newValue); console.log(valForDelete);
//                                        });


//                                        jQuery(document).on('click', '#edit_cat', function (){
//                                            jQuery(this).parent().find('.del_val').focus();
//                                            var changing_val = jQuery(this).parent().find('.del_val').val().replace(/ /g, '_');
//                                            jQuery('#changing_val').removeAttr('value').attr('value',changing_val);
//                                            //console.log(changing_val);
//                                        });
//
                                        
                                        jQuery(document).on('click', '#portfolios-list .active', function (){
                                            jQuery(this).find('input').focus();                                         
                                        });
                                        
                                        //getting category old name
//                                        jQuery(document).on('focus', '.del_val', function (){ // Know which category we want to change 
//                                                var changing_val = jQuery(this).val().replace(/ /g,"_");  //console.log(changing_val);
//                                                jQuery('#changing_val').removeAttr('value').attr('value',changing_val);
//                                        });
                                        
//                                        jQuery(document).on('change', '.del_val', function (){
//                                            //alert("ok")
//                                                var no_edited_cats = jQuery("#allCategories").val().replace(/ /g,"_");
//                                                var old_name = jQuery('#changing_val').val();
//                                                var edited_cat = jQuery(this).val();
//                                                edited_cat = edited_cat.replace(/ /g,"_");
//                                                var new_cat = no_edited_cats.replace(old_name,edited_cat);
//                                                jQuery('#allCategories').val(new_cat);  // console.log(no_edited_cats); console.log(old_name); console.log(edited_cat); console.log(new_cat);
//                                        });
                                        jQuery(document).on('click', '.allowIsotope input', function (){
                                            
                                        });
                        </script>
                        
			<!-- SIDEBAR -->
			<div id="postbox-container-1" class="postbox-container">
				<div id="side-sortables" class="meta-box-sortables ui-sortable">
					<div id="portfolio-unique-options" class="postbox">
					<h3 class="hndle"><span>Select The Portfolio/Gallery View</span></h3>
					<ul id="portfolio-unique-options-list">
						<li style="display:none;">
							<label for="sl_width">Width</label>
							<input type="text" name="sl_width" id="sl_width" value="<?php echo $row->sl_width; ?>" class="text_area" />
						</li>
						<li style="display:none;">
							<label for="sl_height">Height</label>
							<input type="text" name="sl_height" id="sl_height" value="<?php echo $row->sl_height; ?>" class="text_area" />
						</li>
						<li style="display:none;">
							<label for="pause_on_hover">Pause on hover</label>
							<input type="hidden" value="off" name="pause_on_hover" />					
							<input type="checkbox" name="pause_on_hover"  value="on" id="pause_on_hover"  <?php if($row->pause_on_hover  == 'on'){ echo 'checked="checked"'; } ?> />
						</li>
						<li>
							<label for="portfolio_effects_list">Views</label>
							<select name="portfolio_effects_list" id="portfolio_effects_list">
									<option <?php if($row->portfolio_list_effects_s == '0'){ echo 'selected'; } ?>  value="0">Blocks Toggle Up/Down</option>
									<option <?php if($row->portfolio_list_effects_s == '1'){ echo 'selected'; } ?>  value="1">Full-Height Blocks</option>
									<option <?php if($row->portfolio_list_effects_s == '2'){ echo 'selected'; } ?>  value="2">Gallery/Content-Popup</option>
									<option <?php if($row->portfolio_list_effects_s == '3'){ echo 'selected'; } ?>  value="3">Full-Width Blocks</option>
									<option <?php if($row->portfolio_list_effects_s == '4'){ echo 'selected'; } ?>  value="4">FAQ Toggle Up/Down</option>
									<option <?php if($row->portfolio_list_effects_s == '5'){ echo 'selected'; } ?>  value="5">Content Slider</option>
									<option <?php if($row->portfolio_list_effects_s == '6'){ echo 'selected'; } ?>  value="6">Lightbox-Gallery</option>
							</select>
						</li>

						<li style="display:none;">
							<label for="sl_pausetime">Pause time</label>
							<input type="text" name="sl_pausetime" id="sl_pausetime" value="<?php echo $row->description; ?>" class="text_area" />
						</li>
						<li style="display:none;">
							<label for="sl_changespeed">Change speed</label>
							<input type="text" name="sl_changespeed" id="sl_changespeed" value="<?php echo $row->param; ?>" class="text_area" />
						</li>
						<li style="display:none;">
							<label for="portfolio_position">portfolio Position</label>
							<select name="sl_position" id="portfolio_position">
									<option <?php if($row->sl_position == 'left'){ echo 'selected'; } ?>  value="left">Left</option>
									<option <?php if($row->sl_position == 'right'){ echo 'selected'; } ?>   value="right">Right</option>
									<option <?php if($row->sl_position == 'center'){ echo 'selected'; } ?>  value="center">Center</option>
							</select>
						</li>

					</ul>
						<div id="major-publishing-actions">
							<div id="publishing-action">
								<input type="button" onclick="submitbutton('apply')" value="Save Portfolio" id="save-buttom" class="button button-primary button-large">
							</div>
							<div class="clear"></div>
							<!--<input type="button" onclick="window.location.href='admin.php?page=portfolios_huge_it_portfolio'" value="Cancel" class="button-secondary action">-->
						</div>
					</div>
                                    
                                        <div class="postbox">
                                            <div class="inside2">
                                                <ul>
                                                    <li class="allowIsotope">
                                                        Show Sorting Buttons :
                                                        <input type="hidden" value="off" name="ht_show_sorting" />
							<input type="checkbox" id="ht_show_sorting"  <?php if($row->ht_show_sorting  == 'on'){ echo 'checked="checked"'; } ?>  name="ht_show_sorting" value="on" />
                                                    </li>
                                                    <li class="allowIsotope">
                                                        Show Categorie Buttons :
                                                        <input type="hidden" value="off" name=" " />
                                                        <input type="checkbox" id=" " name=" " value="off" disabled="disabled" />
                                                        <a class="probuttonlink" href="http://huge-it.com/portfolio-gallery/">( <span style="color: red;"> PRO </span> )</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        <div class="postbox">
                                            <h3 class="hndle"><span>Categories</span><a class="probuttonlink" href="http://huge-it.com/portfolio-gallery/">( <span style="color: red;"> PRO </span> )</a></h3>
                                            <div class="inside">
                                                <ul>
                                                <?php
                                                $ifforempty= $row->categories;
                                                $ifforempty= stripslashes($ifforempty);
                                                $ifforempty= esc_html($ifforempty);
                                                $ifforempty= empty($ifforempty);				
                                                if(!($ifforempty))
                                                {
                                                    foreach ($myrows as $value) {
                                                        if(!empty($value))
                                                        {
                                                        ?>
                                                            <span>
                                                                <li class="hndle">
                                                                    <input class="del_val" value="<?php echo str_replace("_", " ", $value); ?>" style="" disabled="disabled">
                                                                    <span id="delete_cat" style="" value="a"><img src="../wp-content/plugins/portfolio-gallery/images/delete1.png" width="9" height="9" value="a"></span>
                                                                    <span id="edit_cat" style=""><img src="../wp-content/plugins/portfolio-gallery/images/edit3.png" width="10" height="10"></span>
                                                                </li>
                                                            </span>
                                                        <?php
                                                        }
                                                    }
                                                }

                                                    ?>
                                                    <input type="hidden" value="<?php if (strpos($row->categories,',,') !== false)  { $row->categories = str_replace(",,",",",$row->categories); }echo $row->categories; ?>" id="allCategories" name="allCategories">
                                                    <li id="add_cat_input" style="">
                                                        <input type="text" size="12" disabled="disabled">
                                                        <a style="" id="add_new_cat_buddon">+ Add New Category	</a>
                                                    </li>
                                                </ul>
                                                <input type="hidden" value="" id="changing_val">
                                            </div>
                                        </div>
                                        
					<div id="portfolio-shortcode-box" class="postbox shortcode ms-toggle">
					<h3 class="hndle"><span>Usage</span></h3>
					<div class="inside">
						<ul>
							<li rel="tab-1" class="selected">
								<h4>Shortcode</h4>
								<p>Copy &amp; paste the shortcode directly into any WordPress post or page.</p>
								<textarea class="full" readonly="readonly">[huge_it_portfolio id="<?php echo $row->id; ?>"]</textarea>
							</li>
							<li rel="tab-2">
								<h4>Template Include</h4>
								<p>Copy &amp; paste this code into a template file to include the slideshow within your theme.</p>
								<textarea class="full" readonly="readonly">&lt;?php echo do_shortcode("[huge_it_portfolio id='<?php echo $row->id; ?>']"); ?&gt;</textarea>
							</li>
						</ul>
					</div>
				</div>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" name="task" value="" />
</form>
</div>

<?php

}
?>