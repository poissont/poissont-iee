<?php

class bii_project extends global_class {

	protected $id_post;
	protected $company_name;
	protected $company_logo;
	protected $company_location;
	protected $company_url;
	protected $company_fb;
	protected $company_twitter;
	protected $project_name;
	protected $project_goal;
	protected $project_category;
	protected $project_start;
	protected $project_end;
	protected $project_end_type;
	protected $project_short_description;
	protected $project_video;
	protected $project_long_description;
	protected $project_faq;
	protected $project_updates;
	protected $project_hero;
	protected $project_image2;
	protected $project_image3;
	protected $project_image4;
	protected $project_levels;

	static function fromIdPost($id) {
		$item = new static();
		$post = get_post($id);
		$post_meta = get_post_meta($id);
		$item->id_post = $id;

		foreach ($post_meta as $key => $val) {
			if (strpos($key, "ign_") !== false) {
				$key = str_replace("ign_", "", $key);
				$item->$key = $val[0];
			}
			$item->project_hero = wp_get_attachment_image_src(get_post_thumbnail_id($id))[0];
			$item->project_name = $post->post_title;
			$item->project_category = $item->getCategorie();
		}
		return $item;
	}

	static function header_form_front($text) {
		?>
		<div class="vc_col-xs-12">
			<h3><?= $text; ?></h3>
			<?php
		}

		static function footer_form_front() {
			?>
		</div>
		<?php
	}

	function prop_infos_entr() {
		$array = [
			"company_name" => ["value" => $this->company_name, "label" => "Nom de l'entreprise", "class_input" => "required"],
			"company_logo" => ["value" => $this->company_logo, "label" => "Votre logo", "input" => "file", "class" => "vc_col-xs-12 col-xs-12 vc_col-sm-5"],
			"company_location" => ["value" => $this->company_location, "label" => "Ville", "input" => "textarea", "class" => "vc_col-xs-12 col-xs-12 vc_col-sm-7","class_input" => "required"],
			"company_url" => ["value" => $this->company_url, "label" => "Url de l'entreprise"],
			"company_fb" => ["value" => $this->company_fb, "label" => "Profil facebook", "class" => "vc_col-xs-12 col-xs-12 vc_col-sm-6"],
			"company_twitter" => ["value" => $this->company_twitter, "label" => "Profil twitter", "class" => "vc_col-xs-12 col-xs-12 vc_col-sm-6"],
			"idcf_fes_wp_nonce" => ["value" => wp_create_nonce('idcf_fes_section_nonce'), "input" => "hidden"],
		];
		if (isset($this->id_post) && $this->id_post) {
			$array["project_post_id"] = ["value" => $this->id_post, "input" => "hidden"];
		}
		return $array;
	}

	function prop_infos() {

		$options = terms::array_slug_name("term_id in (select term_id from " . terms::prefix_bdd() . "term_taxonomy where taxonomy = 'project_category') order by name asc");
		$array = [
			"project_name" => ["value" => $this->project_name, "label" => "Titre de la campagne", "class" => "vc_col-xs-12 col-xs-12 vc_col-sm-8", "class_input" => "required"],
			"project_goal" => ["value" => $this->fund_goal, "label" => "Objectif Financier", "input" => "number", "step" => "any", "min" => 0, "class" => "vc_col-xs-12 col-xs-12 vc_col-sm-4", "class_input" => "required"],
			"project_category" => ["value" => $this->project_category, "label" => "Catégorie du projet", "input" => "select", "options" => $options,],
			"project_start" => ["value" => $this->start_date, "label" => "Date de début", "class" => "vc_col-xs-12 col-xs-12 vc_col-sm-6", "input" => "datepicker", "class_input" => "required"],
			"project_end" => ["value" => $this->fund_end, "label" => "Date de fin", "class" => "vc_col-xs-12 col-xs-12 vc_col-sm-6", "input" => "datepicker",],
			"project_end_type" => [
				"value" => $this->end_type,
				"label" => "Options de fin de campagne",
				"input" => "select",
				"options" => [
					"closed" => "Fermer quand le montant est atteint",
					"open" => "Laisser ouvert",
				],
			],
		];
		return $array;
	}

	function prop_detail() {
		$array = [

			"project_short_description" => ["value" => $this->project_description, "label" => "Description courte de votre projet", "input" => "textarea"],
			"project_long_description" => ["value" => $this->long_description, "label" => "Description longue de votre projet", "input" => "textarea"],
			"project_faq" => ["value" => $this->faqs, "label" => "FAQ du projet", "input" => "textarea", "class" => "vc_col-xs-12 col-xs-12 vc_col-sm-6"],
			"project_updates" => ["value" => $this->updates, "label" => "Mises à jour du projet", "input" => "textarea", "class" => "vc_col-xs-12 col-xs-12 vc_col-sm-6"],
		];
		return $array;
	}

	function prop_images() {
		$array = [

			"project_hero" => ["value" => $this->project_hero, "label" => "Image à la une", "input" => "file", "class" => "vc_col-xs-12 col-xs-12 vc_col-sm-6"],
			"project_image2" => ["value" => $this->product_image2, "label" => "Image 2", "input" => "file", "class" => "vc_col-xs-12 col-xs-12 vc_col-sm-6"],
			"project_image3" => ["value" => $this->product_image3, "label" => "Image 3", "input" => "file", "class" => "vc_col-xs-12 col-xs-12 vc_col-sm-6"],
			"project_image4" => ["value" => $this->product_image4, "label" => "Image 4", "input" => "file", "class" => "vc_col-xs-12 col-xs-12 vc_col-sm-6"],
			"project_video" => ["value" => $this->product_video, "label" => "Vidéo du projet"],
		];
		return $array;
	}

	function prop_levels() {
		return [];
	}

	function form_edit_front() {
		$this->form_edit_section("Vos Informations", "prop_infos_entr");
		$this->form_edit_section("Informations sur le projet", "prop_infos");
		$this->form_edit_section("Détails du projet", "prop_detail");
		$this->form_edit_section("Images du projet", "prop_images");
//		$this->form_edit_section("Niveaux de récompenses", "prop_levels");
		$this->inputCGU();
		$this->input_front("", ["input" => "submit"]);

//		pre($this);
	}

	function form_edit_section($titre, $method) {
		$infos = $this->$method();
		static::header_form_front($titre);
		foreach ($infos as $prop => $val) {
			$this->input_front($prop, $val);
		}
		static::footer_form_front();
	}

	function inputCGU() {
		?>
		<div class="vc_col-xs-12 col-xs-12">
			<h3>Valider le projet</h3>
			<div class="vc_col-xs-12 col-xs-12">
				<label for="CGU" class='vc_col-xs-9 col-xs-9'>J'accepte les conditions générales d'utilisation</label><input id='CGU' class="vc_col-xs-3 col-xs-3" type='checkbox' />
			</div>
		</div>
		<?php
	}

	function input_front($name, $val = []) {
		if (!isset($val["value"])) {
			if (property_exists($this, $name)) {
				$val["value"] = $this->$name;
			} else {
				$val["value"] = "";
			}
		}
		if (!isset($val["class"]) || !$val["class"]) {
			$val["class"] = "vc_col-xs-12 col-xs-12";
		}
		if (!isset($val["input"]) || !$val["input"]) {
			$val["input"] = "text";
		}
		if (!isset($val["label"]) || !$val["label"]) {
			$val["label"] = $name;
		}
		if (!isset($val["class_input"])) {
			$val["class_input"] = "";
		}
		$val["class_input"] .= " $name form-control";
		if ($val["input"] != "hidden") {
			$namelabel = $name;
			if ($val["input"] == "datepicker") {
				$namelabel.= "-dtp";
			}
			if (strpos($val["class_input"], "required") !== false) {
				$val["label"] .= " *";
			}
			?>
			<div class="<?= $val["class"]; ?>">
				<label for="<?= $namelabel ?>"><?= $val["label"]; ?></label>

				<?php
			}
			$otherfields = "";
			if ($val["input"] == "file") {
				$otherfields = 'accept="image/*"';
				if ($val["value"]) {
					?><div style="background-image: url('<?= $val["value"]; ?>');background-repeat: no-repeat; background-size: 150px auto;" class="project-thumb image"></div><?php
				}
			}
			if(isset($val["data-pattern"]) && $val["data-pattern"]){
				$otherfields.= " data-pattern='".$val["data-pattern"]."'";
			}
			
			if ($val["input"] == "number") {
				$otherfields = "min='" . $val["min"] . "' step='" . $val["step"] . "'";
			}
			if ($val["input"] == "textarea") {
				?><textarea id="<?= $name ?>" name="<?= $name ?>" class="<?= $val["class_input"] ?>" ><?= $val["value"] ?></textarea><?php
			} elseif ($val["input"] == "select") {
				?><select id="<?= $name ?>" name="<?= $name ?>" class="<?= $val["class_input"] ?>" ><?php
						foreach ($val["options"] as $value => $display_value) {
							$selected = "";
							if ($val["value"] == $value) {
								$selected = "selected='selected'";
							}
							?><option value="<?= $value ?>" <?= $selected ?>><?= utf8_encode($display_value) ?></option>
					<?php } ?></select><?php
			} elseif ($val["input"] == "submit") {
				?><input type="submit" value="Enregistrer votre projet !" class="project_fesubmit" name="project_fesubmit" id="project_fesubmit" /><?php
				} elseif ($val["input"] == "datepicker") {
					$valexp = explode("/", $val["value"]);
					$newval = $val["value"];
					if (isset($valexp[1])) {
						$newval = $valexp[1] . "/" . $valexp[0] . "/" . $valexp[2];
					}
					?><input type="hidden" id="<?= $name ?>"  name="<?= $name ?>"  value="<?= $val["value"] ?>" /><input type="text" id="<?= $name ?>-dtp"  name="<?= $name ?>-dtp"  value="<?= $newval ?>" class="<?= $val["class_input"] ?> datepicker" data-relative="<?= $name ?>" /><?php
			} else {
				?><input id="<?= $name ?>" type="<?= $val["input"] ?>" name="<?= $name ?>" class="<?= $val["class_input"] ?>" value="<?= $val["value"] ?>" <?= $otherfields; ?> /><?php
			}
			if ($val["description"]) {
				?><p class="<?= $name . "-description" ?>"><?= $val["description"] ?></p><?php
			}

			if ($val["input"] != "hidden") {
				?></div><?php
		}
	}

	function getCategorie() {
		$prefix = static::prefix_bdd();
		$identifiant = terms::identifiant();
		$tt = $prefix . "term_taxonomy";
		$tr = $prefix . "term_relationships";
		$where = "$identifiant in(select term_id from $tt tt inner join $tr tr on tr.term_taxonomy_id = tt.term_taxonomy_id where tr.object_id = $this->id_post and tt.taxonomy='project_category')";
		$list = terms::all_id($where);
		if (count($list)) {
			$item = new terms($list[0]);
			return $item->slug();
		} else {
			return 'autres';
		}
	}
	function getCategorieName() {
		$prefix = static::prefix_bdd();
		$identifiant = terms::identifiant();
		$tt = $prefix . "term_taxonomy";
		$tr = $prefix . "term_relationships";
		$where = "$identifiant in(select term_id from $tt tt inner join $tr tr on tr.term_taxonomy_id = tt.term_taxonomy_id where tr.object_id = $this->id_post and tt.taxonomy='project_category')";
		$list = terms::all_id($where);
		if (count($list)) {
			$item = new terms($list[0]);
			return ucfirst($item->name());
		} else {
			return 'Autres';
		}
	}

}
