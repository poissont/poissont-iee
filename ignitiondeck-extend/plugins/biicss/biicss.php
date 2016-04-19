<?php

/*
  Plugin Name: Biicss
  Description: Ajoute bootstrap et font awesome sur le site et son back office
  Version: 1.2
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */
define('bii_css_version', '1.3');

add_action('admin_enqueue_scripts', function () {
	if (isset($_GET["page"]) && (strpos($_GET["page"], "bii") !== false) || (strpos($_GET["page"], "_list") !== false) || (strpos($_GET["page"], "_edit") !== false)) {
		wp_enqueue_style('bootstrap', plugins_url('css/bootstrap.css', __FILE__));
//	wp_enqueue_style('bootstrap-theme', plugins_url('css/bootstrap-theme.css', __FILE__));
		wp_enqueue_style('font-awesome', plugins_url('css/font-awesome.min.css', __FILE__));
//	wp_enqueue_style('stylepage', plugins_url('css/style.css', __FILE__));
	}
});

add_action('wp_enqueue_scripts', function() {
	if (get_option("bii_useleftmenu")) {
		wp_enqueue_script('jquery-effects-core');
		wp_enqueue_style('leftmenu', plugins_url('css/leftmenu.css', __FILE__));
		wp_enqueue_script('leftmenuscript', plugins_url('js/leftmenu.js', __FILE__), array('jquery','jquery-ui-core','jquery-effects-core', 'util'), false, true);
	}
});

add_action("bii_informations", function() {
	?>
	<tr><td>Le menu à gauche est </td><td><?= bii_makebutton("bii_useleftmenu"); ?></td></tr>
	<?php
});

add_filter("bii_class_menu",function($arg1,$arg2){
	$class = "";
	if(get_option("bii_useleftmenu")){
		$class.="bii-left-menu";
	}
	return $class;
},10,2);
add_action("between_header_and_containerwrapper",function(){
	?>
	<div id="bii-overlay"></div>
	<?php
},10,2);