<?php
/*
  Plugin Name: BiiDebug
  Description: Ajoute des fonctions de débug, invisibles pour le public
  Version: 2.0
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */
define('bii_debug_version', '2.0');

function biidebug_enqueueJS() {
	wp_enqueue_script('util', plugins_url('js/util.js', __FILE__), array('jquery'), false, true);

	wp_enqueue_script('lazyload2', plugins_url('js/lazyload.js', __FILE__), array('jquery'), false, true);
	wp_enqueue_script('manual-lazyload', plugins_url('js/manual-lazyload.js', __FILE__), array('jquery', 'lazyload2', 'util'), false, true);
}

biidebug_enqueueJS();
if (!(get_option("bii_medium_width"))) {
	update_option("bii_medium_width", 1050);
}
if (!(get_option("bii_small_width"))) {
	update_option("bii_small_width", 985);
}
if (!(get_option("bii_xsmall_width"))) {
	update_option("bii_xsmall_width", 767);
}
if (!(get_option("bii_xxsmall_width"))) {
	update_option("bii_xxsmall_width", 479);
}

function bii_showlogs() {
	?>
	<script type="text/javascript" src="http://l2.io/ip.js?var=myip"></script>
	<script type="text/javascript">
		var ajaxurl = '<?= admin_url('admin-ajax.php'); ?>';
		var bloginfourl = '<?= get_bloginfo("url") ?>';
		var bii_showlogs = false;
		var ip_client = myip;
		if (ip_client == "77.154.194.84") {
			bii_showlogs = true;
		}
		var bii_medium = "(max-width: <?= get_option("bii_medium_width"); ?>px";
		var bii_small = "(max-width: <?= get_option("bii_small_width"); ?>px";
		var bii_xsmall = "(max-width: <?= get_option("bii_xsmall_width"); ?>px";
		var bii_xxsmall = "(max-width: <?= get_option("bii_xxsmall_width"); ?>px";
	</script>
	<?php
}

add_action('wp_head', 'bii_showlogs');
add_action('admin_head', 'bii_showlogs');



/* Retirer emojis */

remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');
add_action("bii_options_title", function() {
	?>

	<li role="presentation" class="hide-relative active hide-publier" data-relative="pl-Informations"><i class="fa fa-info"></i> Informations</li>
	<li role="presentation" class="hide-relative " data-relative="pl-Biidebug"><i class="fa fa-cogs"></i> Biidebug</li>
	<li role="presentation" class="hide-relative hide-publier" data-relative="pl-Shortcodes"><i class="fa fa-cog"></i> Shortcodes</li>
	<?php
}, 1);
if ($_SERVER["REMOTE_ADDR"] == "77.154.194.84") {
	add_action("bii_options_title", function() {
		?>
		<li role="presentation" class="hide-relative hide-publier" data-relative="pl-zdt"><i class="fa fa-wrench"></i> Zone de test</li>
		<?php
	}, 99);
}
add_action("bii_options", function() {
	?>
	<div class="col-xxs-12 pl-Informations bii_option">
		<table>
			<?php do_action("bii_informations"); ?>				
		</table>
	</div>
	<div class="col-xxs-12 pl-Biidebug bii_option hidden">

		<?php
		bii_makestuffbox("bii_medium_width", "pixels maximum md", "number", "col-xxs-12 col-sm-6 col-md-3");
		bii_makestuffbox("bii_small_width", "pixels maximum sm", "number", "col-xxs-12 col-sm-6 col-md-3");
		bii_makestuffbox("bii_xsmall_width", "pixels maximum xs", "number", "col-xxs-12 col-sm-6 col-md-3");
		bii_makestuffbox("bii_xxsmall_width", "pixels maximum xxs", "number", "col-xxs-12 col-sm-6 col-md-3");
		?>


	</div>
	<div class="col-xxs-12 pl-Shortcodes bii_option hidden">

		<div class="col-xxs-12">
			<h3>Base</h3>
			<table>
				<?php do_action("bii_base_shortcodes"); ?>						
			</table>
		</div>
		<div class="col-xxs-12">
			<h3>Ignition Desk</h3>
			<table>
				<?php do_action("bii_specific_shortcodes"); ?>						
			</table>

		</div>
	</div>
	<?php
}, 1);

add_action("bii_options_submit", function() {
	$tableaucheck = ["bii_medium_width", "bii_small_width", "bii_xsmall_width", "bii_xxsmall_width"];
	foreach ($tableaucheck as $itemtocheck) {
		if (isset($_POST[$itemtocheck])) {
			update_option($itemtocheck, $_POST[$itemtocheck]);
		}
	}
}, 5);

if (!function_exists("debugEcho")) {

	function debugEcho($string) {
		if ($_SERVER["REMOTE_ADDR"] == "77.154.194.84") {
			echo $string;
		}
	}

	function pre($item, $color = "#000") {
		if ($_SERVER["REMOTE_ADDR"] == "77.154.194.84") {
			echo "<pre style='color:$color'>";
			var_dump($item);
			echo "</pre>";
		}
	}

	function consoleLog($string) {
		if ($_SERVER["REMOTE_ADDR"] == "77.154.194.84") {
			$string = addslashes($string);
			?><script>console.log('<?php echo $string; ?>');</script><?php
		}
	}

	function consoleDump($var) {
		if ($_SERVER["REMOTE_ADDR"] == "77.154.194.84") {
//	ob_start();
//	var_dump($var);
//	$string = ob_get_contents();
//	ob_end_clean();
			?><script>console.log('<?php serialize($var); ?>');</script><?php
		}
	}

	function logQueryVars($afficherNull = false) {
		global $wp_query;
		foreach ($wp_query->query_vars as $key => $item) {
			if (!is_array($item)) {
				$$key = urldecode($item);
				if ($afficherNull) {
					consoleLog("$key => $item");
				} else {
					if ($item != "") {
						consoleLog("$key => $item");
					}
				}
			}
		}
	}

	function logRequestVars() {
		foreach ($_REQUEST as $key => $item) {
			if (!is_array($item)) {
				$$key = urldecode($item);
				consoleLog("$key => $item");
			}
		}
	}

	function logSESSIONVars() {
		foreach ($_SESSION as $key => $item) {
			if (!is_array($item)) {
				$$key = urldecode($item);
				pre("$key => $item");
			}
		}
	}

	function logGETVars() {
		foreach ($_GET as $key => $item) {
			if (!is_array($item)) {
				$$key = urldecode($item);
				consoleLog("$key => $item");
			} else {
				$log = "$key => {";
				foreach ($item as $key2 => $val) {
					$log .= " $key2=>$val";
				}
				$log .= "}";
				consoleLog($log);
			}
		}
	}

	function headersOK($url) {
		error_log("URL : " . $url);
		$return = false;
		$headers = @get_headers($url, 1);

		error_log("HEADER : " . print_r($headers, true));
		if ($headers[0] == 'HTTP/1.1 200 OK') {
			$return = true;
		}

		return $return;
	}

	function isHTTP($url) {
		$return = false;
		if (substr($url, 0, 7) == 'http://' || substr($url, 0, 8) == 'https://') {
			$return = true;
		}
		return $return;
	}

	function startVoyelle($string) {
		$voyelle = false;
		$string = strtolower(remove_accents($string));
		$array_voyelles = array("a", "e", "i", "o", "u");
		if (in_array($string[0], $array_voyelles)) {
			$voyelle = true;
		}
		return $voyelle;
	}

	function stripAccents($string) {
		$string = htmlentities($string, ENT_NOQUOTES, 'utf-8');
		$string = preg_replace('#\&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring)\;#', '\1', $string);
		$string = preg_replace('#\&([A-za-z]{2})(?:lig)\;#', '\1', $string);
		$string = preg_replace('#\&[^;]+\;#', '', $string);
		return $string;
	}

	function stripAccentsLiens($string) {
		$string = mb_strtolower($string, 'UTF-8');
		$string = stripAccents($string);

		$search = array('@[ ]@i', '@[\']@i', '@[^a-zA-Z0-9_-]@');
		$replace = array('-', '-', '');

		$string = preg_replace($search, $replace, $string);
		$string = str_replace('--', '-', $string);
		$string = str_replace('--', '-', $string);

		return $string;
	}

	function stripAccentsToMaj($string) {
		$string = stripAccentsLiens($string);
		$string = str_replace('-', ' ', $string);
		$string = strtoupper($string);
		return $string;
	}

	function url_exists($url) {
		$file_headers = @get_headers($url);
		if ($file_headers[0] == 'HTTP/1.1 404 Not Found') {
			$exists = false;
		} else {
			$exists = true;
		}
		return $exists;
	}

	function bii_write_log($log) {
		if (WP_DEBUG_LOG) {
			if (is_array($log) || is_object($log)) {
				error_log(print_r($log, true));
			} else {
				error_log($log);
			}
		}
	}

	function bii_makebutton($option, $pluriel = false, $feminin = false, $invert = false) {
		$array_switch = ["désactivé", "activé"];
//	$array_switch = ["désactivé", "activé"];
		$gotoval = 1;
		$value = get_option($option);
		if ($value == 1) {
			$gotoval = 0;
			$facheck = "fa-check-square-o";
		}
		if ($invert) {
			if ($value == 1) {
				$value = 0;
			} else {
				$value = 1;
			}
		}
		$facheck = "fa-square-o";
		if ($value == 1) {
			$facheck = "fa-check-square-o";
		}

		$valtexte = $array_switch[$value];
		if ($feminin) {
			$valtexte.="e";
		}
		if ($pluriel) {
			$valtexte.="s";
		}
		$button = "<button data-newval='$gotoval' data-option='$option' class='bii_upval btn btn-info'><i class='fa $facheck'></i> $valtexte</button>";
		return $button;
	}

	function bii_makeinput($option, $type = "text", $class = "", $options = [], $echo = true) {
		$value = stripcslashes(get_option($option));
		$class .= " form-control";
		if ($type == "textarea") {
			$return = "<textarea class='$class' id='$option' name='$option'>$value</textarea>";
		} else if ($type == "select") {
			$return = "<select class='$class' id='$option' name='$option'>";
			foreach ($options as $optid => $name) {
				$selected = "";
				if ($optid == $value) {
					$selected = "selected='selected'";
				}
				$return.= "<option value='$optid'>$name</option>";
			}
			$return .= "</select>";
		} else if ($type == "wpeditor") {
			$echo = false;
			$return = "";
			wp_editor($value, $value);
		} elseif ($type == "image") {
			$return = "";
			$return .= "<div class='form-inline'>"
				. "<div class='previsualisation'>

						<img id='image-preview' width='100' height='100' src='$value' alt='image' />

					</div>"
				. "<label for='$option'>" . __('Photo 1') . "</label><br />"
				. "<div class='item $class form-group'>"
				. "<input id='$option' type='text' name='$option' class='form-control' value='$value' />"
				. "<input id='upload_$option' class='input-upload $option form-control'  type='button' value='Parcourir' />"
				. "</div>"
				. "</div>"
				. "<div class='spacer'></div>"
				. "<script>"
				. "jQuery('#upload_$option').click(function(e) {
						var custom_uploader;
						e.preventDefault();
						if (custom_uploader) {
							custom_uploader.open();
							return;
						}
						custom_uploader = wp.media.frames.file_frame = wp.media({
							title: 'Choose Image',
							button: {
								text: 'Choose Image'
							},
							multiple: false
						});
						custom_uploader.on('select', function () {
							attachment = custom_uploader.state().get('selection').first().toJSON();
							jQuery('#$option').val(attachment.url);
							jQuery('#$option').trigger('keyup');
						});
						custom_uploader.open();"
				. "});jQuery('#$option').on('keyup', function () {
						console.log('keyup');
						var src = jQuery(this).val();
						var image = \"<img id='image-preview' width='100' height='100' src='\" + src + \"' alt='image' />\";
						jQuery('.previsualisation').html(image);
						jQuery('#image-preview').error(function () {
							jQuery(this).attr({
								'src': '$value'
							});
						});
					});"
				. "</script>";
		} else {
			$return = "<input type='$type' class='$class' id='$option' name='$option' value='$value' />";
		}
		if ($echo) {
			echo $return;
		}
		return $return;
	}

	function bii_makestuffbox($option, $name, $type = "text", $class_stuffbox = "", $options = [], $class_input = "") {
		if (!$class_stuffbox || $type == "wpeditor") {
			$class_stuffbox = "col-xxs-12";
		}
		?>
		<div id="<?= $option ?>_div" class="stuffbox <?= $class_stuffbox; ?> ">
			<h3><label for="<?= $option ?>"><?= $name ?></label></h3>
			<div class="inside">
				<?php bii_makeinput($option, $type, $class_input, $options); ?>
			</div>
		</div>
		<?php
	}

}