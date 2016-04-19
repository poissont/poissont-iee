<?php

/*
  Plugin Name: Ignitiondeck-extend
  Description: Ignitiondeck-extend - Initiondesk Extension for better languages and shortcodes
  Version: 0.7.9
  Author: BiilinkAgency
 */

define('IDCExtend_version', '0.7.9');
define('IDCE_PATH', plugin_dir_path(__FILE__));

define('Bii_plugin_slug',"Biilinkplugin");
define('Bii_menu_title',"Bii Options");
define('Bii_dashicon_menu',"http://upyourtown.com/wp-content/uploads/2016/04/icon_up_projet_hover.png");
define('Bii_menu_slug',"bii-plugin");
define('Bii_plugin_name',"bii-plugin");
define('Bii_dashboard_page',"bii_dashboard");
define('Bii_min_role',"publish_pages");

if (!get_option("bii_IDCE_installed")) {
	update_option("bii_inscriptions", 0);
	update_option("bii_acessmoncompte", 1);
	update_option("bii_IDCE_installed", 1);
}


//Plugin biidebug, ajout de fonctions
require_once(plugin_dir_path(__FILE__) . "/plugins/biidebug/biidebug.php");

//Plugin biiadvanced admin, ajout de fonctionnalités ajax sur l'interface d'admin
require_once(plugin_dir_path(__FILE__) . "/plugins/biiadvanced-admin/biiadvanced-admin.php");

//Plugin biicss, ajout de bootstrap et font awesome
require_once(plugin_dir_path(__FILE__) . "/plugins/biicss/biicss.php");

//Plugin biicheckseo, ajout de scripts permettant de vérifier la SEO des pages parcourues
require_once(plugin_dir_path(__FILE__) . "/plugins/biicheckseo/biicheckseo.php");
//Plugin bii_advanced_shortcodes, ajout de shortcodes
require_once(plugin_dir_path(__FILE__) . "/plugins/biiadvanced_shortcodes/biiadvanced_shortcodes.php");
//Plugin bii_preloader, ajout d'un preloader
require_once(plugin_dir_path(__FILE__) . "/plugins/bii_preloader/bii_preloader.php");

//Include du config
require_once(plugin_dir_path(__FILE__) . "config.php");
require_once(plugin_dir_path(__FILE__) . "/class/extendedClassesFromIDC.php");
require_once(plugin_dir_path(__FILE__) . "functions.php");
require_once(plugin_dir_path(__FILE__) . "shortcodes.php");

