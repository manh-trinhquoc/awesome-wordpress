<?php 
	/*	
	*	Goodlayers Function File
	*	---------------------------------------------------------------------
	*	This file include all of important function and features of the theme
	*	to make it available for later use.
	*	---------------------------------------------------------------------
	*/

	// constants
	define('THEME_SHORT_NAME','gdl'); 
	define('THEME_FULL_NAME','Modernize');
	define('GOODLAYERS_PATH', get_template_directory_uri());
	define('SERVER_PATH', get_template_directory() );
	define('AJAX_URL', admin_url( 'admin-ajax.php' ));
	define('FONT_SAMPLE_TEXT', 'Sample Font'); // sample font text of the goodlayers backoffice panel
	
	$date_format = get_option(THEME_SHORT_NAME.'_default_date_format','F d, Y');
	$widget_date_format = get_option(THEME_SHORT_NAME.'_default_widget_date_format','M d, Y');
	define('GDL_DATE_FORMAT', $date_format);
	define('GDL_WIDGET_DATE_FORMAT', $widget_date_format);
	
	$gdl_icon_type = get_option(THEME_SHORT_NAME.'_icon_type','dark');
	$gdl_footer_icon_type = get_option(THEME_SHORT_NAME.'_footer_icon_type','light');

	$gdl_admin_translator = get_option(THEME_SHORT_NAME.'_enable_admin_translator','enable');
	$gdl_is_responsive = get_option(THEME_SHORT_NAME.'_enable_responsive','disable');
	$gdl_is_responsive = ($gdl_is_responsive == 'enable')? true: false;
	$gdl_boxed_layout = get_option(THEME_SHORT_NAME.'_enable_boxed_layout', 'enable');
	
	$default_post_sidebar = get_option(THEME_SHORT_NAME.'_default_post_sidebar','post-no-sidebar');
	$default_post_sidebar = str_replace('post-', '', $default_post_sidebar);
	$default_post_left_sidebar = get_option(THEME_SHORT_NAME.'_default_post_left_sidebar','');
	$default_post_right_sidebar = get_option(THEME_SHORT_NAME.'_default_post_right_sidebar','');	
	
	// for multisite file
	$gdl_custom_stylesheet_name = 'style-custom.css';
	if( is_multisite() && get_current_blog_id() > 1 ){
		$gdl_custom_stylesheet_name = 'style-custom' . get_current_blog_id() . '.css';
	}

	// get the path for the file ( to support child theme )
	if( !function_exists('get_root_directory') ){
		function get_root_directory( $path ){
			if( file_exists( get_stylesheet_directory() . '/' . $path ) ){
				return get_stylesheet_directory() . '/';
			}else{
				return SERVER_PATH . '/';
			}
		}
	}
	
	$temp_root = get_root_directory('include/include-script.php');
	include_once($temp_root . 'include/include-script.php'); // include all javascript and style in to the theme
	$temp_root = get_root_directory('include/plugin/utility.php');
	include_once($temp_root . 'include/plugin/utility.php'); // utility function
	$temp_root = get_root_directory('include/function-regist.php');
	include_once($temp_root . 'include/function-regist.php'); // registered wordpress function
	$temp_root = get_root_directory('include/goodlayers-option.php');
	include_once($temp_root . 'include/goodlayers-option.php'); // goodlayers panel
	$temp_root = get_root_directory('include/plugin/fontloader.php');
	include_once($temp_root . 'include/plugin/fontloader.php'); // load necessary font
	$temp_root = get_root_directory('include/plugin/shortcode-generator.php');
	include_once($temp_root . 'include/plugin/shortcode-generator.php'); // shortcode
	
	// dashboard option
	$temp_root = get_root_directory('include/meta-template.php');
	include_once($temp_root . 'include/meta-template.php'); // input template 
	$temp_root = get_root_directory('include/post-option.php');
	include_once($temp_root . 'include/post-option.php');	// meta of post post_type
	$temp_root = get_root_directory('include/page-option.php');
	include_once($temp_root . 'include/page-option.php'); // meta of page post_type
	$temp_root = get_root_directory('include/portfolio-option.php');
	include_once($temp_root . 'include/portfolio-option.php'); // meta of portfolio post_type
	$temp_root = get_root_directory('include/testimonial-option.php');
	include_once($temp_root . 'include/testimonial-option.php'); // meta of testimonial post_type
	$temp_root = get_root_directory('include/price-table-option.php');
	include_once($temp_root . 'include/price-table-option.php'); // meta of price post_type
	$temp_root = get_root_directory('include/gallery-option.php');
	include_once($temp_root . 'include/gallery-option.php'); // meta of gallery post_type
	$temp_root = get_root_directory('include/personnel-option.php');
	include_once($temp_root . 'include/personnel-option.php'); // meta of personnel post_type	
	
	// exterior plugins
	if(!class_exists('ReallySimpleCaptcha')) {
		$temp_root = get_root_directory('include/plugin/really-simple-captcha/really-simple-captcha.php');
		include_once($temp_root . 'include/plugin/really-simple-captcha/really-simple-captcha.php'); // capcha comment plugin class
	}
	$temp_root = get_root_directory('include/plugin/filosofo-image/filosofo-custom-image-sizes.php');
	include_once($temp_root . 'include/plugin/filosofo-image/filosofo-custom-image-sizes.php'); // Custom image size plugin
	$temp_root = get_root_directory('include/plugin/dropdown-menus.php');
	include_once($temp_root . 'include/plugin/dropdown-menus.php'); // Custom dropdown menu
	
	if(!is_admin()){
		$temp_root = get_root_directory('include/plugin/misc.php');
		include_once($temp_root . 'include/plugin/misc.php');	 // misc function to use at font-end
		$temp_root = get_root_directory('include/plugin/page-item.php');
		include_once($temp_root . 'include/plugin/page-item.php');	 // organize page item element
		$temp_root = get_root_directory('include/plugin/blog-item.php');
		include_once($temp_root . 'include/plugin/blog-item.php');	 // organize blog item element
		$temp_root = get_root_directory('include/plugin/comment.php');
		include_once($temp_root . 'include/plugin/comment.php'); // function to get list of comment
		$temp_root = get_root_directory('include/plugin/pagination/pagination.php');
		include_once($temp_root . 'include/plugin/pagination/pagination.php'); // page divider plugin
		$temp_root = get_root_directory('include/plugin/social-shares.php');
		include_once($temp_root . 'include/plugin/social-shares.php'); // page divider plugin
		$temp_root = get_root_directory('include/plugin/really-simple-captcha/cbnet-really-simple-captcha-comments.php');
		include_once($temp_root . 'include/plugin/really-simple-captcha/cbnet-really-simple-captcha-comments.php'); // capcha comment plugin
	}
	
	// include custom widget
	$temp_root = get_root_directory('include/plugin/custom-widget/custom-blog-widget.php');
	include_once($temp_root . 'include/plugin/custom-widget/custom-blog-widget.php'); 
	$temp_root = get_root_directory('include/plugin/custom-widget/custom-port-widget.php');
	include_once($temp_root . 'include/plugin/custom-widget/custom-port-widget.php'); 
	$temp_root = get_root_directory('include/plugin/custom-widget/custom-port-widget-2.php');
	include_once($temp_root . 'include/plugin/custom-widget/custom-port-widget-2.php'); 
	$temp_root = get_root_directory('include/plugin/custom-widget/popular-post-widget.php');
	include_once($temp_root . 'include/plugin/custom-widget/popular-post-widget.php'); 
	$temp_root = get_root_directory('include/plugin/custom-widget/contact-widget.php');
	include_once($temp_root . 'include/plugin/custom-widget/contact-widget.php'); 
	$temp_root = get_root_directory('include/plugin/custom-widget/flickr-widget.php');
	include_once($temp_root . 'include/plugin/custom-widget/flickr-widget.php'); 
	$temp_root = get_root_directory('include/plugin/custom-widget/twitter-widget.php');
	include_once($temp_root . 'include/plugin/custom-widget/twitter-widget.php');
	$temp_root = get_root_directory('include/plugin/custom-widget/twitteroauth.php');
	include_once($temp_root . 'include/plugin/custom-widget/twitteroauth.php');		
	$temp_root = get_root_directory('include/plugin/custom-widget/personnal-widget.php');
	include_once($temp_root . 'include/plugin/custom-widget/personnal-widget.php');

	if(is_admin()){
		$temp_root = get_root_directory('include/style-custom.php');
		include_once($temp_root . 'include/style-custom.php');		
	}
	
	if( false ){
		if( !function_exists('layerslider_activation_scripts') ){
			$temp_root = get_root_directory('include/plugin/gdl-layerslider.php');
			include_once($temp_root . 'include/plugin/gdl-layerslider.php'); // Layer Slider	
		}
	}
	$temp_root = get_root_directory('include/plugin/tgm/gdl-plugin-activation.php');
	include_once($temp_root . 'include/plugin/tgm/gdl-plugin-activation.php');		
?>