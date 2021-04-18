<?php

	/*	
	*	Goodlayers Personnel Option File
	*	---------------------------------------------------------------------
	* 	@version	1.0
	* 	@author		Goodlayers
	* 	@link		http://goodlayers.com
	* 	@copyright	Copyright (c) Goodlayers
	*	---------------------------------------------------------------------
	*	This file create and contains the personnel post_type meta elements
	*	---------------------------------------------------------------------
	*/
	
	add_action( 'init', 'create_personnel' );
	function create_personnel() {
	
		$labels = array(
			'name' => __('Personnel', 'gdl_back_office'),
			'singular_name' => __('Personnel Item', 'gdl_back_office'),
			'add_new' => __('Add New', 'gdl_back_office'),
			'add_new_item' => __('Personnel Name', 'gdl_back_office'),
			'edit_item' => __('Personnel Name', 'gdl_back_office'),
			'new_item' => __('New Personnel', 'gdl_back_office'),
			'view_item' => '',
			'search_items' => __('Search Personnel', 'gdl_back_office'),
			'not_found' =>  __('Nothing found', 'gdl_back_office'),
			'not_found_in_trash' => __('Nothing found in Trash', 'gdl_back_office'),
			'parent_item_colon' => ''
		);
		
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array('slug' => 'personnel', 'with_front' => false),
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => 5,
			'exclude_from_search' => true,
			'supports' => array('title','editor','author','thumbnail','excerpt')
		); 
		  
		register_post_type( 'personnel' , $args);
		
		register_taxonomy(
			"personnel-category", array("personnel"), array(
				"hierarchical" => true, 
				"label" => "Categories", 
				"singular_label" => "Categories", 
				"rewrite" => true));
		register_taxonomy_for_object_type('personnel-category', 'personnel');
		
	}
	
	// add table column in edit page
	add_filter("manage_edit-personnel_columns", "show_personnel_column");	
	function show_personnel_column($columns){
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => "Title",
			"author" => "Author",
			"personnel-category" => "personnel Categories",
			"date" => "date");
		return $columns;
	}
	add_action("manage_posts_custom_column","personnel_custom_columns");
	function personnel_custom_columns($column){
		global $post;

		switch ($column) {
			case "personnel-category":
			echo get_the_term_list($post->ID, 'personnel-category', '', ', ','');
			break;
		}
	}

	$personnel_meta_boxes = array(	
		"Position" => array(
			'title'=> __('POSITION', 'gdl_back_office'),
			'name'=>'personnel-option-position',
			'type'=>'inputtext')
	);
	
	add_action('add_meta_boxes', 'add_personnel_option');
	function add_personnel_option(){	
	
		add_meta_box('personnel-option', __('Personnel Option','gdl_back_office'), 'add_personnel_option_element',
			'personnel', 'normal', 'high');
			
	}
	
	function add_personnel_option_element(){
	
		global $post, $personnel_meta_boxes;
		echo '<div id="gdl-overlay-wrapper">';
		
		?> <div class="testimonial-option-meta" id="testimonial-option-meta"> <?php
		
			set_nonce();
			
			foreach($personnel_meta_boxes as $meta_box){

				$meta_box['value'] = get_post_meta($post->ID, $meta_box['name'], true);
				print_meta($meta_box);
				
			}
			
		?> </div> <?php
		
		echo '</div>';
		
	}
	
	function save_personnel_option_meta($post_id){
	
		global $personnel_meta_boxes;
		$edit_meta_boxes = $personnel_meta_boxes;
		
				// save
		foreach ($edit_meta_boxes as $edit_meta_box){
		
			if(isset($_POST[$edit_meta_box['name']])){	
				$new_data = stripslashes($_POST[$edit_meta_box['name']]);		
			}else{
				$new_data = '';
			}
			
			$old_data = get_post_meta($post_id, $edit_meta_box['name'],true);
			save_meta_data($post_id, $new_data, $old_data, $edit_meta_box['name']);
			
		}
		
	}	
?>