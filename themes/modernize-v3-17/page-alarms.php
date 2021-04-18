<?php
/*
* Template Name: Alarms Landing
*/?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<title><?php bloginfo('name'); ?> <?php wp_title(); ?></title>
<meta name="robots" content="noindex" />
<meta name="robots" content="nofollow" />
<!--[if IE]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<link href="<?php bloginfo('template_url'); ?>/css/main.css" rel="stylesheet" type="text/css">
<script src="https://use.typekit.net/ihn4kvg.js"></script>
<script>try{Typekit.load({ async: false });}catch(e){}</script>
<!-- Favicon
   ================================================== -->
	<?php 
		if(get_option( THEME_SHORT_NAME.'_enable_favicon','disable') == "enable"){
			$gdl_favicon = get_option(THEME_SHORT_NAME.'_favicon_image');
			if( $gdl_favicon ){
				$gdl_favicon = wp_get_attachment_image_src($gdl_favicon, 'full');
				echo '<link rel="shortcut icon" href="' . $gdl_favicon[0] . '" type="image/x-icon" />';
			}
		} 
	?>
<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '372209523145413', {
em: 'insert_email_variable,'
});
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=372209523145413&ev=PageView&noscript=1"
/></noscript>
<!-- DO NOT MODIFY -->
<!-- End Facebook Pixel Code -->
<!-- Google Tag Manager -->

<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PGMSNQM');</script>
<!-- End Google Tag Manager -->

<!-- Google Tag Manager (noscript) -->

<script type="text/javascript" src="//cdn.callrail.com/companies/502395143/abb55bde3cab99d4ac4e/12/swap.js"></script>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PGMSNQM" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<script type="text/javascript">
setTimeout(function(){var a=document.createElement("script");
var b=document.getElementsByTagName("script")[0];
a.src=document.location.protocol+"//script.crazyegg.com/pages/scripts/0017/3725.js?"+Math.floor(new Date().getTime()/3600000);
a.async=true;a.type="text/javascript";b.parentNode.insertBefore(a,b)}, 1);
</script>
</head>

<body class="alarm-landing">
	<div class="page-wrapper">
		<div class="section-1 hero-section section center-container" style="background: url(<?php the_field('hero_bg'); ?>) no-repeat top center;
								    -webkit-background-size: cover;
								    -moz-background-size: cover;
								    -o-background-size: cover;
								    background-size: cover;">
			<div class="theme-container">
				<div class="row">
					<div class="col-sm-6">
						<div class="copy-wrapper">
							<?php the_field('top_text'); ?>
							<a class="call-cta" href="tel:<?php the_field('phone_number'); ?>"><span>Call Now</span><?php the_field('phone_number'); ?></a>
						</div>
					</div>
					<div class="col-sm-6 blurb hidden-sm hidden-md hidden-lg">
						<img class="price-blurb img-responsive" src="<?php the_field('price_image'); ?>" alt="$19.95 Per Month">
					</div>
					<img class="price-blurb blurb-md hidden-xs" src="<?php the_field('price_image'); ?>" alt="$19.95 Per Month">	
				</div>
			</div>
			<div class="ribbon">
				<h2><?php the_field('top_ribbon_text'); ?></h2>
			</div>	
		</div>
		<div class="section-2 section">
			<div class="row">
				<div class="col-sm-4">
					<img class="img-responsive receiver" src="<?php the_field('left_image'); ?>" alt="">
				</div>
				<div class="col-sm-4">
					<h3 class="price"><span><?php the_field('price'); ?></span>/Month</h3>
					<?php the_field('middle_text'); ?>
					<a class="call-cta" href="tel:<?php the_field('phone_number'); ?>"><span>Call Now</span><?php the_field('phone_number'); ?></a>
				</div>
				<div class="col-sm-4">
					<img class="img-responsive necklace" src="<?php the_field('right_image'); ?>" alt="">
					<?php the_field('right_text'); ?>
				</div>
			</div>
		</div>
		<div class="section section-3" style="background: url(<?php the_field('use_bg'); ?>) no-repeat top center;
								    -webkit-background-size: cover;
								    -moz-background-size: cover;
								    -o-background-size: cover;
								    background-size: cover;">
			<div class="theme-container clearfix">
				<div class="col-xs-12 col-lg-6">
					<?php the_field('use_content'); ?>
				</div>
				<img class="visible-xs img-responsive" src="<?php bloginfo('template_url'); ?>/assets/images/couple.png">
			</div>
		</div>
		<div class="section section-4">
			<div class="theme-container">
				<div class="row">
					<div class="col-xs-12 col-sm-4 visible-xs">
						<img class="img-responsive" src="<?php the_field('product_image'); ?>">
					</div>
					<div class="col-xs-12 col-sm-4 left-callout">
						<?php the_field('callout_left_content'); ?>
					</div>
					<div class="col-xs-12 col-sm-4 visible-xs">
						<?php the_field('callout_right_content'); ?>
					</div>
					<div class="col-xs-12 col-sm-4 hidden-xs">
						<img class="img-responsive" src="<?php the_field('product_image'); ?>">
					</div>
					<div class="col-xs-12 col-sm-4 hidden-xs">
						<?php the_field('callout_right_content'); ?>
					</div>
					<div class="product-cta col-xs-12">
						<h2><?php the_field('callout_middle_content'); ?></h2>
					</div>
				</div>
			</div>
			<div class="ribbon">
				<h2><?php the_field('callout_ribbon_text'); ?></h2>
			</div>
		</div>
	</div>
	<div class="section section-5" style="background: url(<?php the_field('bottom_background_image'); ?>) no-repeat top center;
								    -webkit-background-size: cover;
								    -moz-background-size: cover;
								    -o-background-size: cover;
								    background-size: cover;">
		<div class="theme-container clearfix">
			<div class="col-xs-12">
				<a class="call-cta" href="tel:<?php the_field('phone_number'); ?>"><span>Call Now</span><?php the_field('phone_number'); ?></a>
				<p class="big-p"><?php the_field('bottom_text'); ?></p>
				<ul>
					<li><img src="<?php bloginfo('template_url'); ?>/assets/images/logo-csaa.jpg" alt=""></li>
					<li><img src="<?php bloginfo('template_url'); ?>/assets/images/logo-authorize.png" alt=""></li>
				</ul>
			</div>
		</div>
		<img class="operator-image" src="<?php the_field('right_image_operators'); ?>" alt="operators">
	</div>
	<div class="page-footer">
		<div class="theme-container">
			<div class="row">
				<div class="col-sm-6">
					<p>Copyright © 2012-2016</p>
					<p>Family Care Medical Alarms</p>
				</div>
				<div class="col-sm-6">
					<a class="call-cta" href="tel:<?php the_field('phone_number'); ?>"><span>Call Now</span><?php the_field('phone_number'); ?></a>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
</body>
</html>
