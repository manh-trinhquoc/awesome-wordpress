<?php
	/*	
	*	Goodlayers Custom Style File
	*	---------------------------------------------------------------------
	*	This file fetch all style options in admin panel to generate the 
	*	style-custom.css file
	*	---------------------------------------------------------------------
	*/
	
	// This function is called when user save the option ( admin panel )
	function gdl_generate_style_custom(){
		global $gdl_fh, $gdl_custom_stylesheet_name;
		
		$return_data = array('success'=>'-1', 'alert'=>'Cannot write ' . $gdl_custom_stylesheet_name . ' file, you may try setting the style-custom.css file permission to 755 or 777 to solved this. ( If file does not exists, you have to create it yourself )');
		
		// initial the value of the style
		$file_path = SERVER_PATH . "/" . $gdl_custom_stylesheet_name;
		$gdl_fh = @fopen($file_path, 'w');
		
		if( !$gdl_fh ){ die( json_encode($return_data) ); }
		
		gdl_get_style_custom_content();
		
		// close data
		fclose($gdl_fh);	
	}
	
	// This function write the files to the admin panel
	function gdl_write_data( $string ){
		global $gdl_fh;
		fwrite( $gdl_fh, $string . "\r\n" );
	}
	
	// help print the css easier
	function gdl_print_style( $selector, $content ){
		gdl_write_data( $selector . '{ ' . $content  . '} ');
	}
	
	// help to print the attribute easier
	function gdl_style_att( $attribute, $value ){
		return $attribute . ': ' . $value . '; ';
	}
	
	function gdl_get_style_custom_content(){
		
		$temp_val = '';
		$temp_sel = '';
		$temp_att = '';
		
		// Background Style
		$background_style = get_option(THEME_SHORT_NAME.'_background_style', 'Pattern');
		if($background_style == 'Pattern'){
			$background_pattern = get_option(THEME_SHORT_NAME.'_background_pattern', '1');
			
			$temp_att = gdl_style_att( 'background-image', 'url(' . GOODLAYERS_PATH . '/images/pattern/pattern-' . $background_pattern . '.png)' );
			gdl_print_style( 'html', $temp_att );
		}
		$temp_att = gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME . "_body_background", '#dddddd') );
		gdl_print_style( 'html', $temp_att );	
		
		// Logo Margin
		$temp_att = gdl_style_att( 'padding-top', get_option(THEME_SHORT_NAME . "_logo_top_margin", '35') . 'px' );
		$temp_att = $temp_att . gdl_style_att( 'padding-left', get_option(THEME_SHORT_NAME . "_logo_left_margin", '0') . 'px' );
		$temp_att = $temp_att . gdl_style_att( 'padding-bottom', get_option(THEME_SHORT_NAME . "_logo_bottom_margin", '26') . 'px' );
		gdl_print_style( '.logo-wrapper', $temp_att );
		
		// Social Margin
		$temp_att = gdl_style_att( 'margin-top', get_option(THEME_SHORT_NAME . "_social_wrapper_margin", '33') . 'px' );
		gdl_print_style( '.social-wrapper', $temp_att );		
		
		// Header Font
		$temp_att = gdl_style_att( 'font-size', get_option(THEME_SHORT_NAME . "_h1_size", '30') . 'px' );
		gdl_print_style( 'h1', $temp_att );	
		$temp_att = gdl_style_att( 'font-size', get_option(THEME_SHORT_NAME . "_h2_size", '25') . 'px' );
		gdl_print_style( 'h2', $temp_att );	
		$temp_att = gdl_style_att( 'font-size', get_option(THEME_SHORT_NAME . "_h3_size", '20') . 'px' );
		gdl_print_style( 'h3', $temp_att );	
		$temp_att = gdl_style_att( 'font-size', get_option(THEME_SHORT_NAME . "_h4_size", '18') . 'px' );
		gdl_print_style( 'h4', $temp_att );	
		$temp_att = gdl_style_att( 'font-size', get_option(THEME_SHORT_NAME . "_h5_size", '16') . 'px' );
		gdl_print_style( 'h5', $temp_att );	
		$temp_att = gdl_style_att( 'font-size', get_option(THEME_SHORT_NAME . "_h6_size", '15') . 'px' );
		gdl_print_style( 'h6', $temp_att );			
		$temp_att = gdl_style_att( 'font-size', get_option(THEME_SHORT_NAME . "_content_size", '12') . 'px' );
		gdl_print_style( 'body', $temp_att );	
		
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME . "_title_color", '#494949') );
		gdl_print_style( 'h1, h2, h3, h4, h5, h6, .title-color', $temp_att );	
		
		// Font Family
		$temp_att = gdl_style_att( 'font-family', substr(get_option(THEME_SHORT_NAME . "_content_font"), 2) );
		gdl_print_style( 'body', $temp_att );	
		$temp_att = gdl_style_att( 'font-family', substr(get_option(THEME_SHORT_NAME . "_header_font"), 2) );
		gdl_print_style( 'h1, h2, h3, h4, h5, h6, .gdl-title', $temp_att );				
		$temp_att = gdl_style_att( 'font-family', substr(get_option(THEME_SHORT_NAME . "_stunning_text_font"), 2) );
		gdl_print_style( 'h1.stunning-text-title', $temp_att );	

		// Divider
		$temp_val = get_option(THEME_SHORT_NAME . "_divider_line", '#ececec');
		$temp_att = gdl_style_att( 'border-bottom', '1px solid ' . $temp_val );
		gdl_print_style( 'div.divider', $temp_att );	
		
		$temp_sel = ".gdl-divider, ";
		$temp_sel = $temp_sel . ".custom-sidebar.gdl-divider div, ";
		$temp_sel = $temp_sel . ".custom-sidebar.gdl-divider .custom-sidebar-title, ";
		$temp_sel = $temp_sel . ".custom-sidebar.gdl-divider ul li";
		$temp_att = gdl_style_att( 'border-color', $temp_val . ' !important' );
		gdl_print_style( $temp_sel, $temp_att );
		
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME . "_back_to_top_text_color", '#7c7c7c') . ' !important' );
		gdl_print_style( '.scroll-top', $temp_att );	
		
		// Header Area
		$temp_att = gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME . "_header_background_color", '#ffffff'));
		gdl_print_style( '.header-outer-wrapper', $temp_att );	
		
		// Stunning Text 
		$temp_att = gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME . "_stunning_text_background_color", '#ffffff') . ' !important' );
		gdl_print_style( '.stunning-text-wrapper', $temp_att );	
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME . "_stunning_text_title_color", '#333333') );
		gdl_print_style( 'h1.stunning-text-title', $temp_att );	
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME . "_stunning_text_caption_color", '#666666') );
		gdl_print_style( '.stunning-text-caption', $temp_att );			
		
		$temp_val = get_option(THEME_SHORT_NAME.'_stunning_text_button_background', '#ef7f2c');
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_stunning_text_button_color', '#ffffff') . ' !important' );
		$temp_att = $temp_att . gdl_style_att( 'background-color', $temp_val . ' !important' );
		$temp_att = $temp_att . gdl_style_att( 'border', '1px solid ' . $temp_val . ' !important' );
		gdl_print_style( '.stunning-text-button', $temp_att );				
		
		// Font Color
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME . "_content_color", '#666666') . ' !important' );
		gdl_print_style( 'body', $temp_att );			

		$temp_val = get_option(THEME_SHORT_NAME . "_link_color", '#ef7f2c');
		
		$temp_att = gdl_style_att( 'color', $temp_val );
		gdl_print_style( 'a', $temp_att );	
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME . "_link_hover_color", '#ef7f2c') );
		gdl_print_style( 'a:hover', $temp_att );		
	
		$temp_att = gdl_style_att( 'color', $temp_val . ' !important' );
		gdl_print_style( '.gdl-link-title', $temp_att );		
		
		// Slider
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME . "_slider_title_color", '#ef7f2c') . ' !important' );
		gdl_print_style( '.gdl-slider-title', $temp_att );		
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME . "_slider_caption_color", '#ffffff') . ' !important' );
		gdl_print_style( '.gdl-slider-caption, .nivo-caption', $temp_att );		
		$temp_att = gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME . "_full_slider_bottom_line", '#ebebeb') );
		gdl_print_style( 'div.slider-bottom-gimmick', $temp_att );		
		$temp_att = gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME . "_layer_slider_nav_background", '#ef7f2c') );
		gdl_print_style( '.ls-modernize .ls-nav-prev, .ls-modernize .ls-nav-next', $temp_att );			
		
		// Column Service
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME . "_column_service_title_color", '#ef7f2c') . ' !important' );
		gdl_print_style( 'h2.column-service-title', $temp_att );			
	
		// Post - Port Color
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME . "_port_title_color", '#ef7f2c') . ' !important' );
		gdl_print_style( '.port-title-color, .port-title-color a', $temp_att );	
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME . "_port_title_hover_color", '#ef7f2c') . ' !important' );
		gdl_print_style( '.port-title-color a:hover', $temp_att );			

		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME . "_post_title_color", '#646464') . ' !important' );
		gdl_print_style( '.post-title-color', $temp_att );	
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME . "_post_title_hover_color", '#646464') . ' !important' );
		gdl_print_style( '.post-title-color a:hover', $temp_att );	
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME . "_post_widget_title_color", '#ef7f2c') . ' !important' );
		gdl_print_style( '.post-widget-title-color', $temp_att );	
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME . "_post_info_color", '#797979') . ' !important' );
		gdl_print_style( '.post-info-color, div.custom-sidebar #twitter_update_list', $temp_att );	
		$temp_att = gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME . "_pagination_normal_state", '#f5f5f5') );
		gdl_print_style( 'div.pagination a', $temp_att );
		$temp_att = gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME . "_post_about_author_color", '#f9f9f9') . ' !important' );
		gdl_print_style( '.about-author-wrapper', $temp_att );			
		
		// Frame Color
		$temp_sel = "div.gallery-thumbnail-image, ";
		$temp_sel = $temp_sel . "div.portfolio-thumbnail-image, div.portfolio-thumbnail-video, div.portfolio-thumbnail-slider, ";
		$temp_sel = $temp_sel . "div.blog-thumbnail-image, div.blog-thumbnail-video, div.blog-thumbnail-slider, ";
		$temp_sel = $temp_sel . ".gdl-image-frame";
		$temp_att = gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME . "_post_port_frame_color", '#f0f0f0') . ' !important' );
		$temp_att = $temp_att . gdl_style_att( 'border', '1px solid ' . get_option(THEME_SHORT_NAME . "_post_port_frame_border", '#ffffff') . ' !important' );
		gdl_print_style( $temp_sel, $temp_att );	
		
		// Testimonial
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME . "_testimonial_text", '#848484') . ' !important' );
		gdl_print_style( '.testimonial-content', $temp_att );			
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME . "_testimonial_author", '#494949') . ' !important' );
		gdl_print_style( '.testimonial-author-name', $temp_att );		
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME . "_testimonial_position", '#8d8d8d') . ' !important' );
		gdl_print_style( '.testimonial-author-position', $temp_att );		
		
		// Table
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME . "_table_text_title", '#666666') );
		$temp_att = $temp_att . gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME . "_table_title_background", '#f7f7f7') );
		gdl_print_style( 'table th', $temp_att );		
		$temp_att = gdl_style_att( 'border-color', get_option(THEME_SHORT_NAME . "_table_border", '#e5e5e5') );
		gdl_print_style( 'table, table tr, table tr td, table tr th', $temp_att );				

		// Top Navigation
		$temp_val = get_option(THEME_SHORT_NAME.'_top_navigation_text', '#e7e7e7');
		$temp_att = gdl_style_att( 'color', $temp_val . ' !important' );
		$temp_att = $temp_att . gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME . "_top_navigation_background", '#494949') . ' !important' );
		gdl_print_style( '.top-navigation-wrapper, .top-navigation-left li a', $temp_att );	
		
		$temp_val = '#' . hexDarker(substr($temp_val, 1));
		$temp_att = gdl_style_att( 'border-right', '1px solid ' . $temp_val . ' !important' );
		gdl_print_style( '.top-navigation-left li a', $temp_att );	
		
		$temp_att = gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME . "_top_navigation_bottom_bar", '#e77927') . ' !important' );
		gdl_print_style( '.top-navigation-wrapper-gimmick', $temp_att );		
		
		// Navigation
		if(get_option(THEME_SHORT_NAME.'_main_navigation_gradient', 'enable') == 'enable'){ 
			$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/gradient-top-gray-40px.png) repeat-x' );
			gdl_print_style( 'div.navigation-wrapper', $temp_att );		
		} 
		
		$temp_val = get_option(THEME_SHORT_NAME.'_main_navigation_border_top_bottom', '#ececec');
		$temp_att = gdl_style_att( 'border-top', '1px solid ' . $temp_val . ' !important' );
		$temp_att = $temp_att . gdl_style_att( 'border-bottom', '1px solid ' . $temp_val . ' !important' );
		$temp_val = get_option(THEME_SHORT_NAME.'_main_navigation_bottom_shadow', '#f5f5f5');
		$temp_att = $temp_att . gdl_style_att( '-moz-box-shadow', '0px 1px 5px -1px ' . $temp_val );
		$temp_att = $temp_att . gdl_style_att( '-webkit-box-shadow', '0px 1px 5px -1px ' . $temp_val );
		$temp_att = $temp_att . gdl_style_att( 'box-shadow', '0px 1px 5px -1px ' . $temp_val );
		gdl_print_style( '.navigation-wrapper', $temp_att );	
		
		$temp_att = gdl_style_att( 'border-color', get_option(THEME_SHORT_NAME.'_sub_navigation_border', '#ececec') . ' !important' );
		gdl_print_style( '.navigation-wrapper .sf-menu ul, .navigation-wrapper .sf-menu ul li', $temp_att );
		$temp_att = gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME.'_main_navigation_background', '#fdfdfd') . ' !important' );
		gdl_print_style( '.navigation-wrapper', $temp_att );
		$temp_att = gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME.'_sub_navigation_background', '#fdfdfd') . ' !important' );
		gdl_print_style( '.sf-menu li li', $temp_att );			

		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_main_navigation_text', '#7a7a7a') . ' !important' );
		$temp_att = $temp_att . gdl_style_att( 'border-right', '1px solid ' . get_option(THEME_SHORT_NAME.'_main_navigation_border_right', '#dbdbdb') . ' !important' );
		$temp_att = $temp_att . gdl_style_att( 'border-left', '1px solid ' . get_option(THEME_SHORT_NAME.'_main_navigation_border_left', '#ffffff') . ' !important' );
		gdl_print_style( '.navigation-wrapper .sf-menu li a', $temp_att );		

		$temp_sel = ".navigation-wrapper .sf-menu ul a, .navigation-wrapper .sf-menu ul .current-menu-ancestor ul a, .navigation-wrapper .sf-menu ul .current-menu-item ul a, ";
		$temp_sel = $temp_sel . ".navigation-wrapper .sf-menu .current-menu-ancestor ul a, .navigation-wrapper .sf-menu .current-menu-item ul a";
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_sub_navigation_text', '#7a7a7a') . ' !important' );
		gdl_print_style( $temp_sel, $temp_att );	

		$temp_sel = ".navigation-wrapper .sf-menu ul a:hover, .navigation-wrapper .sf-menu ul .current-menu-item ul a:hover, ";
		$temp_sel = $temp_sel . ".navigation-wrapper .sf-menu .current-menu-item ul a:hover";
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_sub_navigation_text_hover', '#3d3d3d') . ' !important' );
		gdl_print_style( $temp_sel, $temp_att );			

		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_main_navigation_text_hover', '#3d3d3d') . ' !important' );
		gdl_print_style( '.navigation-wrapper .sf-menu a:hover', $temp_att );				
	
		$temp_sel = ".navigation-wrapper .sf-menu .current-menu-ancestor a, .navigation-wrapper .sf-menu .current-menu-item a";
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_main_navigation_text_current', '#3d3d3d') . ' !important' );
		gdl_print_style( $temp_sel, $temp_att );	

		$temp_sel = ".navigation-wrapper .sf-menu ul .current-menu-ancestor a, ";
		$temp_sel = $temp_sel . ".navigation-wrapper .sf-menu ul .current-menu-ancestor ul .current-menu-item a, ";
		$temp_sel = $temp_sel . ".navigation-wrapper .sf-menu ul .current-menu-item a";
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_sub_navigation_text_current', '#3d3d3d') . ' !important' );
		gdl_print_style( $temp_sel, $temp_att );		
		
		// Search Box
		$temp_att = gdl_style_att( 'border-left', '1px solid ' . get_option(THEME_SHORT_NAME.'_main_navigation_border_right', '#dbdbdb') );
		gdl_print_style( '.search-wrapper', $temp_att );	
		$temp_att = gdl_style_att( 'border-left', '1px solid ' . get_option(THEME_SHORT_NAME.'_main_navigation_border_left', '#ffffff') );
		gdl_print_style( '.search-wrapper form', $temp_att );		
		
		$temp_val = get_option(THEME_SHORT_NAME.'_search_box_shadow', '#ddd');
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_search_box_text', '#333333') . ' !important' );
		$temp_att = $temp_att . gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME.'_search_box_background', '#efefef') . ' !important' );
		$temp_att = $temp_att . gdl_style_att( '-webkit-box-shadow', 'inset 0px 0px 6px ' . $temp_val . ' !important' );
		$temp_att = $temp_att . gdl_style_att( '-moz-box-shadow', 'inset 0px 0px 6px ' . $temp_val . ' !important' );
		$temp_att = $temp_att . gdl_style_att( 'box-shadow', 'inset 0px 0px 6px ' . $temp_val . ' !important' );
		gdl_print_style( '.search-wrapper #search-text input[type="text"]', $temp_att );		

		// Price Item
		$temp_att = gdl_style_att( 'border-color', get_option(THEME_SHORT_NAME.'_price_item_border', '#ececec') . ' !important' );
		gdl_print_style( 'div.gdl-price-item .gdl-divider', $temp_att );		
		
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_price_item_price_title_color', '#3a3a3a') . ' !important' );
		$temp_att = $temp_att . gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME.'_price_item_price_title_background', '#e9e9e9') . ' !important' );
		gdl_print_style( 'div.gdl-price-item .price-title', $temp_att );	
		
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_price_item_best_price_title_color', '#ffffff') . ' !important' );
		$temp_att = $temp_att . gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME.'_price_item_best_price_title_background', '#5f5f5f') . ' !important' );
		gdl_print_style( 'div.gdl-price-item .price-item.active .price-title', $temp_att );			
		
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_price_item_price_color', '#3a3a3a') . ' !important' );
		gdl_print_style( 'div.gdl-price-item .price-tag', $temp_att );
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_price_item_best_price_color', '#ef7f2c') . ' !important' );
		gdl_print_style( 'div.gdl-price-item .price-item.active .price-tag', $temp_att );
		$temp_att = gdl_style_att( 'border-top', '1px solid ' . get_option(THEME_SHORT_NAME.'_price_item_best_price_color', '#ef7f2c') . ' !important' );
		gdl_print_style( 'div.gdl-price-item .price-item.active', $temp_att );	
		
		// Tabs Color
		$temp_val = get_option(THEME_SHORT_NAME.'_tab_border_color', '#dddddd');
		
		$temp_att = gdl_style_att( 'border-color', $temp_val . ' !important' );
		gdl_print_style( 'ul.gdl-tabs', $temp_att );

		$temp_att = gdl_style_att( 'border-color', $temp_val . ' !important' );
		$temp_att = $temp_att . gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME.'_tab_background_color', '#f5f5f5') . ' !important' );
		$temp_att = $temp_att . gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_tab_text_color', '#666666') . ' !important' );
		gdl_print_style( 'ul.gdl-tabs li a', $temp_att );		

		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_tab_active_text_color', '#111111') . ' !important' );
		$temp_att = $temp_att . gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME.'_tab_active_background_color', '#fff') . ' !important' );
		gdl_print_style( 'ul.gdl-tabs li a.active', $temp_att );	
		
		// Personnel
		$temp_att = gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME.'_personnal_background', '#f7f7f7'));
		gdl_print_style( 'div.personnel-item', $temp_att );	
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_personnal_position_text', '#9d9d9d'));
		gdl_print_style( 'div.personnel-item .personnel-position', $temp_att );			
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_personnal_title', '#353535'));
		gdl_print_style( 'div.personnel-item .personnel-title', $temp_att );			
		$temp_att = gdl_style_att( 'border-color', get_option(THEME_SHORT_NAME.'_personnal_thumbnail_border', '#ef7f2c'));
		gdl_print_style( 'div.personnel-item .personnel-thumbnail', $temp_att );	
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_personnal_content', '#353535'));
		gdl_print_style( 'div.personnel-item .personnel-content', $temp_att );			
		
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_personnal_widget_info', '#4a4a4a'));
		gdl_print_style( 'div.personnal-widget-item .personnal-widget-info', $temp_att );			
		
		// Sidebar
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_sidebar_title_color', '#494949') . ' !important' );
		gdl_print_style( '.sidebar-title-color', $temp_att );			
		
		// Footer
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_footer_link_color', '#ef7f2c') . ' !important' );
		gdl_print_style( '.footer-wrapper a', $temp_att );	
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_footer_link_hover_color', '#ef7f2c') . ' !important' );
		gdl_print_style( '.footer-wrapper a:hover', $temp_att );							
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_footer_title_color', '#ececec') . ' !important' );
		gdl_print_style( '.footer-widget-wrapper .custom-sidebar-title', $temp_att );
		
		$temp_att = gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME.'_footer_background', '#313131') . ' !important' );
		gdl_print_style( '.footer-container-wrapper', $temp_att );	
		$temp_att = gdl_style_att( 'border-top', '3px solid ' . get_option(THEME_SHORT_NAME.'_footer_top_bar', '#cfcfcf') );
		gdl_print_style( 'div.footer-container-wrapper', $temp_att );			
			
		$temp_sel = 'div.footer-wrapper div.contact-form-wrapper input[type="text"], ';	
		$temp_sel = $temp_sel . 'div.footer-wrapper div.contact-form-wrapper input[type="password"], '; 
		$temp_sel = $temp_sel . 'div.footer-wrapper div.contact-form-wrapper textarea, '; 
		$temp_sel = $temp_sel . 'div.footer-wrapper div.custom-sidebar #search-text input[type="text"], '; 
		$temp_sel = $temp_sel . 'div.footer-wrapper div.custom-sidebar .contact-widget-whole input, '; 
		$temp_sel = $temp_sel . 'div.footer-wrapper div.custom-sidebar .contact-widget-whole textarea'; 
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_footer_input_text', '#888888') . ' !important' );
		$temp_att = $temp_att . gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME.'_footer_input_background', '#383838') . ' !important' );
		$temp_att = $temp_att . gdl_style_att( 'border', '1px solid ' . get_option(THEME_SHORT_NAME.'_footer_input_border', '#434343') . ' !important' );
		gdl_print_style( $temp_sel, $temp_att );
		
		$temp_att = gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME.'_footer_button_color', '#222222') . ' !important' );
		$temp_att = $temp_att . gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_footer_button_text', '#7a7a7a') . ' !important' );
		gdl_print_style( 'div.footer-wrapper a.button, div.footer-wrapper button, div.footer-wrapper button:hover', $temp_att );
		$temp_att = gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME.'_footer_frame_background', '#292929') );
		$temp_att = $temp_att . gdl_style_att( 'border-color', get_option(THEME_SHORT_NAME.'_footer_frame_border', '#3b3b3b') . ' !important' );
		gdl_print_style( 'div.footer-wrapper div.custom-sidebar .recent-post-widget-thumbnail', $temp_att );
		
		$temp_sel = '.footer-wrapper .gdl-divider, ';
		$temp_sel = $temp_sel . '.footer-wrapper .custom-sidebar.gdl-divider div, ';
		$temp_sel = $temp_sel . '.footer-wrapper .custom-sidebar.gdl-divider ul li';
		$temp_att = gdl_style_att( 'border-color', get_option(THEME_SHORT_NAME.'_footer_divider_color', '#3b3b3b') . ' !important' );
		gdl_print_style( $temp_sel, $temp_att );
		
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_footer_content_color', '#999999') . ' !important' );
		gdl_print_style( '.footer-wrapper, .footer-wrapper table th', $temp_att );		
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_footer_content_info_color', '#b1b1b1') . ' !important' );
		gdl_print_style( '.footer-wrapper .post-info-color, div.custom-sidebar #twitter_update_list', $temp_att );			

		// Copyright
		$temp_var = get_option(THEME_SHORT_NAME.'_copyright_shadow','#111111');
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_copyright_text', '#808080') . ' !important' );
		$temp_att = $temp_att . gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME.'_copyright_background', '#202020') . ' !important' );		
		$temp_att = $temp_att . gdl_style_att( '-moz-box-shadow', 'inset 0px 3px 6px -3px ' . $temp_var );
		$temp_att = $temp_att . gdl_style_att( '-webkit-box-shadow', 'inset 0px 3px 6px -3px ' . $temp_var );
		$temp_att = $temp_att . gdl_style_att( 'box-shadow', 'inset 0px 3px 6px -3px ' . $temp_var );
		gdl_print_style( 'div.copyright-container-wrapper', $temp_att );		
		
		// Contact Form
		$temp_val_frame = get_option(THEME_SHORT_NAME.'_contact_form_frame_color', '#f8f8f8');
		$temp_val_shadow = get_option(THEME_SHORT_NAME.'_contact_form_inner_shadow', '#ececec');
		$temp_sel = 'div.contact-form-wrapper input[type="text"], div.contact-form-wrapper input[type="password"], ';
		$temp_sel = $temp_sel . 'div.contact-form-wrapper textarea, div.custom-sidebar #search-text input[type="text"], ';
		$temp_sel = $temp_sel . 'div.custom-sidebar .contact-widget-whole input, div.comment-wrapper input[type="text"], ';
		$temp_sel = $temp_sel . 'div.comment-wrapper textarea, div.custom-sidebar .contact-widget-whole textarea, ';
		$temp_sel = $temp_sel . 'span.wpcf7-form-control-wrap input[type="text"], span.wpcf7-form-control-wrap input[type="password"], ';
		$temp_sel = $temp_sel . 'span.wpcf7-form-control-wrap input[type="email"], span.wpcf7-form-control-wrap textarea';
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_contact_form_text_color', '#888888') );
		$temp_att = $temp_att . gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME.'_contact_form_background_color', '#fff') );
		$temp_att = $temp_att . gdl_style_att( 'border', '1px solid ' . get_option(THEME_SHORT_NAME.'_contact_form_border_color', '#cfcfcf') );
		$temp_att = $temp_att . gdl_style_att( '-webkit-box-shadow', $temp_val_shadow . ' 0px 1px 4px inset, ' . $temp_val_frame . ' -5px -5px 0px 0px, ' . $temp_val_frame . ' 5px 5px 0px 0px, ' . $temp_val_frame . ' 5px 0px 0px 0px, ' . $temp_val_frame . ' 0px 5px 0px 0px, ' . $temp_val_frame . ' 5px -5px 0px 0px, ' . $temp_val_frame . ' -5px 5px 0px 0px ' );
		$temp_att = $temp_att . gdl_style_att( 'box-shadow', $temp_val_shadow . ' 0px 1px 4px inset, ' . $temp_val_frame . ' -5px -5px 0px 0px, ' . $temp_val_frame . ' 5px 5px 0px 0px, ' . $temp_val_frame . ' 5px 0px 0px 0px, ' . $temp_val_frame . ' 0px 5px 0px 0px, ' . $temp_val_frame . ' 5px -5px 0px 0px, ' . $temp_val_frame . ' -5px 5px 0px 0px ' );
		gdl_print_style( $temp_sel, $temp_att );
		
		// Button
		$temp_sel = 'a.button, button, input[type="submit"], input[type="reset"], ';
		$temp_sel = $temp_sel . 'input[type="button"], a.gdl-button';
		$temp_att = gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME.'_button_background_color', '#f1f1f1') );
		$temp_att = $temp_att . gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_button_text_color', '#7a7a7a') );
		$temp_att = $temp_att . gdl_style_att( 'border', '1px solid ' . get_option(THEME_SHORT_NAME.'_button_border_color', '#dedede') );
		gdl_print_style( $temp_sel, $temp_att );	

		$temp_sel = 'a.button:hover, button:hover, input[type="submit"]:hover, input[type="reset"]:hover, ';
		$temp_sel = $temp_sel . 'input[type="button"]:hover, a.gdl-button:hover';
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_button_text_hover_color', '#7a7a7a') );
		gdl_print_style( $temp_sel, $temp_att );
		
		// Elements shadow
		$temp_val = get_option(THEME_SHORT_NAME.'_elements_shadow','#ececec');
		
		$temp_sel = 'a.button, button, input[type="submit"], input[type="reset"], input[type="button"], a.gdl-button';
		$temp_att = gdl_style_att( '-moz-box-shadow', '1px 1px 3px ' . $temp_val );
		$temp_att = $temp_att . gdl_style_att( '-webkit-box-shadow', '1px 1px 3px ' . $temp_val );
		$temp_att = $temp_att . gdl_style_att( 'box-shadow', '1px 1px 3px ' . $temp_val );
		gdl_print_style( $temp_sel, $temp_att );

		$temp_sel = 'div.gallery-thumbnail-image, div.custom-sidebar .recent-post-widget-thumbnail, .gdl-image-frame, ';
		$temp_sel = $temp_sel . 'div.portfolio-thumbnail-image, div.portfolio-thumbnail-video, div.portfolio-thumbnail-slider, ';
		$temp_sel = $temp_sel . 'div.single-port-thumbnail-image, div.single-port-thumbnail-video, div.single-port-thumbnail-slider, ';
		$temp_sel = $temp_sel . 'div.blog-thumbnail-image, div.blog-thumbnail-slider, div.blog-thumbnail-video';
		$temp_att = gdl_style_att( '-moz-box-shadow', '0px 0px 4px 1px ' . $temp_val );
		$temp_att = $temp_att . gdl_style_att( '-webkit-box-shadow', '0px 0px 4px 1px ' . $temp_val );
		$temp_att = $temp_att . gdl_style_att( 'box-shadow', '0px 0px 4px 1px ' . $temp_val );
		gdl_print_style( $temp_sel, $temp_att );

		$temp_att = gdl_style_att( '-moz-box-shadow', 'inset 3px 0px 3px -3px ' . $temp_val );
		$temp_att = $temp_att . gdl_style_att( '-webkit-box-shadow', 'inset 3px 0px 3px -3px ' . $temp_val );
		$temp_att = $temp_att . gdl_style_att( 'box-shadow', 'inset 3px 0px 3px -3px ' . $temp_val );
		gdl_print_style( "div.right-sidebar-wrapper", $temp_att );
		
		$temp_att = gdl_style_att( '-moz-box-shadow', 'inset -3px 0px 3px -3px ' . $temp_val );
		$temp_att = $temp_att . gdl_style_att( '-webkit-box-shadow', 'inset -3px 0px 3px -3px ' . $temp_val );
		$temp_att = $temp_att . gdl_style_att( 'box-shadow', 'inset -3px 0px 3px -3px ' . $temp_val );
		gdl_print_style( "div.left-sidebar-wrapper", $temp_att );

		$temp_att = gdl_style_att( '-moz-box-shadow', '0px 0px 3px ' . $temp_val );
		$temp_att = $temp_att . gdl_style_att( '-webkit-box-shadow', '0px 0px 3px ' . $temp_val );
		$temp_att = $temp_att . gdl_style_att( 'box-shadow', '0px 0px 3px ' . $temp_val );
		gdl_print_style( "div.gdl-price-item .price-item.active", $temp_att );

		// Icon Type
		global $gdl_icon_type;
		
		$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/arrow-right.png) no-repeat' );
		gdl_print_style( "div.single-port-next-nav .right-arrow", $temp_att );
		$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/arrow-left.png) no-repeat' );
		gdl_print_style( "div.single-port-prev-nav .left-arrow", $temp_att );		

		$temp_sel = "div.single-thumbnail-author, ";
		$temp_sel = $temp_sel . "div.archive-wrapper .blog-item .blog-thumbnail-author, ";
		$temp_sel = $temp_sel . "div.blog-item-holder .blog-item2 .blog-thumbnail-author, div.blog-item-holder .blog-item3 .blog-thumbnail-author ";
		$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/author.png) no-repeat 0px 1px' );
		gdl_print_style( $temp_sel, $temp_att );

		$temp_sel = "div.single-thumbnail-date, div.custom-sidebar .recent-post-widget-date, ";
		$temp_sel = $temp_sel . "div.archive-wrapper .blog-item .blog-thumbnail-date, ";
		$temp_sel = $temp_sel . "div.blog-item-holder .blog-item1 .blog-thumbnail-date, ";
		$temp_sel = $temp_sel . "div.blog-item-holder .blog-item2 .blog-thumbnail-date, div.blog-item-holder .blog-item3 .blog-thumbnail-date";
		$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/calendar.png) no-repeat 0px 1px' );
		gdl_print_style( $temp_sel, $temp_att );		
		
		$temp_sel = "div.single-thumbnail-comment, div.archive-wrapper .blog-item .blog-thumbnail-comment, ";
		$temp_sel = $temp_sel . "div.blog-item-holder .blog-item1 .blog-thumbnail-comment, ";
		$temp_sel = $temp_sel . "div.blog-item-holder .blog-item2 .blog-thumbnail-comment, div.blog-item-holder .blog-item3 .blog-thumbnail-comment,";
		$temp_sel = $temp_sel . "div.custom-sidebar .recent-post-widget-comment-num";
		$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/comment.png) no-repeat 0px 1px' );
		gdl_print_style( $temp_sel, $temp_att );	

		$temp_sel = "div.single-thumbnail-tag, div.archive-wrapper .blog-item .blog-thumbnail-tag, ";
		$temp_sel = $temp_sel . "div.blog-item-holder .blog-item2 .blog-thumbnail-tag, div.blog-item-holder .blog-item3 .blog-thumbnail-tag";
		$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/tag.png) no-repeat' );
		gdl_print_style( $temp_sel, $temp_att );	
		
		$temp_sel = "div.custom-sidebar #searchsubmit, ";
		$temp_sel = $temp_sel . 'div.search-wrapper input[type="submit"]';
		$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/find-17px.png) no-repeat center' );
		gdl_print_style( $temp_sel, $temp_att );			
		
		$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/link-small.png) no-repeat' );
		gdl_print_style( 'div.single-port-visit-website', $temp_att );	
		$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/minus-24px.png) no-repeat' );
		gdl_print_style( 'span.accordion-head-image.active, span.toggle-box-head-image.active', $temp_att );			
		$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/plus-24px.png) no-repeat' );
		gdl_print_style( 'span.accordion-head-image, span.toggle-box-head-image', $temp_att );		
		$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/navigation-20px.png)' );
		gdl_print_style( 'div.jcarousellite-nav .prev, div.jcarousellite-nav .next', $temp_att );	
		$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/quotes-18px.png)' );
		gdl_print_style( 'div.testimonial-icon', $temp_att );	
		$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/arrow4.png) no-repeat 0px 14px' );
		gdl_print_style( 'div.custom-sidebar ul li', $temp_att );	
		$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/left-cross-5px.png)' );
		gdl_print_style( 'div.stunning-text-wrapper', $temp_att );	
		
		// Personnal Widget
		$temp_att = gdl_style_att( 'background-image', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/personnal-widget-left.png)' );
		gdl_print_style( 'div.personnal-widget-prev', $temp_att );	
		$temp_att = gdl_style_att( 'background-image', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/personnal-widget-right.png)' );
		gdl_print_style( 'div.personnal-widget-next', $temp_att );			
		
		// Footer Icon Type
		global $gdl_footer_icon_type;
		
		$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_footer_icon_type . '/arrow4.png) no-repeat 0px 14px' );
		gdl_print_style( "div.footer-wrapper div.custom-sidebar ul li", $temp_att );
		$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_footer_icon_type . '/find-17px.png) no-repeat center' );
		gdl_print_style( "div.footer-wrapper div.custom-sidebar #searchsubmit", $temp_att );
		$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_footer_icon_type . '/comment.png) no-repeat 0px 1px' );
		gdl_print_style( "div.footer-wrapper div.custom-sidebar .recent-post-widget-comment-num", $temp_att );
		$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_footer_icon_type . '/calendar.png) no-repeat 0px 1px' );
		gdl_print_style( "div.footer-wrapper div.custom-sidebar .recent-post-widget-date", $temp_att );		

		// Personnal Widget
		$temp_att = gdl_style_att( 'background-image', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/personnal-widget-left.png)' );
		gdl_print_style( 'div.footer-wrapper div.personnal-widget-prev', $temp_att );	
		$temp_att = gdl_style_att( 'background-image', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/personnal-widget-right.png)' );
		gdl_print_style( 'div.footer-wrapper div.personnal-widget-next', $temp_att );		
		
		// Additional Style From The admin panel > general > page style
		gdl_write_data(get_option(THEME_SHORT_NAME.'_additional_style', ''));
		
		$boxed_layout = get_option(THEME_SHORT_NAME.'_enable_boxed_layout', 'enable');
		if( $boxed_layout != 'disable' ){
		
			// Container 
			$temp_val = get_option(THEME_SHORT_NAME . "_container_shadow", '#bbbbbb');
			$temp_att = gdl_style_att( 'background', get_option(THEME_SHORT_NAME . "_container_background", '#ffffff') );
			$temp_att = $temp_att . gdl_style_att( '-moz-box-shadow', '0px 0px 8px ' . $temp_val );
			$temp_att = $temp_att . gdl_style_att( '-webkit-box-shadow', '0px 0px 8px ' . $temp_val );
			$temp_att = $temp_att . gdl_style_att( 'box-shadow', '0px 0px 8px ' . $temp_val );
			gdl_print_style( 'div.all-container-wrapper', $temp_att );			
		}else{
		
				
		}

		
		
	}
	
?>
