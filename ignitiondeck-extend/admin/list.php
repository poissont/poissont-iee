<?php
if (!isset($nom_classe)) {
	$nom_classe = "produit";
}
$contrat = "";
$filter = setFilter();
ini_set('display_errors', '1');

$where = $nom_classe::whereDefault() . $filter;
$groupBy = $nom_classe::groupByDefault();
if ($groupBy != "") {
	$where.= " GROUP BY $groupBy";
}

//echo $where;
$order = $nom_classe::identifiant();

if (isset($_REQUEST['orderby'])) {
	$order = $_REQUEST['orderby'];
	$ordre = "desc";

	if (isset($_REQUEST['ordre'])) {
		$ordre = $_REQUEST['ordre'];
	}
	foreach ($nom_classe::getListeProprietes() as $prop => $foo) {
		if ($order == $prop || $order == "p.$prop") {
			$where .= "ORDER BY $order $ordre";
		}
	}
}

$count = $nom_classe::nb($where);
$whereSC = $where;

if ($nom_classe::display_pagination()) {

	$lp = "admin.php?page=$nom_classe" . "_list";
	if (isset($_REQUEST["filter"])) {
		$lp.= "&filter=" . $_REQUEST["filter"];
	}
	if (isset($_REQUEST["orderby"])) {
		$lp.= "&orderby=" . $_REQUEST["orderby"];
	}
	if (isset($_REQUEST["ordre"])) {
		$lp.= "&ordre=" . $_REQUEST["ordre"];
	}
	$lien_pagination = $lp . "&pagination=£PAGE£";
	include("inc/pagination.php");

	$nbpage = ceil($count / $nbparPage);

	ob_start();
	pagination($lien_pagination, $nbpage, $page);
	$htmlpagination = ob_get_contents();
	ob_end_clean();
	$where.= $limit;
}


$showleg = false;
//echo $where;
$array = $nom_classe::all_id($where);


$nb_attr = $nom_classe::nbAttr();
?>
<div class="message"><?php
	if (isset($_SESSION["bii_message"])) {
		echo $_SESSION["bii_message"];
	}
	?></div>
<input type="hidden" id="kon" value="0" />
<div class="wrap">
	<h2><?= $nom_classe::iconFa(); ?><?= $nom_classe::titre_page_admin_liste(); ?>
		<?php
		if ($nom_classe::exportable()) {
			$date = time();
//			echo dirname(__FILE__);
			$fichier = "/web/clients/lhavrais/www.liguehavraise.fr/wp-content/plugins/bii_donations/downloads/$nom_classe-$date.csv";
			$WHERE_WRITECSV = $where;
			$nom_classe::writeCSV($fichier);
			?>
			<a class="btn btn-info" href="/wp-content/plugins/bii_donations/downloads/<?php echo "$nom_classe-$date"; ?>.csv"><span class="fa fa-file-excel-o"></span> Exporter une liste (.xls)</a>
		<?php } if ($nom_classe::editable()) { ?>
			<a class="btn btn-success" href="admin.php?page=<?php echo $nom_classe; ?>_edit"><span class="fa fa-plus"></span> <?php echo $nom_classe::messageNouveau(); ?></a>
		<?php } if ($nom_classe::rafraichissable()) { ?>
			<button class="btn btn-primary refresh-all"><span class="fa fa-refresh"></span> Tout rafraichir</button>  
		<?php } if ($nom_classe::display_all_short_code()) { ?>
			<button class="btn btn-info show-something" data-show="shortcodes"><span class="fa fa-eye"></span> Afficher tous les shortcodes</button>  
			<?php
		} if (method_exists($nom_classe, "legende")) {
			$showleg = true;
			?>
			<button class="btn btn-warning btn-toogle" data-toogle="legende"><span class="fa fa-plus-circle"></span> Légende</button>  
<?php } ?>
	</h2>
	<div class="updated below-h2" id="message" style="display:none;">
		<p>La fiche a bien été supprimée</p>
	</div>
		<?php if ($nom_classe::display_all_short_code()) { ?>
		<div class="shortcodes hidden">
			<?php
			if ($count > 0) {
				$arraySc = $nom_classe::all_id($whereSC);
				foreach ($arraySc as $id) {
					if ($id != 0) {
						$item = new $nom_classe($id);
						echo $item->shortcode();
					}
				}
				if ($nom_classe == "produit") {
					/* ?><br /><span><b>Shortcode pour l'afffichage des produits pour les aubaines</b></span><br /><?php
					  foreach ($arraySc as $id) {
					  if ($id != 0) {
					  $item = new $nom_classe($id);
					  echo $item->shortcodeSolde();
					  }
					  } */
					?><br /><span><b>Shortcode pour le lien du produit</b></span><br /><?php
					foreach ($arraySc as $id) {
						if ($id != 0) {
							$item = new $nom_classe($id);
							echo $item->shortcode_lien();
						}
					}
				}
			}
			?>
		</div>
		<?php
	}
	if ($showleg) {
		$nom_classe::legende();
	}
	if ($nom_classe::display_filter()) {
		include('inc/filterform.php');
	}
	if ($nom_classe::display_pagination()) {
		?><div class="pagination"><?php
		echo $htmlpagination;
		?></div><?php
}
	?>

	<form method="get" id="posts-filter">

		<div class="tablenav top">
			<div class="tablenav-pages one-page">
				<span class="displaying-num"><?php echo $count . ' élément' . (($count > 1) ? 's' : ''); ?></span>
			</div>
		</div>
		<table class="pi-list-table widefat fixed maisons">
			<thead>
				<tr>
<?php $nom_classe::tableHeaders(); ?>
				</tr>
			</thead>
			<tfoot>
				<tr>
<?php $nom_classe::tableHeaders(array(), array(), true); ?>
				</tr>

			</tfoot>
			<tbody id="the-list">
				<?php
				if ($count > 0) {
					$i = 1;
					foreach ($array as $id) {
						if ($id != 0) {
							$item = new $nom_classe($id);
							?>
							<tr <?php echo (($i % 2) ? 'class="alternate"' : ''); ?> id="card-<?php echo $id; ?>"><?php $item->ligneValeurs(); ?></tr>
							<?php
							$i++;
						}
					}
				} else {
					?>
					<tr class="no-items">
						<td colspan="<?php echo $nb_attr; ?>" class="colspanchange"><?php echo $nom_classe::messageRienAAfficher(); ?></td>
					</tr>
<?php } ?>
			</tbody>
		</table>
		<div class="tablenav bottom">
			<div class="tablenav-pages one-page">
				<span class="displaying-num"><?php echo $count . ' élément' . (($count > 1) ? 's' : ''); ?></span>
			</div>
		</div>
	</form>
	<script>
		jQuery(function () {
			jQuery(".pict").hide();
			jQuery(".legende").hide();

			jQuery("#likeaform").keypress(function (e) {
				if (e.which == 13) {
					jQuery("#valform").trigger("click");
				}
			});

			jQuery(".refresh-all").on("click", function () {
				jQuery(".ref").each(function () {
					jQuery(this).trigger("click");
				});
			});
			jQuery("#wpbody").on("click", ".show-something", function () {
				jQuery(this).removeClass("show-something").addClass("hide-something");
				var dc = jQuery(this).attr("data-show");
				jQuery("." + dc).removeClass("hidden");
			});
			jQuery("#wpbody").on("click", ".hide-something", function () {
				jQuery(this).removeClass("hide-something").addClass("show-something");
				var dc = jQuery(this).attr("data-show");
				jQuery("." + dc).addClass("hidden");
			});
			jQuery(".btn-toogle").on("click", function () {
				dt = jQuery(this).attr("data-toogle");
				jQuery(this).removeClass("btn-toogle").addClass("btn-untoogle");
				jQuery("." + dt).show();
			});
			jQuery(".btn-untoogle").on("click", function () {
				dt = jQuery(this).attr("data-toogle");
				jQuery(this).removeClass("btn-untoogle").addClass("btn-toogle");
				jQuery("." + dt).hide();
			});

			jQuery("#the-list").on("click", ".change-statut", function (event) {
				event.preventDefault();
				var parent = jQuery(this).parent(".statut");
				var value = 1;
				var id = jQuery(this).attr("data-id");
				parent.children(".btn").each(function () {
					jQuery(this).removeClass("btn-danger").removeClass("btn-warning").removeClass("btn-success");
					jQuery(this).addClass("btn-default");
					jQuery(this).children(".fa").remove();
				});
				jQuery(this).removeClass("btn-default");
				if (jQuery(this).hasClass("go-warning")) {
					jQuery(this).addClass("btn-warning");
				}
				if (jQuery(this).hasClass("go-success")) {
					jQuery(this).addClass("btn-success");
					value = 0;
				}
				if (jQuery(this).hasClass("go-danger")) {
					jQuery(this).addClass("btn-danger");
				}
				var html = jQuery(this).html();
				jQuery(this).html(html + " <i class='fa fa-check-square-o'></i>");
//				console.log(html);
				changeValue(id, "change_archive", value);
			});

			//Haut, haut, bas, bas, gauche, droite, gauche, droite, B, A
			var k = [38, 38, 40, 40, 37, 39, 37, 39, 66, 65],
				n = 0;
			jQuery(document).keydown(function (e) {
				if (e.keyCode === k[n++]) {
					if (n === k.length) {
						jQuery("#kon").val(1);
						jQuery(".ref").each(function () {
							jQuery(this).addClass("refold").removeClass("ref");
						});
						jQuery(".pict").show();
						jQuery(".showonkonami").show();
						alert("Actualisation du plus ancien au plus récent");
						n = 0;
						return false;
					}
				} else {
					n = 0;
				}
			});


			jQuery("#the-list").on("click", ".del", function () {
				var id = this.id;
				id = id.substring(7);
				del(id);
			});
			jQuery("#the-list").on("click", ".dup", function () {
				var id = this.id;
				id = id.substring(7);
				dupl(id);
			});
			jQuery("#the-list").on("click", ".pict", function () {
				var id = this.id;
				id = id.substring(8);
				pict(id);
			});
			jQuery("#the-list").on("click", ".ordre .btn", function (e) {
				e.preventDefault();
				var id = jQuery(this).attr("data-id");
				var filtre = jQuery(this).attr("data-filtre");
				var signe = "-";
				if (jQuery(this).hasClass("down")) {
					signe = "+";
				}
				var nomClasse = "<?php echo $nom_classe; ?>";
				ordonne(id, filtre, signe, nomClasse);
			});
			jQuery("#the-list").on("click", ".ordre-spe .btn", function (e) {
				e.preventDefault();
				var id = jQuery(this).attr("data-id");
				var cat = jQuery(this).attr("data-cat");
				var signe = "-";
				if (jQuery(this).hasClass("down")) {
					signe = "+";
				}
				var nomclasse = "sous_categorie_fiche";
				ordonneSpe(id, cat, signe, nomclasse);
			});


			function ordonne(id, filtre, signe, nomclasse) {

				jQuery.ajax({
					url: ajaxurl,
					data: {
						'action': 'vazard_ordre',
						'nom_classe': nomclasse,
						'id': id,
						'filtre': filtre,
						'signe': signe
					},
					dataType: 'html',
					success: function (reponse) {
						jQuery("#the-list").html(reponse);
					}
				});
			}
			function ordonneSpe(id, cat, signe, nomclasse) {

				jQuery.ajax({
					url: ajaxurl,
					data: {
						'action': 'vazard_ordre_spe',
						'nom_classe': nomclasse,
						'id': id,
						'filtre': cat,
						'signe': signe
					},
					dataType: 'html',
					success: function (reponse) {
						jQuery("#the-list").html(reponse);
					}
				});
			}

			function del(id_delete) {
				var nomClasse = "<?php echo $nom_classe; ?>";
				if (confirm("<?php echo $nom_classe::messageConfirmation(); ?>")) {
					jQuery.ajax({
						url: ajaxurl,
						data: {
							'action': 'bii_ajax_delete',
							'nom_classe': '<?php echo $nom_classe; ?>',
							'id_delete': id_delete
<?php
if (isset($page)) {
	echo ", 'pagination':$page";
}
?>
						},
						dataType: 'html',
						success: function (reponse) {
							jQuery("#the-list").html(reponse);
						}
					});
				}
			}
			function dupl(id_delete) {
				var nomClasse = "<?php echo $nom_classe; ?>";

				jQuery.ajax({
					url: ajaxurl,
					data: {
						'action': 'duplicate_interface_admin',
						'nom_classe': '<?php echo $nom_classe; ?>',
						'id_duplicate': id_delete
					},
					dataType: 'html',
					success: function (reponse) {
						jQuery("#the-list").html(reponse);
					}
				});

			}
			function changeValue(id, method, value) {
				var nomClasse = "<?php echo $nom_classe; ?>";
//				console.log(nomClasse + "(" + id + ")" + "::" + method + "(" + value + ")");
				jQuery.ajax({
					url: ajaxurl,
					data: {
						'action': "bii_change_value",
						'nom_classe': nomClasse,
						'method': method,
						'value': value,
						'id': id

					},
					dataType: 'html',
					success: function (reponse) {
						console.log(reponse);
					}

				});

			}
		});
	</script>
</div>