<?php get_header(); ?>
	<?php 
		
		$sidebar = get_post_meta($post->ID,'page-option-sidebar-template',true);
		$sidebar_class = '';
		if( $sidebar == "left-sidebar" || $sidebar == "right-sidebar"){
			$sidebar_class = "sidebar-included " . $sidebar;
		}else if( $sidebar == "both-sidebar" ){
			$sidebar_class = "both-sidebar-included";
		}

	?>
	<div class="content-wrapper <?php echo $sidebar_class; ?>">
			
		<div class="page-wrapper">
			<?php
				// Top Slider Part
				global $gdl_top_slider_type, $gdl_top_slider_xml;
				if( $gdl_top_slider_type == 'Layer Slider' ){
					$layer_slider_id = get_post_meta( $post->ID, 'page-option-layer-slider-id', true);
					echo print_item_size('element1-1',  "mt0 top-layer-slider-wrapper");
					
					echo '<div class="slider-wrapper fullwidth" >';
					echo do_shortcode('[layerslider id="' . $layer_slider_id . '"]');
					echo "<div class='slider-top-shadow slider-gimmick'></div>";
					echo "<div class='slider-bottom-shadow slider-gimmick'></div>";
					echo "<div class='slider-bottom-gimmick slider-gimmick'></div>";
					echo '</div>';
					
					echo '</div>'; // slider-wrapper
					
				}else if ($gdl_top_slider_type != "No Slider" && $gdl_top_slider_type != ''){
					echo print_item_size('element1-1',  "mt0");
						
						$slider_xml = "<Slider>" . create_xml_tag('size', 'full-width');
						$slider_xml = $slider_xml . create_xml_tag('height', get_post_meta( $post->ID, 'page-option-top-slider-height', true) );
						$slider_xml = $slider_xml . create_xml_tag('width', 980);
						$slider_xml = $slider_xml . create_xml_tag('slider-type', $gdl_top_slider_type);
						$slider_xml = $slider_xml . $gdl_top_slider_xml;
						$slider_xml = $slider_xml . "</Slider>";
						$slider_xml_dom = new DOMDocument();
						$slider_xml_dom->loadXML($slider_xml);
						print_slider_item($slider_xml_dom->documentElement);

					echo "</div>";
				}
				
				$left_sidebar = get_post_meta( $post->ID , "page-option-choose-left-sidebar", true);
				$right_sidebar = get_post_meta( $post->ID , "page-option-choose-right-sidebar", true);		
			
				echo "<div class='gdl-page-float-left'>";
				
				echo "<div class='gdl-page-item'>";
				
				// Page title and content
				$gdl_show_title = get_post_meta($post->ID, 'page-option-show-title', true);
				$gdl_show_content = get_post_meta($post->ID, 'page-option-show-content', true);
				if ( $gdl_show_title != "No" ){
					while (have_posts()){ the_post(); 
						echo '<div class="sixteen columns mt30">';
						echo '<h1 class="gdl-page-title gdl-divider gdl-title title-color">';
						the_title();
						echo '</h1>';
						$content = get_the_content();
						$content = apply_filters('the_content', $content);
						if( $gdl_show_content != 'No' && !empty( $content ) ){
							echo '<div class="gdl-page-content">';
							echo $content;
							wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'gdl_front_end' ) . '</span>', 'after' => '</div>' ) );
							echo '</div>';
						}
						echo '</div>';
					}
				}else{
					while (have_posts()){ the_post(); 
						$content = get_the_content();
						$content = apply_filters('the_content', $content);
						if( $gdl_show_content != 'No' && !empty( $content ) ){
							echo '<div class="sixteen columns mt0">';
							echo '<div class="gdl-page-content">';
							echo $content;
							wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'gdl_front_end' ) . '</span>', 'after' => '</div>' ) );
							echo '</div>';
							echo '</div>';
						}			
					}
				}
		
				global $gdl_item_row_size;
				$gdl_item_row_size = 0;
				// Page Item Part
				if(!empty($gdl_page_xml)  && !post_password_required()){
					$page_xml_val = new DOMDocument();
					$page_xml_val->loadXML($gdl_page_xml);
					foreach( $page_xml_val->documentElement->childNodes as $item_xml){
						switch($item_xml->nodeName){
							case 'Accordion' :
								print_item_size(find_xml_value($item_xml, 'size'));
								print_accordion_item($item_xml);
								break;
							case 'Blog' :
								print_item_size(find_xml_value($item_xml, 'size'), 'wrapper mt0');
								print_blog_item($item_xml);
								break;
							case 'Contact-Form' :
								print_item_size(find_xml_value($item_xml, 'size'), 'mt30');
								print_contact_form($item_xml);
								break;
							case 'Column':
								print_item_size(find_xml_value($item_xml, 'size'));
								print_column_item($item_xml);
								break;
							case 'Column-Service' :
								print_item_size(find_xml_value($item_xml, 'size'));
								print_column_service($item_xml);
								break;
							case 'Content' :
								print_item_size(find_xml_value($item_xml, 'size'));
								print_content_item($item_xml);
								break;
							case 'Divider' :
								print_item_size(find_xml_value($item_xml, 'size'));
								print_divider($item_xml);
								break;
							case 'Gallery' :
								print_item_size(find_xml_value($item_xml, 'size'), 'wrapper');
								print_gallery_item($item_xml);
								break;								
							case 'Message-Box' :
								print_item_size(find_xml_value($item_xml, 'size'));
								print_message_box($item_xml);
								break;
							case 'Page':
								print_item_size(find_xml_value($item_xml, 'size'), 'wrapper gdl-portfolio-item mt0');
								print_page_item($item_xml);
								break;
							case 'Post-Slider':
								print_item_size(find_xml_value($item_xml, 'size'), 'gdl-post-slider-item');
								print_post_slider_item($item_xml);
								break;								
							case 'Price-Item':
								print_item_size(find_xml_value($item_xml, 'size'), 'gdl-price-item');
								print_price_item($item_xml);
								break;
							case 'Portfolio' :
								print_item_size(find_xml_value($item_xml, 'size'), 'wrapper gdl-portfolio-item mt0');
								print_portfolio($item_xml);
								break;
							case 'Personnel' :
								print_item_size(find_xml_value($item_xml, 'size'), 'wrapper mt0');
								print_personnel($item_xml);
								break;								
							case 'Slider' : 
								print_item_size(find_xml_value($item_xml, 'size'), 'mt20');
								print_slider_item($item_xml);
								break;
							case 'Stunning-Text' :
								print_item_size(find_xml_value($item_xml, 'size'), 'wrapper');
								print_stunning_text($item_xml);
								break;
							case 'Tab' :
								print_item_size(find_xml_value($item_xml, 'size'));
								print_tab_item($item_xml);
								break;
							case 'Testimonial' :
								print_item_size(find_xml_value($item_xml, 'size'), 'wrapper');
								print_testimonial($item_xml);
								break;
							case 'Toggle-Box' :
								print_item_size(find_xml_value($item_xml, 'size'));
								print_toggle_box_item($item_xml);
								break;
							default: 
								print_item_size(find_xml_value($item_xml, 'size'));
								break;
						}
						echo "</div>";
					}
				}
				echo "</div>"; // end of gdl-page-item
				
				get_sidebar('left');		
				
				echo "</div>"; // gdl-page-float-left	
				
				get_sidebar('right');
				
			?>
			
			<br class="clear">
		</div>
	</div> <!-- content-wrapper -->
	
<?php get_footer(); ?>