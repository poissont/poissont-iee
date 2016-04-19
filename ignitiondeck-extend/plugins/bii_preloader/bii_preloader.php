<?php
/*
  Plugin Name: bii_preloader
  Description: Preloader
  Version: 0.3
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */

define('bii_preloader_version', '0.3');
if (!get_option("bii_preloader_installed")) {
	update_option("bii_preloader_installed", 1);
	update_option("bii_usepreloader", 1);
}

function biipreloader_enqueueJS() {
	if (get_option("bii_usepreloader")) {
		wp_enqueue_script('bii_preloader', plugins_url('js/preloader.js', __FILE__), array('jquery', 'util'), false, true);
		wp_enqueue_style('preloader', plugins_url('css/preloader.css', __FILE__));
	}
}

function bii_SC_preloader($atts) {
	$timeout = 1000;
	$fading = 1000;
	if (isset($atts["timeout"])) {
		$timeout = $atts["timeout"] * 1;
	}
	if (isset($atts["fading"])) {
		$fading = $atts["fading"] * 1;
	}
	ob_start();
	?>
	<div id="bii_preloader" data-timeout="<?= $timeout; ?>" data-fading="<?= $fading; ?>">
		<div class="text-preloader">
			<?= stripcslashes(get_option("bii_preloader_text")) ?>
		</div>


	</div>
	<?php
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}

add_shortcode('bii_preloader', 'bii_SC_preloader');

add_action('add_meta_boxes', 'bii_preloader_metaboxes');

function bii_preloader_metaboxes() {
	add_meta_box("biipreload", "Preloader", "bii_MB_preloader", ["post", "page"], "normal");
}

function bii_MB_preloader($post) {
	if (get_option("bii_usepreloader")) {
		$useprl = get_post_meta($post->ID, 'bii_usepreloader', true);
		$timeout = get_post_meta($post->ID, 'bii_preloadertimeout', true);
		$fading = get_post_meta($post->ID, 'bii_preloaderfading', true);
		if (!$useprl) {
			$useprl = 0;
		}
		if (!$fading) {
			$fading = 1000;
		}
		if (!$timeout) {
			$timeout = 1000;
		}
		$checked = "";
		$hidden = "hidden";
		if ($useprl == 1) {
			$checked = "checked='checked'";
			$hidden = "";
		}
		?>

		<label class="bii_label" for="bii_usepreloader-cbx">Utiliser le preloader</label>
		<input type='hidden'  id='bii_usepreloader' name='bii_usepreloader' value='<?= $useprl; ?>' />
		<input type='checkbox'  id='bii_usepreloader-cbx' name='bii_usepreloader-cbx' class='cbx-data-change form-control' data-change='bii_usepreloader' <?= $checked ?> />
		<div class="bii_preloader <?= $hidden; ?>">
			<hr/>
			<div>
				<label class="bii_label" for="bii_preloadertimeout">Délai avant début de fondu</label>
				<input name="bii_preloadertimeout" id="bii_preloadertimeout" type="number"  id="menu_order" value="<?= $timeout ?>">
			</div>
			<div>
				<label class="bii_label" for="bii_preloaderfading">Durée de l'animation de fondu</label>
				<input name="bii_preloaderfading" id="bii_preloaderfading" type="number"  id="menu_order" value="<?= $fading ?>">
			</div>
		</div>
		<style>
			.bii_label{
				display: inline-block;
				width: 17%;
			}
		</style>
		<script>
			jQuery(".cbx-data-change").on("click", function () {
				jQuery(".bii_preloader").removeClass("hidden");
				var id = jQuery(this).attr("data-change");

				console.log(id);
				var checked = jQuery(this).is(":checked");
				var value = 0;
				if (checked == true) {
					value = 1;

				}
				jQuery("#" + id).val(value);
				console.log(jQuery("#" + id));
			});
		</script>
		<?php
	}
}

add_action('save_post', 'save_metaboxes');

function save_metaboxes($post_ID) {
	$array_values = ["bii_usepreloader", "bii_preloadertimeout", "bii_preloaderfading"];
	foreach ($array_values as $val) {
		if (isset($_POST[$val])) {
			update_post_meta($post_ID, $val, esc_html($_POST[$val]));
		}
	}
}

function bii_build_preloader($post_ID) {
//	consoleLog($post_ID);
	if (!$post_ID) {
		if (isset($_GET["preview_id"])) {
			$post_ID = $_GET["preview_id"];
		}
	}
	$useprl = get_post_meta($post_ID, 'bii_usepreloader', true) && get_option("bii_usepreloader");
	if ($useprl) {
		$timeout = get_post_meta($post_ID, 'bii_preloadertimeout', true);
		$fading = get_post_meta($post_ID, 'bii_preloaderfading', true);

		echo bii_SC_preloader(["timeout" => $timeout, "fading" => $fading]);
	}
}

biipreloader_enqueueJS();

add_action("bii_informations", function() {
	?>
	<tr><td>Le preloader est  </td><td><?= bii_makebutton("bii_usepreloader"); ?></td></tr>
	<?php
});
if (get_option("bii_usepreloader")) {
	add_action("bii_options_title", function() {
		?>
		<li role="presentation" class="hide-relative" data-relative="pl-Preloader"><i class="fa fa-desktop"></i> Preloader</li>
		<?php
	});
	add_action("bii_options", function() {
		?>
		<div class="hidden col-xxs-12 pl-Preloader bii_option">
			<?php
			bii_makestuffbox("bii_preloader_text", "HTML à afficher", "textarea");
			?>
		</div>
		<?php
	});
}
add_action("bii_options_submit", function() {
	$tableaucheck = ["bii_preloader_text"];
	foreach ($tableaucheck as $itemtocheck) {
		if (isset($_POST[$itemtocheck])) {
			update_option($itemtocheck, $_POST[$itemtocheck]);
		}
	}
}, 5);
