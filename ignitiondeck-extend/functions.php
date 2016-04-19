<?php

function divisionbootstrap($nb) {
	if ($nb == 0) {
		return "hidden";
	}
	if (strpos($nb, "/")) {
		$expl = explode("/", $nb);
		$nb = $expl[0] / $expl[1];
	}
	$r = (int) 12 / $nb;
	return $r;
}

function bootstrap_builder($nb_cols_md = 3, $nb_cols_sm = 2, $nb_cols_xs = 1, $nb_cols_xxs = 1, $nb_cols_lg = 4) {
	$douziemes = [
//			"xxs"=>divisionbootstrap($nb_cols_xxs),
		"xs" => divisionbootstrap($nb_cols_xs),
		"sm" => divisionbootstrap($nb_cols_sm),
		"md" => divisionbootstrap($nb_cols_md),
		"lg" => divisionbootstrap($nb_cols_lg),
	];
	$class = "";
	$nbprec = 0;
	foreach ($douziemes as $screen => $nb) {
		if ($nb == "hidden") {
			$class .= "vc_$nb-$screen";
		} elseif ($nb != $nbprec) {
			$class .= " vc_col-$screen-$nb";
		}
		$nbprec = $nb;
	}
	bii_write_log($class);
	return $class;
}

function select_recherche_chiffre($id){
	$value = "";
	if(isset($_REQUEST[$id])){
		$value = $_REQUEST[$id];
	}
}

function getProjects() {
//	pre(get_current_user_id());
	return posts::all_id("post_type = 'ignition_product' AND post_author='".get_current_user_id()."' AND post_status NOT IN('trash')");
}
function getProjectsPublish() {
//	pre(get_current_user_id());
	return posts::all_id("post_type = 'ignition_product' AND post_author='".get_current_user_id()."' AND post_status IN('publish')");
}
function getProjectsPending() {
//	pre(get_current_user_id());
	return posts::all_id("post_type = 'ignition_product' AND post_author='".get_current_user_id()."' AND post_status IN('draft','pending')");
}
function getProjectsTrash() {
//	pre(get_current_user_id());
	return posts::all_id("post_type = 'ignition_product' AND post_author='".get_current_user_id()."' AND post_status IN('trash')");
}

function getFundsRaised() {
	$projets = getProjectsPublish();
	$funds = 0;
	foreach ($projets as $id) {
		$funds += get_post_meta($id, "ign_fund_raised")[0] * 1;
	}
	return $funds;
}

function getFundsGoal(){
	$projets = getProjects();
	$funds = 0;
	foreach ($projets as $id) {
		$funds += get_post_meta($id, "ign_fund_goal")[0] * 1;
	}
	return $funds;
}

function bii_cvnbst($nombre) {
	$nb1 = Array('un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf');

	$nb2 = Array('vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante', 'quatre-vingt', 'quatre-vingt');

	# Décomposition du chiffre
	# Séparation du nombre entier et des décimales
	if (preg_match("/\b,\b/i", $nombre)) {
		$nombre = explode(',', $nombre);
	} else {
		$nombre = explode('.', $nombre);
	}
	$nmb = $nombre[0];

	# Décomposition du nombre entier par tranche de 3 nombre (centaine, dizaine, unitaire)
	$i = 0;
	while (strlen($nmb) > 0) {
		$nbtmp[$i] = substr($nmb, -3);
		if (strlen($nmb) > 3) {
			$nmb = substr($nmb, 0, strlen($nmb) - 3);
		} else {
			$nmb = '';
		}
		$i++;
	}
	$nblet = '';
	## Taitement du côté entier
	for ($i = 1; $i >= 0; $i--) {
		if (strlen(trim($nbtmp[$i])) == 3) {
			$ntmp = substr($nbtmp[$i], 1);

			if (substr($nbtmp[$i], 0, 1) <> 1 && substr($nbtmp[$i], 0, 1) <> 0) {
				$nblet.=$nb1[substr($nbtmp[$i], 0, 1) - 1];
				if ($ntmp <> 0) {
					$nblet.=' cent ';
				} else {
					$nblet.=' cents ';
				}
			} elseif (substr($nbtmp[$i], 0, 1) <> 0) {
				$nblet.='cent ';
			}
		} else {
			$ntmp = $nbtmp[$i];
		}

		if ($ntmp > 0 && $ntmp < 20) {
			if (!($i == 1 && $nbtmp[$i] == 1)) {
				$nblet.=$nb1[$ntmp - 1] . ' ';
			}
		}

		if ($ntmp >= 20 && $ntmp < 60) {
			switch (substr($ntmp, 1, 1)) {
				case 1 : $sep = ' et ';
					break;
				case 0 : $sep = '';
					break;
				default: $sep = '-';
			}
			$nblet.=$nb2[substr($ntmp, 0, 1) - 2] . $sep . $nb1[substr($ntmp, 1, 1) - 1] . ' ';
		}

		if ($ntmp >= 60 && $ntmp < 80) {
			$nblet.=$nb2[4];
			switch (substr($ntmp, 1, 1)) {
				case 1 : $sep = ' et ';
					break;
				case 0 : $sep = '';
					break;
				default: $sep = '-';
			}

			if (substr($ntmp, 0, 1) <> 7) {
				$nblet.=$sep . $nb1[substr($ntmp, 1, 1) - 1] . ' ';
			} else {
				if (substr($ntmp, 1, 1) + 9 == 9)
					$sep = '-';
				$nblet.=$sep . $nb1[substr($ntmp, 1, 1) + 9] . ' ';
			}
		}

		if ($ntmp >= 80 && $ntmp < 100) {
			$nblet.=$nb2[6];
			switch (substr($ntmp, 1, 1)) {
				case 1 : $sep = ' et ';
					break;
				case 0 : $sep = '';
					break;
				default: $sep = '-';
			}

			//if(substr($ntmp,1,1)<>0){
			if (substr($ntmp, 0, 1) <> 9) {
				$nblet.=$sep . $nb1[substr($ntmp, 1, 1) - 1];
				if (substr($ntmp, 1, 1) == 0)
					$nblet.='s';
			}else {
				if (substr($ntmp, 1, 1) == 0)
					$sep = '-';
				$nblet.=$sep . $nb1[substr($ntmp, 1, 1) + 9];
			}
			$nblet.=' ';
			//}elseif(substr($ntmp,0,1)<>9){
			//    $nblet.='s ';
			//}else{
			//    $nblet.=' ';
			//}
		}

		if ($i == 1 && $nbtmp[$i] <> 0) {
			if ($nbtmp[$i] > 1) {
				$nblet.='milles ';
			} else {
				$nblet.='mille ';
			}
		}
	}

	if ($nombre[0] > 1)
		$nblet.='euros ';
	if ($nombre[0] == 1)
		$nblet.='euro ';

	## Traitement du côté décimale
	if ($nombre[0] > 0 && $nombre[1] > 0)
		$nblet.=' et ';
	$ntmp = substr($nombre[1], 0, 2);
	if (!empty($ntmp)) {
		if ($ntmp > 0 && $ntmp < 20) {
			$nblet.=$nb1[$ntmp - 1] . ' ';
		}

		if ($ntmp >= 20 && $ntmp < 60) {
			switch (substr($ntmp, 1, 1)) {
				case 1 : $sep = ' et ';
					break;
				case 0 : $sep = '';
					break;
				default: $sep = '-';
			}
			$nblet.=$nb2[substr($ntmp, 0, 1) - 2] . $sep . $nb1[substr($ntmp, 1, 1) - 1] . ' ';
		}

		if ($ntmp >= 60 && $ntmp < 80) {
			$nblet.=$nb2[4];
			switch (substr($ntmp, 1, 1)) {
				case 1 : $sep = ' et ';
					break;
				case 0 : $sep = '';
					break;
				default: $sep = '-';
			}

			if (substr($ntmp, 0, 1) <> 7) {
				$nblet.=$sep . $nb1[substr($ntmp, 1, 1) - 1] . ' ';
			} else {
				if (substr($ntmp, 1, 1) + 9 == 9)
					$sep = '-';
				$nblet.=$sep . $nb1[substr($ntmp, 1, 1) + 9] . ' ';
			}
		}

		if ($ntmp >= 80 && $ntmp < 100) {
			$nblet.=$nb2[6];
			switch (substr($ntmp, 1, 1)) {
				case 0 : $sep = '';
					break;
				default: $sep = '-';
			}

			if (substr($ntmp, 0, 1) <> 9) {
				$nblet.=$sep . $nb1[substr($ntmp, 1, 1) - 1];
				if (substr($ntmp, 1, 1) == 0)
					$nblet.='s';
			}else {
				if (substr($ntmp, 1, 1) == 0)
					$sep = '-';
				$nblet.=$sep . $nb1[substr($ntmp, 1, 1) + 9];
			}
			$nblet.=' ';
		}

		if ($ntmp <> 0 && !empty($ntmp)) {
			if ($ntmp > 1) {
				$nblet.='cents ';
			} else {
				$nblet.='cent ';
			}
		}
	}

	return $nblet;
}

