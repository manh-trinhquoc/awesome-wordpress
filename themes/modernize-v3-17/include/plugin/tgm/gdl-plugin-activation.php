<?php
require_once(SERVER_PATH . '/include/plugin/tgm/class-tgm-plugin-activation.php');

add_action( 'tgmpa_register', 'gdlr_register_required_plugins' );
if( !function_exists('gdlr_register_required_plugins') ){
	function gdlr_register_required_plugins(){
		$plugins = array(
			array(
				'name'     				=> 'LayerSlider',
				'slug'     				=> 'LayerSlider', 
				'source'   				=> SERVER_PATH . '/include/plugin/tgm/LayerSlider.zip',
				'version'               => '5.2.0',
				'required' 				=> true,
				'force_activation' 		=> false,
				'force_deactivation' 	=> true, 
			)
		);

		$config = array(
			'domain'       		=> 'gdl_front_end',         
			'default_path' 		=> '',                         
			'parent_menu_slug' 	=> 'themes.php', 			
			'parent_url_slug' 	=> 'themes.php', 			
			'menu'         		=> 'install-required-plugins', 
			'has_notices'      	=> true,                       
			'is_automatic'    	=> false,					   
			'message' 			=> '',						
			'strings'      		=> array(
				'page_title'                       			=> __('Install Required Plugins', 'gdl_front_end' ),
				'menu_title'                       			=> __('Install Plugins', 'gdl_front_end' ),
				'installing'                       			=> __('Installing Plugin: %s', 'gdl_front_end' ), 
				'oops'                             			=> __('Something went wrong with the plugin API.', 'gdl_front_end' ),
				'notice_can_install_required'     			=> _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ),
				'notice_can_install_recommended'			=> _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), 
				'notice_cannot_install'  					=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
				'notice_can_activate_required'    			=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), 
				'notice_can_activate_recommended'			=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), 
				'notice_cannot_activate' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
				'notice_ask_to_update' 						=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s)
				'notice_cannot_update' 						=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
				'install_link' 					  			=> _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
				'activate_link' 				  			=> _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
				'return'                           			=> __( 'Return to Required Plugins Installer', 'gdl_front_end' ),
				'plugin_activated'                 			=> __( 'Plugin activated successfully.', 'gdl_front_end' ),
				'complete' 									=> __( 'All plugins installed and activated successfully. %s', 'gdl_front_end' ), 
				'nag_type'									=> 'updated'
			)
		);

		tgmpa( $plugins, $config );
	}
}