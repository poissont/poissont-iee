<?php

/*
  Plugin Name: Biiadvanced-admin
  Description: Ajoute des fonctionnalités dans l'interface d'admin
  Version: 1.0
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */
define('bii_advancedadmin_version', '1.0');

function bii_enqueueJSAdmin($hook) {
	wp_enqueue_media();
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-autocomplete');
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_script('customscriptadmin', plugins_url('js/admin.js', __FILE__), array('jquery'), false, true);
}

add_action('admin_enqueue_scripts', 'bii_enqueueJSAdmin');

function bii_get_attachment_id_from_url($attachment_url = '') {
	global $wpdb;
	$attachment_id = false;
	if ('' == $attachment_url) {
		return;
	}
	$upload_dir_paths = wp_upload_dir();
	if (false !== strpos($attachment_url, $upload_dir_paths['baseurl'])) {
		$attachment_url = preg_replace('/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url);
		$attachment_url = str_replace($upload_dir_paths['baseurl'] . '/', '', $attachment_url);
		$attachment_id = $wpdb->get_var($wpdb->prepare("SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url));
	}
	return $attachment_id;
}
