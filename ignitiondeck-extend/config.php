<?php

function bii_listeClass() {
	$list = [
		"rpdo",
		"global_class",
		"bii_project",
		"terms",
		"term_taxonomy",
		"posts",
		"postmeta",
	];
	return $list;
}

function bii_includeClass() {
	$liste = bii_listeClass();
	$pdpf = plugin_dir_path(__FILE__);
	foreach ($liste as $item) {
		require_once($pdpf . "/class/$item.class.php");
	}
}

bii_includeClass();

function rfidc_localeIDC() {

	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-autocomplete');
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_script('IDCExtend', plugins_url('js/localeIDC.js', __FILE__), array('jquery'), null, true);
	wp_enqueue_style('IDCExtendstyle', plugins_url('css/localeIDC.css', __FILE__));
}

add_action('wp_enqueue_scripts', 'rfidc_localeIDC');



if (!get_option("ignitiondeck_locale")) {
	update_option("ignitiondeck_locale", "fr-FR");
}
if (!get_option("ignitiondeck_lctime")) {
	update_option("ignitiondeck_lctime", "fr-FR");
}
setlocale(LC_TIME, get_option("ignitiondeck_lctime"));

function rfidc_monthlist($lang = "fr-FR") {
	switch ($lang) {
		case "fr-FR":
			$array = [
				"January" => "Janvier",
				"Feburay" => "Février",
				"Marth" => "Mars",
				"April" => "Avril",
				"May" => "Mai",
				"June" => "Juin",
				"July" => "Juillet",
				"August" => "Août",
				"September" => "Septembre",
				"October" => "Octobre",
				"November" => "Novembre",
				"December" => "Décembre",
			];
			break;
		default:
			$array = [
				"January" => "January",
				"Feburay" => "Feburay",
				"Marth" => "Marth",
				"April" => "April",
				"May" => "May",
				"June" => "June",
				"July" => "July",
				"August" => "August",
				"September" => "September",
				"October" => "October",
				"November" => "November",
				"December" => "December",
			];
			break;
	}
	return $array;
}

function rfidc_currency($lang = "fr-FR") {
	switch ($lang) {
		case "fr-FR":
			return ["name" => "euro", "currency" => "€", "position" => "after"];
		case "en-GB":
			return ["name" => "pound", "currency" => "£", "position" => "before"];
		default:
			return ["name" => "dollar", "currency" => "$", "position" => "before"];
	}
}

function rfidc_endmonth($end_time) {
	return rfidc_monthlist(get_option("ignitiondeck_locale"))[$end_time];
}

function rfidc_goal($goal, $id) {
	$unite = rfidc_currency(get_option("ignitiondeck_locale"));
	$amount = get_post_meta($id, 'ign_fund_goal', true) * 1;
	if ($unite["position"] == "before") {
		$amount = $unite["currency"] . $amount;
	} else {
		$amount .= " " . $unite["currency"];
	}
	return $amount;
}

function rfidc_fundraised($goal, $id) {
	$unite = rfidc_currency(get_option("ignitiondeck_locale"));
	$amount = get_post_meta($id, 'ign_fund_raised', true) * 1;
	if ($unite["position"] == "before") {
		$amount = $unite["currency"] . $amount;
	} else {
		$amount .= " " . $unite["currency"];
	}
	return $amount;
}

function rfidc_title_genitif($value) {
//	consoleLog($value);
	if (strpos($value, "' Projects") !== false || strpos($value, "'s Projects") !== false) {
		$value = "Projets de " . str_replace(["' Projects", "'s Projects"], ["", ""], $value);
	}
	return $value;
}

function rfidc_display_currency($value, $id) {
//	pre($value,"red");
	return $value;
}

function rfidc_status($value, $lang = "fr-FR") {
	return rfidc_status_traduction($lang)[$value];
}

function rfidc_status_traduction($lang = "fr-FR") {
	$array = [
		"inherit" => "inherit",
		"draft" => "draft",
		"pending" => "pending",
		"publish" => "publish",
		"trash" => "trash",
		"auto-draft" => "auto-draft",
	];
	switch ($lang) {
		case "fr-FR":
			return [
				"inherit" => "hérité",
				"draft" => "brouillon",
				"pending" => "en attente",
				"publish" => "publié",
				"trash" => "corbeille",
				"auto-draft" => "corbeille",
			];
		default:return $array;
	}
}

add_filter('id_end_month', 'rfidc_endmonth', 10, 1);
add_filter('id_project_goal', 'rfidc_goal', 10, 2);
add_filter('id_funds_raised', 'rfidc_fundraised', 10, 2);
add_filter('id_display_currency', 'rfidc_see', 10, 2);
add_filter('the_title', 'rfidc_title_genitif', 10, 2);


add_action('after_setup_theme', 'bii_ast');

function bii_ast() {
	add_filter('wp_nav_menu_items', 'bii_removepillconnexion', 10, 2);
}

function bii_removepillconnexion($nav, $args) {
	$url = get_bloginfo('url');
	$inscription = $moncompte = $connexion = "";
	if (get_option("bii_inscriptions")) {
		$inscription = '<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-id-custom uyt-inscription"><a href="' . $url . '/inscription/"><span class="fa fa-user-plus"></span> S\'inscrire</a></li>';
	}
	if (get_option("bii_acessmoncompte")) {
		$moncompte = '<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-id-custom uyt uyt-moncompte"><a href="' . $url . '/mon-compte/"><span class="fa fa-user"></span> Mon compte</a></li>';
		$connexion = '<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-id-custom uyt uyt-connexion"><a href="' . $url . '/mon-compte/"><span class="fa fa-sign-in"></span> Se connecter</a></li>';
	}
	$remove = [
		'">Mon compte</a>',
		'">Se connecter</a>',
		'Déconnexion',
		'Create Account',
		'<li class="login right">',
		'?page_id=6',
		'?action=register',
		'createaccount buttonpadding',
	];
	$replace = [
		'"><span class="fa fa-user"></span> Mon compte</a>',
		'"><span class="fa fa-sign-in"></span> Se connecter</a>',
		"<span class='fa fa-sign-out'></span> Se déconnecter",
		"<span class='fa fa-user-plus'></span> S'inscrire",
		'<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-id-custom uyt">',
		"mon-compte",
		"",
		"menu-item menu-item-type-custom menu-item-object-custom menu-item-id-custom uyt uyt-moncompte",
	];
	$nav = str_replace($remove, $replace, $nav);
	return $nav;
}

function bii_menu() {

	add_menu_page(__(global_class::wp_slug_menu()), __(global_class::wp_titre_menu()), global_class::wp_min_role(), global_class::wp_nom_menu(), global_class::wp_dashboard_page(), global_class::wp_dashicon_menu());
}

add_action('admin_menu', 'bii_menu');

function bii_dashboard() {
	wp_enqueue_script('admin-init', plugins_url('/admin/js/dashboard.js', __FILE__), array('jquery'), null, true);
	wp_enqueue_style('bii-admin-css', plugins_url('/admin/css/admin.css', __FILE__));
	include('admin/dashboard.php');
}

function bii_ajax_changewpoption() {
	include("ajax/ajax_change_wp_option.php");
	die();
}

function bii_get_post_ajax() {
	include("ajax/getPost.php");
	die();
}

add_action('wp_ajax_bii_get_post', 'bii_get_post_ajax');
add_action('wp_ajax_nopriv_bii_get_post', 'bii_get_post_ajax');

add_action('wp_ajax_bii_change_wp_option', 'bii_ajax_changewpoption');

function bii_dashboard_button_main() {
	$array_active = ["désactivé", "activé"];
	?>
	<tr><td>Le plugin est </td><td class="notbutton"><strong><?= $array_active[get_option("bii_IDCE_installed")]; ?></strong></td></tr>
	<tr><td>Les inscriptions sont </td><td><?= bii_makebutton("bii_inscriptions", true, true); ?></td></tr>
	<tr><td>L'accès au compte est </td><td><?= bii_makebutton("bii_acessmoncompte"); ?></td></tr>
	<?php
}

function bii_option_submit() {
	logRequestVars();
	?>
	<div class="notice">
		<p>Les modifications ont été enregistrées</p>
	</div>
	<?php
}

add_action("bii_informations", "bii_dashboard_button_main", 1);
add_action("bii_options_submit", "bii_option_submit", 1);

add_filter("bii_h1", function($title = "") {
	if (!$title) {
		$title = get_the_title();
	}
	return $title;
}, 10, 1);

add_filter("bii_bootstrap_class", function($attrs, $md, $sm, $xs, $xss, $lg) {
	if (isset($attrs['columns'])) {
		$md = $attrs['columns'];
	}
	if (isset($attrs['columns-large'])) {
		$lg = $attrs['columns-large'];
	}
	if (isset($attrs['columns-middle'])) {
		$md = $attrs['columns-middle'];
	}
	if (isset($attrs['columns-tablet'])) {
		$sm = $attrs['columns-tablet'];
	}
	if (isset($attrs['columns-phone'])) {
		$xs = $attrs['columns-phone'];
	}
	if (isset($attrs['columns-phone-portrait'])) {
		$xss = $attrs['columns-phone-portrait'];
	}
	$class_bootstrap = bootstrap_builder($md, $sm, $xs, $xss, $lg);

	return $class_bootstrap;
}, 10, 6);

add_filter('id_register_project_post_rewrite', function($value) {
	$array = array('slug' => "nos-projets", 'with_front' => true);
	return $array;
}, 10, 1);

add_action("between_header_and_containerwrapper", function($title = "") {
	$title = apply_filters("bii_h1", $title);
	?>
	<div id="site-description">
		<h1><?= $title ?></h1>
	</div>
	<?php
	if (is_front_page()) {
		echo do_shortcode("[rev_slider_alias_homeslider]");
	}
}, 10, 1);

add_action("bii_before_footer", function() {
	?>
	<div class="bii-left-menu">
		<div class="close-toogle"><i class="fa fa-times"></i></div>
		<div class="search-wrapper">
			<div id="header-search-left" class="search-form">
				<form method="get" id="searchform" action="http://upyourtown.com/">
					<div>
						 <!--<label for="s-foot" class="screen-reader-text"><i class="fa fa-search"></i></label>--> 
						<input type="text" placeholder="Votre recherche" value="" name="s" id="s-foot">
						<input type="submit" id="searchsubmit_footer" value="Chercher">
					</div>
				</form>
			</div>


		</div>

		<?php
		// Using wp_nav_menu() to display menu
		wp_nav_menu(array(
			'menu' => 'main-menu', // Select the menu to show by Name
			'class' => "",
			'container' => false, // Remove the navigation container div
			'theme_location' => 'main-menu',
			'walker' => new Stellar_Sub_Menu(),
			'fallback_cb' => 'stellar_default_menu'
			)
		);
		?>

	</div>
	<?php
});

add_action("idc_below_login_form", function() {
//	if($_SERVER["REMOTE_ADDR"] == "77.154.194.84"){
	do_action('facebook_login_button');
//		}
});



add_filter("idc_class_containerwrapper", function($post_type, $is_home) {
	$class = "";
	if ($post_type) {
		$class.= " $post_type";
	}
	$class .= " containerwrapper";
	if ($is_home) {
		$class .= "-home";
	}
	$uid = get_current_user_id();
	if ($uid) {
		$class .= " bii-uc";
	} else {
		$class .= " bii-ud";
	}
	$class .= apply_filters("idc_moar_class_containerwrapper");
	return $class;
}, 10, 2);

add_action("idf_general_social_buttons", function() {
	global $post;
	$url = get_permalink();
	$text = $titre_campagne = $subject = $bodymail = $nom_entr = "";
	$bloginfoname = get_bloginfo("name");
	$idpost = $post->ID;
//	pre($idpost);
	if ($idpost) {
		$titre_campagne = $post->post_title;
		$nom_entr = get_post_meta($idpost, "ign_company_name")[0];
		$subject = $text = "Venez découvrir le projet de financement partipatif de $nom_entr sur $bloginfoname";
		$bodymail = "Vous vouvez voir ce projet sur l'adresse suivante : $url";
	}

	$array_social = [
		'facebook' => ["icon" => "facebook", "text" => "partager sur facebook", "href" => "https://www.facebook.com/sharer/sharer.php?u=$url"],
		'twitter' => ["icon" => "twitter", "text" => "partager sur twitter", "href" => "https://twitter.com/intent/tweet/?url=$url&text=$text"],
		'google+' => ["icon" => "google-plus", "text" => "partager sur google+", "href" => "https://plus.google.com/share?url=$url&hl=fr"],
//		'pinterest' => ["icon" => "pinterest", "text" => "pin-it","href"=>"https://pinterest.com/pin/create/button/?url=$url&media={media}&description={description}"],
		'mail' => ["icon" => "envelope", "text" => "envoyer par mail", "href" => "mailto:?subject=$subject&body=$bodymail"],
	];
	?>
	<ul>
		<?php foreach ($array_social as $key => $val) { ?>
			<li class="social "><a href="<?= $val["href"] ?>" target="_blank"><span class="fa fa-<?= $val["icon"] ?>"></span> <?= $val["text"] ?></a></li>
			<?php } ?>
	</ul>
	<?php
});
