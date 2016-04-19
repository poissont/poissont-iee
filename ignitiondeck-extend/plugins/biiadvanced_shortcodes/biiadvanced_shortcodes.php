<?php

/*
  Plugin Name: Bii advanced shortcodes
  Description: Ajoute des shortcodes avancés
  Version: 1.0
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */
define('biiadvanced_shortcodes', '1.0');

function bii_SC_displaywhenrequest($atts, $content = null) {
	$display = true;
	foreach ($atts as $attr => $value) {
		$display = false;
		if (isset($_REQUEST[$attr]) && ($_REQUEST[$attr] == $value || $value == "all")) {
			$display = true;
		}
	}
	$return = "";
	if ($display) {
		$return = do_shortcode($content);
	}
	return $return;
}

function bii_SC_notdisplaywhenrequest($atts, $content = null) {
	foreach ($atts as $attr => $value) {
		$display = true;
		if (isset($_REQUEST[$attr]) && ($_REQUEST[$attr] == $value || $value == "all")) {
			$display = false;
		}
	}
	$return = "";
	if ($display) {
		$return = do_shortcode($content);
	}
	return $return;
}

add_shortcode('bii_displaywhenrequest', 'bii_SC_displaywhenrequest');
add_shortcode('bii_notdisplaywhenrequest', 'bii_SC_notdisplaywhenrequest');

add_action("bii_base_shortcodes", function() {
	?>
	<tr>
		<td><strong>[bii_displaywhenrequest cle="valeur"] contenu [/bii_displaywhenrequest]</strong></td>
		<td>Affiche contenu lorsque cle est égal à valeur (si valeur est égal à "all", alors contenu est affiché si cle existe)</td>
	</tr>
	<tr>
		<td><strong>[bii_notdisplaywhenrequest cle="valeur"] contenu [/bii_notdisplaywhenrequest]</strong></td>
		<td>Affiche contenu <strong>sauf</strong> lorsque cle est égal à valeur (si valeur est égal à "all", alors contenu n'est pas affiché si cle existe)</td>
	</tr>
	<?php

}, 1);
