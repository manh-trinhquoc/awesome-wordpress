		</div> <!-- header content wrapper -->
	</div> <!-- content container -->
	<div class="footer-container-wrapper">	
		<div class="footer-wrapper">
			<!-- Get Footer Widget -->
			<?php $gdl_show_footer = get_option(THEME_SHORT_NAME.'_show_footer','enable'); ?>
			<?php if( $gdl_show_footer == 'enable' ){ ?>
				<div class="container mt0">
					<div class="footer-widget-wrapper">
						<?php
							$gdl_footer_class = array(
								'footer-style1'=>array('1'=>'four columns', '2'=>'four columns', '3'=>'four columns', '4'=>'four columns'),
								'footer-style2'=>array('1'=>'eight columns', '2'=>'four columns', '3'=>'four columns', '4'=>'display-none'),
								'footer-style3'=>array('1'=>'four columns', '2'=>'four columns', '3'=>'eight columns', '4'=>'display-none'),
								'footer-style4'=>array('1'=>'one-third column', '2'=>'one-third column', '3'=>'one-third column', '4'=>'display-none'),
								'footer-style5'=>array('1'=>'two-thirds column', '2'=>'one-third column', '3'=>'display-none', '4'=>'display-none'),
								'footer-style6'=>array('1'=>'one-third column', '2'=>'two-thirds column', '3'=>'display-none', '4'=>'display-none'),
								);
							$gdl_footer_style = get_option(THEME_SHORT_NAME.'_footer_style', 'footer-style1');
						 
							for( $i=1 ; $i<=4; $i++ ){
								if( $gdl_footer_class[$gdl_footer_style][$i] != 'display-none' ){
									echo '<div class="' . $gdl_footer_class[$gdl_footer_style][$i] . ' mt0">';
									dynamic_sidebar('Footer ' . $i);
									echo '</div>';
								}
							}
						?>
						<div class="clear"></div>
					</div>
				</div> 
			<?php } ?>
		</div> <!-- footer wrapper -->
	</div> <!-- footer container wrapper --> 
	
	<!-- Get Copyright Text -->
	<?php 
		$gdl_show_copyright = get_option(THEME_SHORT_NAME.'_show_copyright','enable');
		if( $gdl_show_copyright == 'enable' ){ 
			echo '<div class="copyright-container-wrapper">';
			
			echo '<div class="copyright-container container">';
			echo '<div class="copyright-left">';
			echo do_shortcode( __(get_option(THEME_SHORT_NAME.'_copyright_left_area'), 'gdl_front_end') );
			echo '</div>';
			echo '<div class="copyright-right">';
			echo do_shortcode( __(get_option(THEME_SHORT_NAME.'_copyright_right_area'), 'gdl_front_end') );
			echo '</div>';
			echo '<div class="clear"></div>';
			echo '</div>'; // copyright container
			
			echo '</div>'; // copyright container wrapper
		} 
	?>	
	
	</div> <!-- all-container-wrapper -->
</div> <!-- body-wrapper -->
	
<?php wp_footer(); ?>
<script type="text/javascript">
jQuery( '.contact-submit' ).click(function() {
	fbq('track', 'CompleteRegistration');
});
</script>
<script type="text/javascript"> 	
	<?php include ( SERVER_PATH ."/javascript/cufon-replace.php" ); ?>
</script>
</body>
</html>