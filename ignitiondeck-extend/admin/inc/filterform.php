<?php
$action = "";
if (isset($nom_classe)) {
	$action = "action='" . $_SERVER['REQUEST_URI'] . "'";
	if ($nom_classe::isWordpress()) {
		$action = "action='admin.php?page=vazard_liste_$nom_classe'";
	}
}
$arrayRempl = autoRemplissageFilter();
//var_dump($arrayRempl);
$nbinstr = count($arrayRempl);
$operators = array(
	array("value" => "LIKE", "text" => "contient", "class" => "string"),
	array("value" => "BEGINWITH", "text" => "commence par", "class" => "string"),
	array("value" => "ENDWITH", "text" => "finit par", "class" => "string"),
	array("value" => "EQ", "text" => "=", "class" => "all"),
	array("value" => "NOT", "text" => "≠", "class" => "all"),
	array("value" => "LT", "text" => "&lt;", "class" => "math"),
	array("value" => "GT", "text" => "&gt;", "class" => "math"),
	)
?>

<div id="likeaform" class=" col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<p>Filtrer sur</p>
	<div id='lines'>
		<input type="hidden" id="countline" value='<?php echo $nbinstr-1; ?>' />
		<div id='firstline'>			
			<?php lineFilter($nom_classe, $operators, 0, $arrayRempl); ?>
		</div>
	</div>
	<?php 
	$i = 0;
	foreach($arrayRempl as $item){
		if($i > 0){
			lineFilter($nom_classe, $operators, $i, $arrayRempl);
		}
		
		$i++;
	}
	?>
</div>
<form method="get" <?php echo $action; ?> id="formfilter" class=" col-lg-12 col-md-12 col-sm-12 col-xs-12">

	<?php
	if (isset($nom_classe)) {
		if ($nom_classe::isWordpress()) {
			?><input type="hidden" name="page" value='<?php echo "$nom_classe"."_list"; ?>'><?php
		} else {
			?><input type="hidden" name="class" value='<?php echo $nom_classe; ?>'><?php
		}
		?>
	<?php } ?>

	<input type="hidden" name="filter" id="filter">
</form>
<script>
	jQuery(function () {
		jQuery(".string").show();
		jQuery(".math").hide();


		jQuery("#likeaform").on("change", ".champ", function () {
			var id = jQuery(this).attr("id");
			id = id.substring(6);

			jQuery("#line-" + id + " .champ option").each(function () {
				if (jQuery(this).is(":selected")) {
					jQuery("#line-" + id + " .valuefilter").removeClass("datepicker");
					if (jQuery(this).hasClass("nb") || jQuery(this).hasClass("date")) {
						jQuery("#line-" + id + " .operator .string").hide();
						jQuery("#line-" + id + " .operator .math").show();

					}
					if (jQuery(this).hasClass("text")) {
						jQuery("#line-" + id + " .operator .string").show();
						jQuery("#line-" + id + " .operator .math").hide();
					}
					if (jQuery(this).hasClass("date")) {
						jQuery("#line-" + id + " .valuefilter").addClass("datepicker");
						jQuery("#line-" + id + " .datepicker").each(function () {
							jQuery(this).datepicker({
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
								dateFormat: 'dd/mm/yy'
							});
						});
					}
				}
			});
		});

		jQuery("#likeaform").on("keyup", ".valuefilter", function () {
			filtervalue();
		});
		jQuery("#likeaform").on("change", ".valuefilter", function () {
			filtervalue();
		});


		jQuery("#valform").click(function () {
			filtervalue();
			jQuery("#formfilter").submit();
		});

		function filtervalue() {
			var countline = jQuery("#countline").val();
			var val = "";
			for (var i = 0; i <= countline; i++) {
				if (i > 0) {
					val += "$AND$";
				}
				var champ = jQuery("#champ-" + i).val();
				var operator = jQuery("#operator-" + i).val();
				var valuefilter = jQuery("#valuefilter-" + i).val();
				if (jQuery("#valuefilter-" + i).hasClass("datepicker")) {
					var from = valuefilter.split("/");
					var f = new Date(from[2] * 1, (from[1] * 1) - 1, from[0] * 1);
					console.log(f);
					valuefilter = f.getTime() / 1000 + 2 * 3600;
				}
				val += champ + "$" + operator + "$" + valuefilter;

			}


			jQuery("#filter").val(val);
		}


		jQuery("#likeaform").on("click", ".add", function () {
			var countline = jQuery("#countline").val();
			countline++;

			var input = jQuery("#firstline").html();
			input = input.replace('line-0', 'line-' + countline);
			input = input.replace('champ-0', 'champ-' + countline);
			input = input.replace('operator-0', 'operator-' + countline);
			input = input.replace('valuefilter-0', 'valuefilter-' + countline);
			input = input.replace('addform', 'removeform-' + countline);
			input = input.replace('fa-plus', 'fa-minus');
			input = input.replace('primary', 'warning');
			input = input.replace('add', 'rem');
			input = input.replace('datepicker', '');
			input = input.replace('hasDatepicker', '');
			input = input.replace('selected="selected"', '');
			input = input.replace('selected="selected"', '');
			input = input.replace('<button class="btn btn-success" id="valform">OK</button>', '');
			input = input.replace(/value=('?"?)([a-zA-z0-9]+[/]*)*('?"?)/g, '');
			input = input.replace(/data-oldval=/g, 'value=');

			jQuery(input).insertAfter("#firstline");
			jQuery("#countline").val(countline);
			jQuery("#line-" + countline + " .operator .string").show();
			jQuery("#line-" + countline + " .operator .math").hide();
		});

		jQuery("#likeaform").on("click", ".rem", function () {
			var id = jQuery(this).attr("id");
			id = id.substring(11);

			jQuery("#line-" + id).remove();
			var countline = jQuery("#countline").val();
			jQuery("#countline").val(countline-1);
		});
	});
</script>

<?php

function lineFilter($nom_classe, $operators, $id = 0, $arrayRempl = array()) {
	?>
	<div id='line-<?php echo $id; ?>' class='line'>

		<div class=" col-lg-4 col-md-4 col-sm-4  col-xs-12">
			<select  id="champ-<?php echo $id; ?>" class="form-control champ">
				<?php $nom_classe::filters_form_arguments($arrayRempl[$id]); ?>

			</select>
		</div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
			<select id="operator-<?php echo $id; ?>" class="form-control operator">
				<?php
				foreach ($operators as $item) {
					$class = $item["class"];
					$value = $item["value"];
					$text = $item["text"];
					$selected = "";
					if (isset($arrayRempl[$id]["operator"]) && $arrayRempl[$id]["operator"] == $value) {
						$selected = "selected='selected'";
					}
					echo "<option class='$class' value='$value' data-oldval='$value' id='$value' $selected>$text</option>";
				}
				?>
			</select>
		</div>
		<div class=" col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<?php
			$value = "";
			if (isset($arrayRempl[$id]["value_filter"])) {
				$value = $arrayRempl[$id]["value_filter"];
			}
			?>
			<input type="text" id="valuefilter-<?php echo $id; ?>" class=" form-control valuefilter" value="<?php echo $value; ?>" />
		</div>
		<div class=" col-lg-2 col-md-2 col-sm-2 col-xs-12">
			<?php if($id == 0){ ?>
			<button class="btn btn-primary add" id="addform"><span class='fa fa-plus'></span></button>
			<button class="btn btn-success" id="valform">OK</button>
			<?php } else { ?>
			<button class="btn btn-warning rem" id="removeform-<?php echo $id; ?>"><span class='fa fa-minus'></span></button>
			<?php } ?>
		</div>
	</div>
	<?php
}
