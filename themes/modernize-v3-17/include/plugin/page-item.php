<?php

	/*
	*	Goodlayers Page Item File
	*	---------------------------------------------------------------------
	* 	@version	1.0
	* 	@author		Goodlayers
	* 	@link		http://goodlayers.com
	* 	@copyright	Copyright (c) Goodlayers
	*	---------------------------------------------------------------------
	*	This file contains the function that can print each page item due to 
	*	different conditions.
	*	---------------------------------------------------------------------
	*/

	// Print the item size <div> with it's class
	function print_item_size($item_size, $addition_class=''){
		global $gdl_item_row_size;
		
		$gdl_item_row_size = (empty($gdl_item_row_size))? 0: $gdl_item_row_size;
		
		if($gdl_item_row_size >= 1){
		
			$gdl_item_row_size = 0;
			echo '<br class="clear">';
			
		}
		
		switch($item_size){
			case 'element1-4':
				echo '<div class="four columns ' . $addition_class . '">';
				$gdl_item_row_size += 1/4; 
				break;
			case 'element1-3':
				echo '<div class="one-third column ' . $addition_class . '">';
				$gdl_item_row_size += 1/3; 
				break;
			case 'element1-2':
				echo '<div class="eight columns ' . $addition_class . '">';
				$gdl_item_row_size += 1/2; 
				break;
			case 'element2-3':
				echo '<div class="two-thirds column ' . $addition_class . '">';
				$gdl_item_row_size += 2/3; 
				break;
			case 'element3-4':
				echo '<div class="twelve columns ' . $addition_class . '">';
				$gdl_item_row_size += 3/4; 
				break;
			case 'element1-1':
				echo '<div class="sixteen columns ' . $addition_class . '">';
				$gdl_item_row_size += 1; 
				break;	
		}
		
	}
	
	// Print column 
	function print_column_item($item_xml){
		echo do_shortcode(html_entity_decode(find_xml_value($item_xml,'column-text')));
	}
	
	if( $gdl_is_responsive ){
		$personnel_div_size_num_class = array(
			'1/4' => array( 'class'=>'four columns', 'size'=>'390x390', 'size2'=>'390x390', 'size3'=>'390x390'),
			'1/3' => array( 'class'=>'one-third column', 'size'=>'390x390', 'size2'=>'390x390', 'size3'=>'390x390'),
			'1/2' => array( 'class'=>'eight columns', 'size'=>'450x450', 'size2'=>'390x390', 'size3'=>'390x390'),
		); 	
	}else{
		$personnel_div_size_num_class = array(
			'1/4' => array( 'class'=>'four columns', 'size'=>'220x220', 'size2'=>'135x135', 'size3'=>'210x210'),
			'1/3' => array( 'class'=>'one-third column', 'size'=>'290x290', 'size2'=>'190x190', 'size3'=>'210x210'),
			'1/2' => array( 'class'=>'eight columns', 'size'=>'450x450', 'size2'=>'300x300', 'size3'=>'210x210'),
		); 			
	}	
	
	// Print personnel 
	function print_personnel($item_xml){
		global $personnel_div_size_num_class;
		global $sidebar;		

		$num_fetch = find_xml_value($item_xml, 'num-fetch');
		$personnel_size = find_xml_value($item_xml, 'item-size');
		$category = find_xml_value($item_xml, 'category', false);
		$category = ($category == 'All')? '': $category;
		if( !empty($category) ){
			$category_term = get_term_by( 'name', $category , 'personnel-category');
			$category = $category_term->slug;
		}
		
		$personnel_class = $personnel_div_size_num_class[$personnel_size]['class'];
		if( $sidebar == "no-sidebar" ){
			$item_size = $personnel_div_size_num_class[$personnel_size]['size'];
		}else if ( $sidebar == "left-sidebar" || $sidebar == "right-sidebar" ){
			$item_size = $personnel_div_size_num_class[$personnel_size]['size2'];
		}else{
			$item_size = $personnel_div_size_num_class[$personnel_size]['size3'];
		}		
		
		query_posts(array('post_type' => 'personnel', 'personnel-category'=>$category, 
			'posts_per_page'=> $num_fetch));
		
		echo '<div class="personnel-item-holder">';
		while( have_posts() ){ the_post();
			echo '<div class="' . $personnel_class . ' personnel-item gdl-divider mt25 mb20" >';

			echo '<div class="personnel-title">';
			the_title();
			echo '</div>';
			
			$position = get_post_meta( get_the_ID(), 'personnel-option-position', true );
			if( !empty($position) ){
				echo '<div class="personnel-position">' . $position . "</div>";
			}
			
			$thumbnail_id = get_post_thumbnail_id();
			$thumbnail = wp_get_attachment_image_src( $thumbnail_id , $item_size );
			$alt_text = get_post_meta($thumbnail_id , '_wp_attachment_image_alt', true);
			if( !empty($thumbnail) ){
				echo '<div class="personnel-thumbnail"><img src="' . $thumbnail[0] . '" alt="' . $alt_text . '"></div>';			
			}
			
			echo '<div class="personnel-content">';
			the_content();
			echo '</div>';
			
			echo '<div class="clear"></div>';			
			echo '</div>'; // personnel class
		}
		echo '</div>'; // personnel item holder
		
		wp_reset_query();
		
	}	

	if( $gdl_is_responsive ){
		$gallery_div_size_num_class = array(
			'1/4' => array( 'class'=>'four columns', 'size'=>'390x390', 'size2'=>'390x390', 'size3'=>'390x390'),
			'1/3' => array( 'class'=>'one-third column', 'size'=>'390x390', 'size2'=>'390x390', 'size3'=>'390x390'),
			'1/2' => array( 'class'=>'eight columns', 'size'=>'450x450', 'size2'=>'390x390', 'size3'=>'390x390'),
		); 	
	}else{
		$gallery_div_size_num_class = array(
			'1/4' => array( 'class'=>'four columns', 'size'=>'210x210', 'size2'=>'135x135', 'size3'=>'210x210'),
			'1/3' => array( 'class'=>'one-third column', 'size'=>'290x290', 'size2'=>'190x190', 'size3'=>'210x210'),
			'1/2' => array( 'class'=>'eight columns', 'size'=>'450x450', 'size2'=>'300x300', 'size3'=>'210x210'),
		); 			
	}
	
	// Print gallery
	function print_gallery_item($item_xml){
	
		global $gallery_div_size_num_class;
		global $sidebar;		

		$header = find_xml_value($item_xml, 'header');
		$gallery_page = find_xml_value($item_xml, 'page');
		$gallery_size = find_xml_value($item_xml, 'item-size');
		$gallery_caption = find_xml_value($item_xml, 'gallery-caption');
		$gallery_caption = ($gallery_caption == 'Yes')? 'caption-enable': 'caption-disable';
		
		$gallery_class = $gallery_div_size_num_class[$gallery_size]['class'];
		if( $sidebar == "no-sidebar" ){
			$item_size = $gallery_div_size_num_class[$gallery_size]['size'];
		}else if ( $sidebar == "left-sidebar" || $sidebar == "right-sidebar" ){
			$item_size = $gallery_div_size_num_class[$gallery_size]['size2'];
		}else{
			$item_size = $gallery_div_size_num_class[$gallery_size]['size3'];
		}
		
		if(!empty($header)){
			echo '<h3 class="gallery-header-title title-color gdl-title">' . $header . '</h3>';
		}	

		$gallery_post = get_posts(array('post_type' => 'gallery', 'name'=>$gallery_page, 'numberposts'=> 1));
		
		echo '<div class="gdl-gallery-item ' . $gallery_caption . '">';
		
		$slider_xml_string = get_post_meta($gallery_post[0]->ID,'post-option-gallery-xml', true);
		$slider_xml_dom = new DOMDocument();
		if( !empty( $slider_xml_string ) ){
			$slider_xml_dom->loadXML($slider_xml_string);	
			foreach( $slider_xml_dom->documentElement->childNodes as $slider ){
				$link_type = find_xml_value($slider, 'linktype');	
				$attachment_id = find_xml_value($slider, 'image');
				$image_url = wp_get_attachment_image_src($attachment_id, $item_size);
				$alt_text = get_post_meta(find_xml_value($slider, 'image') , '_wp_attachment_image_alt', true);	

				echo '<div class="' . $gallery_class . ' gallery-item-wrapper mt0 mb20" >';
				echo '<div class="gallery-thumbnail-image">';
				if( $link_type == 'Link to URL' ){
					$link = find_xml_value( $slider, 'link');	
					echo '<a href="' . $link . '">';
					echo '<img class="gdl-gallery-image" src="' . $image_url[0] . '" alt="' . $alt_text . '" />';
					echo '</a>';
				}else if( $link_type == 'Lightbox' ){
					$image_full = wp_get_attachment_image_src(find_xml_value($slider, 'image'), 'full');
					echo '<a data-rel="prettyPhoto[bkpGallery]" href="' . $image_full[0] . '" >';
					echo '<img class="gdl-gallery-image" src="' . $image_url[0] . '" alt="' . $alt_text .  '" />';
					echo '</a>';
				}else{
					echo '<img class="gdl-gallery-image" src="' . $image_url[0] . '" alt="' . $alt_text . '" />';
				}		
				echo '</div>'; // gallery-thumbnail-image
				
				if( $gallery_caption == 'caption-enable' ){
					$attachment = get_posts(array( 'post_type' => 'attachment', 
						'numberposts' => 1, 'post_status' => null, 'attachment_id' => $attachment_id ));
					
					echo '<div class="gdl-gallery-caption">';
					echo $attachment[0]->post_excerpt;
					echo '</div>';
				}
				echo '</div>';

			}
		}	
		
		
		echo '</div>';
	
	}
	
	// Print the slider item
	function print_slider_item($item_xml){
		
		$xml_size = find_xml_value($item_xml, 'size');
		if( $xml_size == 'full-width' ){
			echo '<div class="slider-wrapper fullwidth">';
		}else{
			echo '<div class="slider-wrapper">';
		}
		
		$slider_width = find_xml_value($item_xml, 'width');
		$slider_height = find_xml_value($item_xml, 'height');
		if( !empty($slider_width) && !empty($slider_height) ){
			$xml_size = $slider_width . 'x' . $slider_height;
		}else{
			$xml_size = '980x360';
		}

		switch(find_xml_value($item_xml,'slider-type')){
		
			case 'Anything Slider':
				print_anything_slider(find_xml_node($item_xml,'slider-item'), $xml_size);
				break;
				
			case 'Nivo Slider': 
				print_nivo_slider(find_xml_node($item_xml,'slider-item'), $xml_size); 
				break;
			
			case 'Flex Slider': 
				print_flex_slider(find_xml_node($item_xml,'slider-item'), $xml_size); 
				break;

		}
		
		if( find_xml_value($item_xml, 'size') == 'full-width' ){
		
			echo "<div class='slider-top-shadow slider-gimmick'></div>";
			echo "<div class='slider-bottom-shadow slider-gimmick'></div>";
			echo "<div class='slider-bottom-gimmick slider-gimmick'></div>";
		}
		
		echo "</div>";
	}
	
	// Print Content Item
	function print_content_item($item_xml){
		wp_reset_query();
		
		if(have_posts()){
			while(have_posts()){
				the_post(); 
				the_content();
			}
		}
	}
	
	// Print Accordion
	function print_accordion_item($item_xml){
	
		$tab_xml = find_xml_node($item_xml, 'tab-item');

		$header = find_xml_value($item_xml, 'header');
		if(!empty($header)){
			echo '<h3 class="accordion-header-title title-color gdl-title">' . $header . '</h3>';
		}
		
		echo "<ul class='gdl-accordion'>";
		
		foreach($tab_xml->childNodes as $accordion){
		
			echo "<li class='gdl-divider'>";
			echo "<h2 class='accordion-head title-color gdl-title'>";
			echo "<span class='accordion-head-image'></span>";
			echo find_xml_value($accordion, 'title') . "</h2>";
			echo "<div class='accordion-content' >";
			echo do_shortcode(html_entity_decode(find_xml_value($accordion, 'caption'))) . '</div>';
			echo "</li>";
			
		}
		
		echo "</ul>";
	
	}
	
	// Print Divider
	function print_divider($item_xml){
		
		echo '<div class="divider"><div class="scroll-top">';
		echo find_xml_value($item_xml, 'text');
		echo '</div></div>';
		
	}
	
	// Print Message Box
	function print_message_box($item_xml){
		$box_color = find_xml_value($item_xml, 'color');
		$box_title = find_xml_value($item_xml, 'title');
		$box_content = html_entity_decode(find_xml_value($item_xml, 'content'));
		echo '<div class="message-box-wrapper ' . $box_color . '">';
		echo '<div class="message-box-title">' . $box_title . '</div>';
		echo '<div class="message-box-content">' . $box_content . '</div>';
		echo '</div>';
	}
	
	// Print Toggle Box
	function print_toggle_box_item($item_xml){
	
		$tab_xml = find_xml_node($item_xml, 'tab-item');
		
		$header = find_xml_value($item_xml, 'header');
		if(!empty($header)){
			echo '<h3 class="toggle-box-header-title title-color gdl-title">' . $header . '</h3>';
		}
		
		echo "<ul class='gdl-toggle-box'>";
		
		foreach($tab_xml->childNodes as $toggle_box){
			
			$active = find_xml_value($toggle_box, 'active');
			
			echo "<li class='gdl-divider'>";
			echo "<h2 class='toggle-box-head title-color gdl-title'>";
			echo "<span class='toggle-box-head-image";
			echo ($active == 'Yes')? ' active':'';
			echo "' ></span>" . find_xml_value($toggle_box, 'title') . '</h2>';
			echo "<div class='toggle-box-content"; 
			echo ($active == 'Yes')? ' active': '';
			echo "' id='toggle-box-content' >";
			echo do_shortcode(html_entity_decode(find_xml_value($toggle_box, 'caption'))) . '</div>';
			echo "</li>";
		
		}
		
		echo "</ul>";
	
	}

	// Print Tab
	function print_tab_item($item_xml){
	
		$tab_xml = find_xml_node($item_xml, 'tab-item');
		$num = 0;
		$tab_title = array();
		$tab_content = array();
		
		foreach($tab_xml->childNodes as $toggle_box){
			$tab_title[$num] = find_xml_value($toggle_box, 'title');
			$tab_content[$num] = html_entity_decode(find_xml_value($toggle_box, 'caption'));
			$num++;
		}
		
		echo "<ul class='gdl-tabs gdl-divider'>";
		
		for($i=0; $i<$num; $i++){
			echo '<li><a data-href="tab-' . $i . '" class=" gdl-divider ';
			echo ( $i == 0 )? 'active':'';
			echo '" >' . $tab_title[$i] . '</a></li>';
		}
		
		echo "</ul>";
		echo "<ul class='gdl-tabs-content'>";
		
		for($i=0; $i<$num; $i++){
			echo '<li data-href="tab-' . $i . '" class="';
			echo ( $i == 0 )? 'active':'';  
			echo '" >' . do_shortcode($tab_content[$i]) . '</li>';
		}
		
		echo "</ul>";
	
	}
	
	// Print price item
	function print_price_item($item_xml){
	
		global $gdl_admin_translator;
		
		if( $gdl_admin_translator == 'enable' ){
			$translator_read_more = get_option(THEME_SHORT_NAME.'_translator_read_more_price', 'Read More');
		}else{
			$translator_read_more = __('Read More','gdl_front_end');
		}	

		$price_item_number = find_xml_value($item_xml, 'item-number');
		$price_item_category = find_xml_value($item_xml, 'category');
		$price_item_category = ($price_item_category == 'All')? '': $price_item_category;
		
		$price_posts = get_posts(array('post_type'=>'price_table', 'price-table-category'=>$price_item_category));
			
		foreach($price_posts as $price_post){
			$best_price = get_post_meta( $price_post->ID, 'price-table-best-price', true );
			$best_price = ($best_price == 'Yes')? 'active': '';
			
			echo '<div class="percent-column1-' . $price_item_number . ' gdl-divider">';
			echo '<div class="price-item ' . $best_price . '">';
			echo '<div class="price-tag">' . __(get_post_meta( $price_post->ID, 'price-table-price-tag', true ), 'gdl_front_end') . '</div>';
			echo '<div class="price-title">' . $price_post->post_title . '</div>';
			
			echo '<div class="price-content">';
			echo do_shortcode( $price_post->post_content );
			echo '</div>';
			
			$price_url = __(get_post_meta( $price_post->ID, 'price-table-option-url', true ), 'gdl_front_end');
			if( !empty($price_url) ){
				echo '<div class="price-button">';
				echo '<a class="gdl-button" href="' . $price_url . '">' . $translator_read_more . '</a>';
				echo '</div>';
			}
			echo '</div>';
			echo '</div>';
		}
	}
	
	// Print column service
	function print_column_service($item_xml){
		$column_service_img_id = find_xml_value($item_xml, 'image');
		$column_service_image = wp_get_attachment_image_src($column_service_img_id, 'full');
		$column_service_title = find_xml_value($item_xml, 'title');
		$column_service_caption = html_entity_decode(find_xml_value($item_xml, 'caption'));
		$alt_text = get_post_meta($column_service_img_id , '_wp_attachment_image_alt', true);
		
		if(!empty($column_service_image)){
			echo "<div class='column-service-image'>";
			echo "<img src='" . $column_service_image[0] . "' alt='" . $alt_text ."' />";
			echo "</div>";
		}
		
		echo '<div class="column-service-content">';
		echo '<h2 class="column-service-title gdl-title">' . $column_service_title . '</h2>';
		echo do_shortcode($column_service_caption);
		echo '</div>';
	
	}

	// Print contact form
	function print_contact_form($item_xml){
		global $post;
		
		global $gdl_admin_translator;
		
		if( $gdl_admin_translator == 'enable' ){
			$gdl_name_string = get_option(THEME_SHORT_NAME.'_translator_name_contact_form', 'Name');
			$gdl_name_error_string = get_option(THEME_SHORT_NAME.'_translator_name_error_contact_form', 'Please enter your name');
			$gdl_email_string = get_option(THEME_SHORT_NAME.'_translator_email_contact_form', 'Email');
			$gdl_email_error_string = get_option(THEME_SHORT_NAME.'_translator_email_error_contact_form', 'Please enter a valid email address');
			$gdl_message_string = get_option(THEME_SHORT_NAME.'_translator_message_contact_form', 'Message');
			$gdl_message_error_string = get_option(THEME_SHORT_NAME.'_translator_message_error_contact_form', 'Please enter message');
			$gdl_submit_button = get_option(THEME_SHORT_NAME.'_translator_submit_contact_form','Submit');
		}else{
			$gdl_name_string = __('Name','gdl_front_end');
			$gdl_name_error_string =  __('Please enter your name','gdl_front_end');
			$gdl_email_string =  __('Email','gdl_front_end');
			$gdl_email_error_string =  __('Please enter a valid email address','gdl_front_end');
			$gdl_message_string =  __('Message','gdl_front_end');
			$gdl_message_error_string = __('Please enter message','gdl_front_end');
			$gdl_submit_button = __('Submit','gdl_front_end');
		}	

	?>

		<div class="contact-form-wrapper" id="gdl-contact-form">
			<form id="gdl-contact-form">
				<ol class="forms">
					<li><strong><?php echo $gdl_name_string; ?> *</strong>
						<input type="text" name="name" class="require-field" />
						<div class="error">* <?php echo $gdl_name_error_string; ?></div>
					</li>
					<li><strong><?php echo $gdl_email_string; ?> *</strong>
						<input type="text" name="email" class="require-field email" />
						<div class="error">* <?php echo $gdl_email_error_string; ?></div>
					</li>
					<li class="textarea"><strong><?php echo $gdl_message_string; ?> *</strong>
						<textarea name="message" class="require-field"></textarea>
						<div class="error">* <?php echo $gdl_message_error_string; ?></div> 
					</li>
					<li><input type="hidden" name="receiver" value="<?php echo find_xml_value($item_xml, 'email'); ?>"></li>
					<li class="sending-result" id="sending-result" ><div class="message-box-wrapper green"></div></li>
					<li class="buttons">
						<button type="submit" class="contact-submit button"><?php echo $gdl_submit_button; ?></button>
						<div class="contact-loading" id="contact-loading">
					</li>
				</ol>
			</form>
			<div class="clear"></div>
		</div>	
	
	<?php
	}
	
	// Print stunning text
	function print_stunning_text($item_xml){
		
		$title = find_xml_value($item_xml, 'title');
		$caption = html_entity_decode(find_xml_value($item_xml, 'caption'));
		$button_title =  find_xml_value($item_xml, 'button-title');
		
		echo '<div class="stunning-text-wrapper">';
		echo '<div class="stunning-text-content-wrapper ';   
		echo empty($button_title)? 'sixteen columns': 'twelve columns';
		echo ' mt0"><h1 class="stunning-text-title">' . $title . '</h1>';
		echo '<div class="stunning-text-caption">' . do_shortcode($caption) . '</div>';
		echo '</div>';
		
		if( !empty($button_title) ){
			
			$button_margin = (int) find_xml_value($item_xml, 'button-top-margin');
			$button_margin = $button_margin - 35;
			echo '<div class="stunning-text-button-wrapper four columns mt0" >';
			echo '<a class="stunning-text-button" style="margin-top:' . $button_margin . 'px;" href="' . find_xml_value($item_xml, 'button-link') . '" >';
			echo  $button_title . '</a>';
			echo '</div>'; 
			
		}
		echo '<div class="clear"></div>';
		echo '</div>';
		
		echo '<div class="clear"></div>';
	}
	
	$gdl_div_size_num_class = array("1/4" => "four columns", "1/3" => "one-third column", "1/2" => "eight columns", 
		"2/3" => "two-thirds column", "3/4" => "twelve columns", "1/1" => "sixteen columns");
	
	// Print Testimonial
	function print_testimonial($item_xml){
		
		$display_type = find_xml_value($item_xml, 'display-type');
		$header = find_xml_value($item_xml, 'header');

		if($display_type == 'Specific Testimonial'){
			if(!empty($header)){
				echo '<h3 class="testimonial-header-title title-color gdl-title">' . $header . '</h3>';
			}
		
			$item_size = find_xml_value($item_xml, 'item-size');
			$header = find_xml_value($item_xml, 'header');
			$specific = find_xml_value($item_xml, 'specific');
			$posts = get_posts(array('post_type' => 'testimonial', 'name'=>$specific, 'numberposts'=> 1));
			
			global $gdl_div_size_num_class;
			
			echo '<div class="' . $gdl_div_size_num_class[$item_size] . '">';
			echo '<div class="testimonial-content">';
			echo '<div class="testimonial-icon"></div>';
			echo $posts[0]->post_content;
			echo '</div>';
			
			$position = __(get_post_meta($posts[0]->ID, 'testimonial-option-author-position', true), 'gdl_front_end');
			
			echo '<div class="testimonial-author gdl-divider">';
			echo '<span class="testimonial-author-name">' . $posts[0]->post_title . '</span>';
			if( !empty( $position ) ){
				echo '<span class="testimonial-author-position">, '; 
				echo $position;
				echo '</span>';
			}
			echo '</div>';
			echo '</div>'; // columns (gdl-div-size-num-class)

		}else{
		
			global $gdl_div_size_num_class;
		
			$item_size = find_xml_value($item_xml, 'item-size');
			$category = find_xml_value($item_xml, 'category');
			$category = ( $category == 'All' )? '': $category;
			$category_posts = get_posts(array('post_type'=>'testimonial', 'testimonial-category'=>$category, 'numberposts'=>100));
			
			echo '<div class="jcarousellite-nav"><div class="prev"></div><div class="next"></div></div>';
			
			if(!empty($header)){
				echo '<h3 class="testimonial-header-title title-color gdl-title">' . $header . '</h3>';
			}else{
				echo '<div class="testimonial-no-header"></div>';
			}
			
			echo '<div class="jcarousellite"><ul>';
			
			foreach( $category_posts as $category_post){
				echo '<li class="' . $gdl_div_size_num_class[$item_size] .' mt0">';

				echo '<div class="testimonial-content">';
				echo '<div class="testimonial-icon"></div>';
				echo $category_post->post_content;
				echo '</div>';

				$position = __(get_post_meta($category_post->ID, 'testimonial-option-author-position', true), 'gdl_front_end');
				
				echo '<div class="testimonial-author gdl-divider">';
				echo '<span class="testimonial-author-name">' . $category_post->post_title . '</span>';
				if( !empty( $position ) ){
					echo '<span class="testimonial-author-position">, '; 
					echo $position;
					echo '</span>';
				}
				echo '</div>';
				
				echo '</li>';
			}
			
			echo '</ul></div>';
			
		}
	
	}

	// size is when no sidebar, side2 is use when 1 sidebar, side 3 is use when 3 sidebar
	if( $gdl_is_responsive ){
		$port_div_size_num_class = array(
			"1/4" => array("class"=>"four columns", "size"=>"390x224", "size2"=>"390x245", "size3"=>"390x247"), 
			"1/3" => array("class"=>"one-third column", "size"=>"390x242", "size2"=>"390x238", "size3"=>"390x247"), 
			"1/2" => array("class"=>"eight columns", "size"=>"450x290", "size2"=>"390x247", "size3"=>"390x247"), 
			"1/1" => array("class"=>"sixteen columns", "size"=>"620x225", "size2"=>"390x182", "size3"=>"390x292"));	
	}else{
		$port_div_size_num_class = array(
			"1/4" => array("class"=>"four columns", "size"=>"210x121", "size2"=>"135x85", "size3"=>"210x135"), 
			"1/3" => array("class"=>"one-third column", "size"=>"290x180", "size2"=>"190x116", "size3"=>"210x135"), 
			"1/2" => array("class"=>"eight columns", "size"=>"450x290", "size2"=>"300x190", "size3"=>"210x135"), 
			"1/1" => array("class"=>"sixteen columns", "size"=>"620x225", "size2"=>"320x150", "size3"=>"180x135"));
	}
	$class_to_num = array(
		"element1-4" => 0.25,
		"1/4"=>0.25,
		"element1-3" => 0.33,
		"1/3"=>0.33,
		"element1-2" => 0.5,
		"1/2"=>0.5,
		"element2-3" => 0.66,
		"2/3"=>0.66,
		"element3-4" => 0.75,
		"3/4"=>0.75,
		"element1-1" => 1,
		"1/1" => 1	
	);
	// Print portfolio
	function print_portfolio($item_xml){
		
		wp_reset_query();

		// Translator words
		global $gdl_admin_translator;	
		
		if( $gdl_admin_translator == 'enable' ){
			$translator_visit_website = get_option(THEME_SHORT_NAME.'_translator_visit_website', 'Visit Website');
			$translator_view_project = get_option(THEME_SHORT_NAME.'_translator_view_all_project', 'View All Projects →');
			$translator_read_more = get_option(THEME_SHORT_NAME.'_translator_read_more', 'Read More');
		}else{
			$translator_visit_website = __('Visit Website','gdl_front_end');		
			$translator_view_project = __('View All Projects →','gdl_front_end');		
			$translator_read_more = __('Read More','gdl_front_end');
		}	
		
		global $paged;
		global $sidebar;
		global $port_div_size_num_class;
		global $class_to_num;
		
		if(empty($paged)){
			$paged = (get_query_var('page')) ? get_query_var('page') : 1; 
		}
		
		// get the item class and size from array
		$port_size = find_xml_value($item_xml, 'item-size');
		
		$item_class = $port_div_size_num_class[$port_size]['class'];
		if( $sidebar == "no-sidebar" ){
			$item_size = $port_div_size_num_class[$port_size]['size'];
		}else if ( $sidebar == "left-sidebar" || $sidebar == "right-sidebar" ){
			$item_size = $port_div_size_num_class[$port_size]['size2'];
		}else{
			$item_size = $port_div_size_num_class[$port_size]['size3'];
		}
		
		// get the portfolio meta value
		$header = find_xml_value($item_xml, 'header');
		$num_fetch = find_xml_value($item_xml, 'num-fetch');
		$num_excerpt = find_xml_value($item_xml, 'num-excerpt');
		
		$category = find_xml_value($item_xml, 'category');
		$category_val = ( $category == 'All' )? '': $category;
		
		$filterable = find_xml_value($item_xml, 'filterable');
		$filter_class = '';

		// start fetching database
		global $post, $wp_query;
		
		if( !empty($category_val) ){
			$category_term = get_term_by( 'name', $category_val , 'portfolio-category');
			$category_val = $category_term->slug;
		}
		
		$post_temp = query_posts(array('post_type'=>'portfolio', 'paged'=>$paged, 
			'portfolio-category'=>$category_val, 'posts_per_page'=>$num_fetch));	

		// check available category
		$avail_cat = array();
		while( have_posts() ){
			the_post();
			$post_categories = get_the_terms( get_the_ID(), 'portfolio-category' );
			if(!empty($post_categories)){
				foreach( $post_categories as $category ){ 
					if( !in_array($category->slug, $avail_cat) ){ 
						array_push($avail_cat, $category->slug);
					}
				}
			}
		}		
		rewind_posts();
		
		// portfolio header
		if(!empty($header)){
			echo '<h3 class="portfolio-header-title title-color gdl-title">' . $header . '</h3>';
			
			$view_all_project = find_xml_value($item_xml, 'view-all-project');
			if($view_all_project != 'No'){
				$view_all_project_link = get_permalink( get_page_by_title( $view_all_project ) );
				echo '<div class="portfolio-view-all-project">';
				echo '<a href="' . $view_all_project_link . '">' . $translator_view_project . '</a>';
				echo '</div>';
			}
		}
		
		
		// category list for filter
		if( $filterable == "Yes" ){
			echo '<ul id="portfolio-item-filter">';
			if( empty($category_val) ){ 
				echo '<li><a href="#" class="gdl-button active" data-value="all">All</a></li>';
				$parent_id = 0;
			}else{
				$parent_id = get_term_by('slug', $category_val, 'portfolio-category');
				echo '<li><a href="#" class="gdl-button active" data-value="' . $parent_id->slug  .'">' . $parent_id->name . '</a></li>';
				$parent_id = $parent_id->term_id;
			}			
			
			$category_lists = get_categories( array( 'taxonomy' => 'portfolio-category', 'child_of' => $parent_id ));
			foreach($category_lists as $category_list){
				if( in_array($category_list->slug, $avail_cat) ){ 
					echo '<li><a href="#" class="gdl-button" data-value="' . $category_list->slug . '">' . $category_list->name . '</a></li>';
				}
			}			
			echo "</ul>";
		}
		
		echo '<div class="clear"></div>';

		// get the portfolio size
		$port_wrapper_size = $class_to_num[find_xml_value($item_xml, 'size')];
		$port_current_size = 0;
		$port_size =  $class_to_num[$port_size];
		
		$port_num_have_bottom = sizeof($post_temp) % (int)($port_wrapper_size/$port_size);
		$port_num_have_bottom = ( $port_num_have_bottom == 0 )? (int)($port_wrapper_size/$port_size): $port_num_have_bottom;
		$port_num_have_bottom = sizeof($post_temp) - $port_num_have_bottom;
			
		echo '<div id="portfolio-item-holder" class="portfolio-item-holder">';
		while( have_posts() ){
			
			the_post();
					
			// get the category for filter
			$item_categories = get_the_terms( $post->ID, 'portfolio-category' );
			$category_slug = " ";
			if( !empty($item_categories) ){
				foreach( $item_categories as $item_category ){
					$category_slug = $category_slug . $item_category->slug . ' ';
				}
			}
			
			// start printing data
			echo '<div class="' . $item_class . $category_slug . ' mt0 pt25 portfolio-item">'; 

			$thumbnail_types = get_post_meta( $post->ID, 'post-option-thumbnail-types', true);
			
			if( $thumbnail_types == "Image" ){
				
				$image_type = get_post_meta( $post->ID, 'post-option-featured-image-type', true);
				$image_type = empty($image_type)? "Link to Current Post": $image_type; 
				$thumbnail_id = get_post_thumbnail_id();
				$thumbnail = wp_get_attachment_image_src( $thumbnail_id , $item_size );
				$alt_text = get_post_meta($thumbnail_id , '_wp_attachment_image_alt', true);
				
				
				if( $image_type == "Link to Current Post" ){
					$hover_thumb = "hover-link";
					$pretty_photo = "";
					$permalink = get_permalink();
				}else if( $image_type == "Link to URL"){
					$hover_thumb = "hover-link";
					$pretty_photo = "";
					$permalink = __(get_post_meta( $post->ID, 'post-option-featured-image-url', true ), 'gdl_front_end');
				}else if( $image_type == "Lightbox to Current Thumbnail" ){	
					$hover_thumb = "hover-zoom";
					$pretty_photo = ' data-rel="prettyPhoto" ';
					$permalink = wp_get_attachment_image_src( $thumbnail_id, 'full' );
					$permalink = $permalink[0];
				}else if( $image_type == "Lightbox to Picture" ){
					$hover_thumb = "hover-zoom";
					$pretty_photo = ' data-rel="prettyPhoto" ';
					$permalink = __(get_post_meta( $post->ID, 'post-option-featured-image-url', true ), 'gdl_front_end');	
					$permalink = $permalink;
				}else{
					$hover_thumb = "hover-video";
					$pretty_photo = ' data-rel="prettyPhoto" ';
					$permalink = __(get_post_meta( $post->ID, 'post-option-featured-image-url', true ), 'gdl_front_end');	
					$permalink = $permalink;				
				}
				
				if( !empty($thumbnail[0]) ){
					echo '<div class="portfolio-thumbnail-image">';
					echo '<div class="overflow-hidden">';
					echo '<a href="' . $permalink . '" ' . $pretty_photo . ' title="' . get_the_title() . '">';
					echo '<span class="portfolio-thumbnail-image-hover">';
					echo '<span class="' . $hover_thumb . '"></span>';
					echo '</span>';
					echo '</a>';
					echo '<img src="' . $thumbnail[0] .'" alt="'. $alt_text .'"/>';
					echo '</div>'; //overflow hidden
					echo '</div>'; //portfolio thumbnail image						
				}
				
			
			}else if( $thumbnail_types == "Video" ){
				
				$video_link = get_post_meta( $post->ID, 'post-option-thumbnail-video', true); 
				echo '<div class="portfolio-thumbnail-video">';
				echo get_video($video_link, gdl_get_width($item_size), gdl_get_height($item_size));
				echo '</div>';
			
			}else if ( $thumbnail_types == "Slider" ){

				$slider_xml = get_post_meta( $post->ID, 'post-option-thumbnail-xml', true); 
				$slider_xml_dom = new DOMDocument();
				$slider_xml_dom->loadXML($slider_xml);
				
				echo '<div class="portfolio-thumbnail-slider">';
				echo print_flex_slider($slider_xml_dom->documentElement, $item_size);
				echo '</div>';			
			
			}
			
			echo '<div class="portfolio-thumbnail-context">';
			// portfolio title
			if( find_xml_value($item_xml, "show-title") == "Yes" ){
				echo '<h2 class="portfolio-thumbnail-title port-title-color gdl-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h2>';
			}
			
			// portfolio excerpt
			if( find_xml_value($item_xml, "show-excerpt") == "Yes" ){			
				echo '<div class="portfolio-thumbnail-content">' . mb_substr( get_the_excerpt(), 0, $num_excerpt ) . '</div>';
			}
			
			// read more button
			if( find_xml_value($item_xml, "read-more") == "Yes" ){
				echo '<a href="' . get_permalink() . '" class="portfolio-read-more gdl-button">' . $translator_read_more . '</a>';
			}
			
			// visit website
			if( find_xml_value($item_xml, "visit-website") == "Yes" ){
				$website = get_post_meta( $post->ID, 'post-option-website-url', true); 
				if( !empty( $website ) ){
					echo '<a href="' . $website . '" class="portfolio-visit-website gdl-button">' . $translator_visit_website . '</a>';
				}
			}
			
			echo '</div>';
			
			// print space if not last line
			if($port_current_size < $port_num_have_bottom){
				if( find_xml_value($item_xml, "show-title") == 'Yes' ||
					find_xml_value($item_xml, "show-excerpt") == "Yes" ||
					find_xml_value($item_xml, "read-more") == "Yes" ||
					find_xml_value($item_xml, "visit-website") == "Yes"){
					echo '<div class="portfolio-bottom"></div>';
				}
				$port_current_size++;
			}
			
			echo '</div>';
			
		}
		echo "</div>";
		echo '<div class="clear"></div>';
		if( find_xml_value($item_xml, "pagination") == "Yes" ){	
			pagination();
		}
		
	}

	// Print nested page
	function print_page_item($item_xml){
		
		wp_reset_query();
		
		global $paged;
		global $sidebar;
		global $port_div_size_num_class;	
		global $class_to_num;
		
		if(empty($paged)){
			$paged = (get_query_var('page')) ? get_query_var('page') : 1; 
		}

		// Translator words
		global $gdl_admin_translator;	
		
		if( $gdl_admin_translator == 'enable' ){
			$translator_read_more = get_option(THEME_SHORT_NAME.'_translator_read_more', 'Read More');
		}else{
			$translator_read_more = __('Read More','gdl_front_end');
		}
		
		// get the item class and size from array
		$port_size = find_xml_value($item_xml, 'item-size');
		
		// get the item class and size from array
		$item_class = $port_div_size_num_class[$port_size]['class'];
		if( $sidebar == "no-sidebar" ){
			$item_size = $port_div_size_num_class[$port_size]['size'];
		}else if ( $sidebar == "left-sidebar" || $sidebar == "right-sidebar" ){
			$item_size = $port_div_size_num_class[$port_size]['size2'];
		}else{
			$item_size = $port_div_size_num_class[$port_size]['size3'];
		}

		// get the page meta value
		$header = find_xml_value($item_xml, 'header');
		$num_fetch = find_xml_value($item_xml, 'num-fetch');
		$num_excerpt = find_xml_value($item_xml, 'num-excerpt');	

		// page header
		if(!empty($header)){
			echo '<h3 class="portfolio-header-title title-color gdl-title">' . $header . '</h3>';
		}
		
		global $post;
		
		$post_temp = query_posts(array('post_type'=>'page', 'paged'=>$paged, 'post_parent'=>$post->ID, 'posts_per_page'=>$num_fetch, 'orderby'=>'menu_order', 'order'=>'asc' ));

		// get the portfolio size
		$port_wrapper_size = $class_to_num[find_xml_value($item_xml, 'size')];
		$port_current_size = 0;
		$port_size =  $class_to_num[$port_size];
		
		$port_num_have_bottom = sizeof($post_temp) % (int)($port_wrapper_size/$port_size);
		$port_num_have_bottom = ( $port_num_have_bottom == 0 )? (int)($port_wrapper_size/$port_size): $port_num_have_bottom;
		$port_num_have_bottom = sizeof($post_temp) - $port_num_have_bottom;
		
		echo '<div id="portfolio-item-holder" class="portfolio-item-holder">';
		while( have_posts() ){
			
			the_post();
			
			// start printing data
			echo '<div class="' . $item_class . ' mt0 pt25 portfolio-item">'; 

			$image_type = get_post_meta( $post->ID, 'post-option-featured-image-type', true);
			$image_type = empty($image_type)? "Link to Current Post": $image_type; 
			$thumbnail_id = get_post_thumbnail_id();
			$thumbnail = wp_get_attachment_image_src( $thumbnail_id , $item_size );
			$alt_text = get_post_meta($thumbnail_id , '_wp_attachment_image_alt', true);
			
			$hover_thumb = "hover-link";
			$pretty_photo = "";
			$permalink = get_permalink();
			

			if( !empty($thumbnail[0]) ){
				echo '<div class="portfolio-thumbnail-image">';
				echo '<div class="overflow-hidden">';
				echo '<a href="' . $permalink . '" ' . $pretty_photo . ' title="' . get_the_title() . '">';
				echo '<span class="portfolio-thumbnail-image-hover">';
				echo '<span class="' . $hover_thumb . '"></span>';
				echo '</span>';
				echo '</a>';
				echo '<img src="' . $thumbnail[0] .'" alt="'. $alt_text .'"/>';
				echo '</div>'; //overflow hidden
				echo '</div>'; //portfolio thumbnail image						
			}
			
			
			echo '<div class="portfolio-thumbnail-context">';
			// page title
			if( find_xml_value($item_xml, "show-title") == "Yes" ){
				echo '<h2 class="portfolio-thumbnail-title port-title-color gdl-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h2>';
			}
			
			// page excerpt
			if( find_xml_value($item_xml, "show-excerpt") == "Yes" ){			
				echo '<div class="portfolio-thumbnail-content">' . mb_substr( get_the_excerpt(), 0, $num_excerpt ) . '</div>';
			}
			
			// read more button
			if( find_xml_value($item_xml, "read-more") == "Yes" ){
				echo '<a href="' . get_permalink() . '" class="portfolio-read-more gdl-button">' . $translator_read_more . '</a>';
			}
			
			echo '</div>';

			// print space if not last line
			if($port_current_size < $port_num_have_bottom){
				echo '<div class="portfolio-bottom"></div>';
				$port_current_size++;
			}
			
			echo '</div>';

		}

		echo "</div>";
		echo '<div class="clear"></div>';
		if( find_xml_value($item_xml, "pagination") == "Yes" ){	
			pagination();
		}		
		
	}
	
	// Print postslider item
	function print_post_slider_item($item_xml){
		
		$num_fetch = find_xml_value($item_xml, 'num-fetch');
		$num_excerpt = find_xml_value($item_xml, 'num-excerpt');
		$show_caption = find_xml_value($item_xml, 'show-caption');
		
		$category = find_xml_value($item_xml, 'category', false);
		$category = ($category == 'All')? '': $category;
		if( !empty($category) ){
			$category_term = get_term_by( 'name', $category , 'category');
			$category = $category_term->slug;
		}
		
		$postslider_xml = "<single-item><Post-Slider>";
		$postslider_xml = $postslider_xml . create_xml_tag('size', find_xml_value($item_xml, 'size'));
		$postslider_xml = $postslider_xml . create_xml_tag('width', find_xml_value($item_xml, 'width'));
		$postslider_xml = $postslider_xml . create_xml_tag('height', find_xml_value($item_xml, 'height'));
		$postslider_xml = $postslider_xml . create_xml_tag('slider-type', find_xml_value($item_xml, 'slider-type'));
		$postslider_xml = $postslider_xml . "<slider-item>";

		query_posts(array('post_type'=>'post', 'category_name'=>$category, 'posts_per_page'=>$num_fetch ));	
		
		while( have_posts() ){
			the_post();
			
			$postslider_xml = $postslider_xml . "<slider>";
			$postslider_xml = $postslider_xml . create_xml_tag('image', get_post_thumbnail_id(get_the_ID()) );
			$postslider_xml = $postslider_xml . create_xml_tag('linktype', 'Link to URL' );
			$postslider_xml = $postslider_xml . create_xml_tag('link', htmlspecialchars(get_permalink()) );
			if( $show_caption == "Yes" ){
				$postslider_xml = $postslider_xml . create_xml_tag('title', htmlspecialchars(get_the_title()) );
				$postslider_xml = $postslider_xml . create_xml_tag('caption', htmlspecialchars(substr(get_the_excerpt(), 0, $num_excerpt)) );
			}
			$postslider_xml = $postslider_xml . "</slider>";
			
		}		
		
		$postslider_xml = $postslider_xml . "</slider-item>";
		$postslider_xml = $postslider_xml . "</Post-Slider></single-item>";
		
		$slider_xml_val = new DOMDocument();
		$slider_xml_val->loadXML($postslider_xml);
		foreach( $slider_xml_val->documentElement->childNodes as $slider_item_xml){
			print_slider_item($slider_item_xml);
		}
		
		wp_reset_query();
	}	
?>