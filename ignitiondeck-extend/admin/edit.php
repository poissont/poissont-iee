<?php
if (!isset($nom_classe)) {
	$nom_classe = "produit";
}

ini_set('display_errors', '1');
if ($nom_classe::editable()) {

	$id = 0;
	if (isset($_REQUEST["id_edit"])) {
		$id = $_REQUEST["id_edit"];
	}
	$instruction = "";
	if (isset($_REQUEST["instruction"])) {
		$instruction = $_REQUEST["instruction"];
	}
	$action_bouton = "Publier";

	if ($id != 0) {
		$action_bouton = "Modifier";
	}
	$action_bouton2 = $action_bouton . " et rester";
	$item = new $nom_classe($id);
	if (isset($_GET['edit'])) {
		if (isset($_POST['id'])) {
			$id_post = $_POST['id'];
			$stay = $_POST['publishandstay'];
			unset($_POST['id']);
			unset($_POST['publishandstay']);
			$item = new $nom_classe($id_post);
			if ($id_post == 0) {
				$item->insert();
			}
			if (method_exists($item, "maxOrdre")) {
				$_POST["ordre"] = $nom_classe::maxOrdre();
			}
//			var_dump($_POST);
			$item->updateChamps($_POST);

			if ($stay) {
				?><script>document.location.href = "<?php echo $nom_classe::redirectStay($id_post); ?>";</script><?php
			} else {
				?><script>document.location.href = "<?php echo $nom_classe::redirectEdit(); ?>";</script><?php
			}
		} else {
			?><script>document.location.href = "<?php echo $nom_classe::redirectError(); ?>";</script><?php
		}
	}
	?>
	<div class="message"><?php
		if (isset($_SESSION["bii_message"])) {
			echo $_SESSION["bii_message"];
		}
		?></div>
	<div class="wrap custom-edit">


		<h2><?= $nom_classe::iconFa(); ?><?= $item->titreEdit(); ?>
			<button class="publier-rester btn btn-info" ><span class="fa fa-save"></span> <?php echo $action_bouton2; ?></button>
			<button class="publier btn btn-success" accesskey="p" tabindex="5" ><span class="fa fa-save"></span> <?php echo $action_bouton; ?></button>

		</h2> 

		<?php if (isset($_GET["erreur_edit"])) { ?>
			<div class="updated below-h2 warning" id="message" style="display:none;">
				<p>La fiche n'a pas pu être éditée, veuillez réésayer</p>
			</div>
		<?php } ?>

		<div id="poststuff" class="metabox-holder has-right-sidebar poststuff-bii">
			<div id="post-body ">
				<div id="post-body-content ">
					<form method="post" class="col-lg-10 col-md-10 col-sm-12 col-xs-12" id="edit-item" action="admin.php?page=<?php echo $nom_classe; ?>_edit&edit=1">

						<input type="hidden" id="id" name="id" value="<?php echo $id; ?>" />
						<input type="hidden" id="publishandstay" name="publishandstay" value="0" />

						<?php $item->form_edit(); ?>
					</form>


				</div>
			</div>

			<div id="side-info-column" class="inner-sidebar col-lg-2 col-md-2 col-sm-12 hidden-xs">
				<div id="side-sortables" class="meta-box-sortables ui-sortable">
					<div id="linksubmitdiv" class="postbox">
						<div class="inside">
							<button id="publishandstay" class="publier-rester btn btn-info"  ><span class="fa fa-save"></span> <?php echo $action_bouton2; ?></button>

							<button id="publish" class="publier btn btn-success" accesskey="p" tabindex="5" ><span class="fa fa-save"></span> <?php echo $action_bouton; ?></button>

						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<button class="publier-rester btn btn-info" ><span class="fa fa-save"></span> <?php echo $action_bouton2; ?></button>
			<button class="publier btn btn-success" accesskey="p" tabindex="5" ><span class="fa fa-save"></span> <?php echo $action_bouton; ?></button>

		</div>

		<div class="clearfix"></div>


		<script>
			jQuery(function () {

				jQuery("#upload").hide();

				if (jQuery('.datepicker').length) {
					jQuery(".datepicker").datepicker({
						firstDay: 1,
						closeText: 'Fermer',
						prevText: '',
						nextText: '',
						currentText: 'Aujourd\'hui',
						monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
						monthNamesShort: ['Janv.', 'Févr.', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'],
						dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
						dayNamesShort: ['Dim.', 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.'],
						dayNamesMin: ['Dim.', 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.'],
						dateFormat: 'dd/mm/yy',
						defaultDate: new Date(),
						beforeShow: function () {
							jQuery('#ui-datepicker-div').addClass("bii-datepicker");
						}
					});

				}

				jQuery(".stuffbox h3 button.enroule, .stuffbox h3 button.deroule").on("click", function (e) {
					e.preventDefault();
					if (jQuery(this).hasClass("enroule")) {
						jQuery(this).find(".fa").addClass("fa-plus-square").removeClass("fa-minus-square");
						jQuery(this).addClass("deroule").removeClass("enroule");
						jQuery(this).parents(".stuffbox").find(".inside").addClass("hidden");
					} else if (jQuery(this).hasClass("deroule")) {
						jQuery(this).find(".fa").addClass("fa-minus-square").removeClass("fa-plus-square");
						jQuery(this).addClass("enroule").removeClass("deroule");
						jQuery(this).parents(".stuffbox").find(".inside").removeClass("hidden");
					}
				});
				jQuery(".cbx").on("click", function () {
					var id = jQuery(this).attr("id");
					id = id.substring(4);
					//					alert(id);
					var checked = jQuery(this).is(":checked");
					var value = 0;
					if (checked == true) {
						value = 1;
					}
					jQuery("#" + id).val(value);
				});

				jQuery('#poststuff').on('click', '.cbx-data-change', function () {
					var dc = jQuery(this).attr('data-change');
					var valeur = 0;
					if (jQuery(this).is(":checked")) {
						valeur = 1;
					}
					console.log(valeur);
					jQuery('#' + dc).val(valeur);
				});

				jQuery(".add-image").on("click", function () {
					jQuery("#upload").show();
					jQuery(".add-image").hide();
				});
				jQuery(".publier").on("click", function () {
					jQuery("#edit-item").submit();
				});
				jQuery(".publier-rester").on("click", function () {
					jQuery("#publishandstay").val(1);
					jQuery("#edit-item").submit();
				});


	//				jQuery(window).bind('keydown', function (event) {
	//					if (event.ctrlKey || event.metaKey) {
	//						switch (String.fromCharCode(event.which).toLowerCase()) {
	//							case 's':
	//								event.preventDefault();
	//								alert('ctrl-s');
	//								break;
	//							
	//						}
	//					}
	//				});
			});

	<?php $nom_classe::admin_additional_script(); ?>


		</script>
	</div>
	<?php
}