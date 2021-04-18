<?php get_header(); ?>
	<?php 
		if( is_product() ){
			$sidebar = get_option(THEME_SHORT_NAME.'_woo_single_product_sidebar', 'single-prod-no-sidebar');
			$sidebar = str_replace('single-prod-', '', $sidebar);
			
			$left_sidebar = get_option(THEME_SHORT_NAME.'_woo_single_product_left_sidebar');
			$right_sidebar = get_option(THEME_SHORT_NAME.'_woo_single_product_right_sidebar');
		}else{
			$sidebar = get_option(THEME_SHORT_NAME.'_woo_all_product_sidebar', 'all-prod-no-sidebar');
			$sidebar = str_replace('all-prod-', '', $sidebar);
			
			$left_sidebar = get_option(THEME_SHORT_NAME.'_woo_all_product_left_sidebar');
			$right_sidebar = get_option(THEME_SHORT_NAME.'_woo_all_product_right_sidebar');		
		}
		
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
				echo "<div class='gdl-page-float-left'>";
				echo "<div class='gdl-page-item'>";
				
				echo '<div class="sixteen columns mt30 gdl-woo-commerce-wrapper">';
				
				echo '<div class="woo-breadcrumbs-wrapper">';
				woocommerce_breadcrumb();
				echo '</div>';
				
				woocommerce_content();
				echo '</div>';
			
				echo "</div>"; // end of gdl-page-item
				get_sidebar('left');		
				echo "</div>"; // gdl-page-float-left	
				get_sidebar('right');
				
			?>
			<br class="clear">
		</div>
	</div> <!-- content-wrapper -->
	
<?php get_footer(); ?>