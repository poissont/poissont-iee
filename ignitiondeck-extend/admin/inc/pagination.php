<?php

$nbparPage = $nom_classe::pagination_limit();


if (!isset($_REQUEST['pagination'])) {
	$page = 1;
} else {
	$page = $_REQUEST['pagination'];
}

$limit = " LIMIT " . ($page-1) * $nbparPage  . ", " . $nbparPage;


function pagination($lien_page, $nb_pages, $page_actuelle = null, $style_affichage = null) {
	$placeholderdefault = '<span class="item hidden-xxs">...</span>';
	$placeholder = $placeholderdefault;
	if ($page_actuelle == null) {
		$page_actuelle = 1;
	}
	if ($nb_pages > 12) {
		$style_affichage = "limited";
	}

	if ($nb_pages > 1) {
		if ($style_affichage == "limited") {
			$liste_val = array();
			$liste_val[] = 1;
			$liste_val[] = 2;
			$liste_val[] = 3;
			$liste_val[] = $page_actuelle - 1;
			$liste_val[] = $page_actuelle;
			$liste_val[] = $page_actuelle + 1;
			$liste_val[] = $nb_pages - 2;
			$liste_val[] = $nb_pages - 1;
			$liste_val[] = $nb_pages;
		}
		for ($i = 1; $i <= $nb_pages; $i++) {
			$lien = str_replace("£PAGE£", $i, $lien_page);
			$class = "";
			if ($i == $page_actuelle) {
				$class = "current";
			}
			$val = $i;

			if ($style_affichage == "limited") {
				if (!in_array($i, $liste_val)) {
					$val = null;
				} 
			}

			if ($val != null) {
				?>
				<a class="item <?php echo $class; ?>" href="<?php echo $lien; ?>">
					<?php echo $i; ?>
				</a>
				<?php
			} else {
				echo $placeholder;
			}
			if ($style_affichage == "limited") {
				if (in_array($i, $liste_val)) {
					$placeholder = $placeholderdefault;
//					consoleLog("pl $i");
				}else{
					$placeholder = "";
				}
			}
		}
	}
}