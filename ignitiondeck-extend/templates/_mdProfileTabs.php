<?php
global $permalink_structure;
global $current_user;
if ($current_user->ID) {
	if (empty($permalink_structure)) {
		$prefix = '&amp;';
	} else {
		$prefix = '?';
	}
	if (is_user_logged_in() && !isset($current_user)) {
		global $current_user;
		get_currentuserinfo();
	}
	if (isset($current_user)) {
		$orders = ID_Member_Order::get_orders_by_user($current_user->ID);
		$orders = array_reverse($orders);
	}
	$durl = md_get_durl();

	if (!class_exists('Helix')) {
		$user_ID = get_current_user_id();
		$moncompte = get_bloginfo("url") . "/mon-compte/";
		$linknewprojet = get_bloginfo("url") . "/preinscription/";
		$linkprojets = get_bloginfo("url") . "/mes-projets/";
		$linkprojetsm = get_bloginfo("url") . "/modifier-un-projet/";
		$textprojet = "Nouveau projet";
		$active_profile = $active_project = $active_new = "";

		$currenturl = get_permalink();

		if ($currenturl == $linknewprojet) {
			$active_new = "active";
		}
		if ($currenturl == $moncompte) {
			$active_profile = "active";
		}
		if ($currenturl == $linkprojets) {
			$active_project = "active";
		}
		if ($currenturl == $linkprojetsm) {
			$active_new = "active";
			$textprojet = "Modifier un projet";
		}
		?>
		<ul class="dashboardmenu dashboardmenu-prequel">
			<li class='<?= $active_profile; ?>'><a href="<?= $moncompte; ?>">Mon compte</a></li>
			<li class='<?= $active_project; ?>'><a href="<?= $linkprojets; ?>">Mes projets</a></li>
			<li class='<?= $active_new; ?>'><a href="<?= $linknewprojet; ?>"><?= $textprojet; ?></a></li>
		</ul>
		<?php
	}
}?>