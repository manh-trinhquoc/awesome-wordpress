<?php
	
	function gdl_social_share_title(){
		$title = str_replace(' ', '%20', get_the_title());
		$title  = str_replace('{', '%7B', $title);
		$title = str_replace('}', '%7D', $title);
		$title = str_replace('&#038;', 'and', $title);
		return $title;
	}
	
	function include_social_shares(){
		global $gdl_icon_type;
		
		$currentUrl = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		if( !empty($_SERVER['HTTPS']) ){
			$currentUrl = "https://" . $currentUrl;
		}else{
			$currentUrl = "http://" . $currentUrl;
		}
		
		echo '<div class="social-shares">';
		echo '<ul>';
		
		if( get_option(THEME_SHORT_NAME.'_facebook_share') == 'enable'){
			?>
			<li>
				<a href="http://www.facebook.com/share.php?u=<?php echo $currentUrl;?>" target="_blank">
					<img class="no-preload" src="<?php echo GOODLAYERS_PATH;?>/images/icon/<?php echo $gdl_icon_type ?>/social/facebook-share.png">
				</a>
			</li>
			<?php
		}
		if( get_option(THEME_SHORT_NAME.'_twitter_share') == 'enable'){		
			?>
			<li>
				<a href="http://twitter.com/home?status=<?php echo gdl_social_share_title(); ?>%20-%20<?php echo $currentUrl;?>" target="_blank">
					<img class="no-preload" src="<?php echo GOODLAYERS_PATH;?>/images/icon/<?php echo $gdl_icon_type ?>/social/twitter-share.png">
				</a>
			</li>
			<?php
		}
		if( get_option(THEME_SHORT_NAME.'_google_share') == 'enable'){		
			?>
			<li>
				<a href="http://www.google.com/bookmarks/mark?op=edit&#038;bkmk=<?php echo $currentUrl;?>&#038;title=<?php echo gdl_social_share_title();?>" target="_blank">
					<img class="no-preload" src="<?php echo GOODLAYERS_PATH;?>/images/icon/<?php echo $gdl_icon_type ?>/social/google-share.png">
				</a>
			</li>
			<?php
		}
		if( get_option(THEME_SHORT_NAME.'_stumble_upon_share') == 'enable'){		
			?>
			<li>
				<a href="http://www.stumbleupon.com/submit?url=<?php echo $currentUrl;?>&#038;title=<?php echo gdl_social_share_title();?>" target="_blank">
					<img class="no-preload" src="<?php echo GOODLAYERS_PATH;?>/images/icon/<?php echo $gdl_icon_type ?>/social/stumble-upon-share.png">
				</a>
			</li>
			<?php
		}
		if( get_option(THEME_SHORT_NAME.'_my_space_share') == 'enable'){		
			?>
			<li>
				<a href="http://www.myspace.com/Modules/PostTo/Pages/?u=<?php echo $currentUrl;?>" target="_blank">
					<img class="no-preload" src="<?php echo GOODLAYERS_PATH;?>/images/icon/<?php echo $gdl_icon_type ?>/social/my-space-share.png">
				</a>
			</li>
			<?php
		}
		if( get_option(THEME_SHORT_NAME.'_delicious_share') == 'enable'){		
			?>
			<li>
				<a href="http://delicious.com/post?url=<?php echo $currentUrl;?>&#038;title=<?php echo gdl_social_share_title();?>" target="_blank">
					<img class="no-preload" src="<?php echo GOODLAYERS_PATH;?>/images/icon/<?php echo $gdl_icon_type ?>/social/delicious-share.png">
				</a>
			</li>
			<?php
		}
		if( get_option(THEME_SHORT_NAME.'_digg_share') == 'enable'){		
			?>
			<li>
				<a href="http://digg.com/submit?url=<?php echo $currentUrl;?>&#038;title=<?php echo gdl_social_share_title();?>" target="_blank">
					<img class="no-preload" src="<?php echo GOODLAYERS_PATH;?>/images/icon/<?php echo $gdl_icon_type ?>/social/digg-share.png">
				</a>
			</li>
			<?php
		}
		if( get_option(THEME_SHORT_NAME.'_reddit_share') == 'enable'){		
			?>
			<li>
				<a href="http://reddit.com/submit?url=<?php echo $currentUrl;?>&#038;title=<?php echo gdl_social_share_title();?>" target="_blank">
					<img class="no-preload" src="<?php echo GOODLAYERS_PATH;?>/images/icon/<?php echo $gdl_icon_type ?>/social/reddit-share.png">
				</a>
			</li>
			<?php
		}
		if( get_option(THEME_SHORT_NAME.'_linkedin_share') == 'enable'){		
			?>
			<li>
				<a href="http://www.linkedin.com/shareArticle?mini=true&#038;url=<?php echo $currentUrl;?>&#038;title=<?php echo gdl_social_share_title(); ?>" target="_blank">
					<img class="no-preload" src="<?php echo GOODLAYERS_PATH;?>/images/icon/<?php echo $gdl_icon_type ?>/social/linkedin-share.png">
				</a>
			</li>
			<?php
		}
		
		if( get_option(THEME_SHORT_NAME.'_google_plus_share') == 'enable'){		
			?>
			<li>		
				<a href="https://plus.google.com/share?url=<?php echo $currentUrl;?>" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
					<img class="no-preload" src="<?php echo GOODLAYERS_PATH;?>/images/icon/<?php echo $gdl_icon_type ?>/social/google-plus-share.png" alt="google-share">
				</a>					
			</li>
			<?php
		}		
		
		echo '</ul>';
		echo '</div>';
	
	}
	
?>