<?php

function layerslider($id = 0, $page = '') {

	// Check id
	if(!isset($id) || empty($id)) {
		echo '[LayerSlider WP] You need to specify the "id" parameter for the layerslider() function call';
		return;
	}

	// Page filter
	if(isset($page) && !empty($page)) {

		// Get page name, ID and categories
		$pagename = basename(get_permalink());
		$pageid = (string) get_the_ID();
		$categories = get_the_category();

		// Get pages
		$pages = explode(',', $page);

		// Iterate over the pages
		foreach($pages as $page) {

			if($page == 'homepage' && is_front_page()) {
				echo layerslider_init(array('id' => $id));

			} elseif($pageid == $page) {
				echo layerslider_init(array('id' => $id));

			} elseif($pagename == $page) {
				echo layerslider_init(array('id' => $id));
			
			} elseif(in_category($page)) {
				echo layerslider_init(array('id' => $id));	
			}
		}


	// All pages
	} else {
		echo layerslider_init(array('id' => $id));
	}
}


function lsGetSkins() {

	// Get skins
	$skins = array_map('basename', glob(LS_ROOT_PATH.'/static/skins/*', GLOB_ONLYDIR));

	// Get 3rd party skins
	// ...

	// Get info
	foreach($skins as $key => $skin) {

		if($skin == 'preview') { continue; }

		// Get the folder and name for fallback
		$ret[$key]['folder'] = $ret[$key]['name'] = $skin;

		// Get screenshot if any
		if(file_exists(LS_ROOT_PATH.'/static/skins/'.$skin.'/screenshot.jpg')) {
			$ret[$key]['screenshot'] = LS_ROOT_PATH.'/static/skins/'.$skin.'/screenshot.jpg';
		}

		// Get skin info if any
		if(file_exists(LS_ROOT_PATH.'/static/skins/'.$skin.'/info.json')) {

			// Get info
			$ret[$key]['info'] = json_decode(file_get_contents(LS_ROOT_PATH.'/static/skins/'.$skin.'/info.json'), true);

			// Override name
			$ret[$key]['name'] = $ret[$key]['info']['name'];
		}
	}

	return $ret;
}

?>
