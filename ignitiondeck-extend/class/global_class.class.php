<?php
/*
 * 	date en timestamp php  -> int(11) en base
 *  
 * 	Getteur magiques avec $instance->nomvariable();
 * 	Insertion d'un objet : $instance = new objet();
 * 	$instance->insert();
 * 
 */
define('bii_global_class_version', '2.4.5');

class global_class {

	private $class_name;
	protected static $PDO = null;

	static function identifiant() {
		return "id";
	}

	/**
	 * Constructeur de la classe
	 * @param int $id [optional] <p>
	 * L'identifiant de l'objet que l'on souhaite construire.
	 * Si $id = 0, un objet avec les paramètres par défauts est créé
	 * </p>
	 * @return object L'objet créé
	 */
	function __construct($id = 0) {
		$id = intval($id);
		$identifiant = static::identifiant();
		$var_object = get_object_vars($this);
		foreach ($var_object as $key => $val) {
			if (strpos($key, $identifiant) === 0 || strpos($key, 'date') === 0) {
				$this->{$key} = 0;
			} else {
				$this->{$key} = '';
			}
		}
		$this->class_name = static::prefix_bdd() . static::nom_classe_bdd();
		if ($id !== 0) {
			$pdo = static::getPDO();
//			$cn = static::nom_classe_bdd();
			$query = "select * from $this->class_name where $identifiant='" . $id . "'";
//			echo $query;
			$query_select = $pdo->query($query);
			if ($row_select = $query_select->fetch(PDO::FETCH_OBJ)) {
				foreach ($row_select as $key => $value) {
					if (property_exists($this, $key)) {
						$this->{$key} = $value;
					}
				}
			}
		}
//		var_dump($this);
		return $this;
	}

	/**
	 * Retourne l'id de l'objet appellé
	 */
	function id() {
		$identifiant = static::identifiant();
		return $this->$identifiant;
	}

	/**
	 * Retourne l'option_value de l'objet appellé
	 * option_value est la valeur que prend l'option quand elle est appellée, id étant sa value
	 * exemple : &lt;option value="$id" &gt;$option_value &lt;/option&gt
	 */
	function option_value() {
		return $this->id();
	}

	static function autoTable() {
		
	}

	/**
	 * Génère les options d'une selection d'objets
	 */
	static function genOptionForm($where = "", $value = "") {
		$liste = static::all_id($where);
		$input = "";
		foreach ($liste as $id_fk) {
			$item = new static($id_fk);
			$input.= "<option value='$id_fk' ";
			if (is_array($value) && in_array($id_fk, $value)) {
				$input .= " selected='selected' ";
			} elseif ($value == $id_fk) {
				$input .= " selected='selected' ";
			}
			$input.= ">" . $item->option_value() . "</option> ";
		}
		return $input;
	}

	// <editor-fold desc="Interface admin">
	static protected function class_fa() {
		return "";
	}

	static function iconFa() {
		$fa = static::class_fa();
		if ($fa) {
			echo "<i class='fa $fa'></i> ";
		}
	}

	/**
	 * Le nom de la classe sur l'interface d'administration
	 */
	static function nom_classe_admin() {
		$called_class = get_called_class();
		if (static::prefix_bdd() && strpos($called_class, static::prefix_bdd()) !== false) {
			return substr($called_class, 5);
		}
//		if(static::singulier()){
//			$called_class.="s";
//		}
		return $called_class;
	}

	/**
	 * Le nom de la table sur la base de données
	 */
	static function nom_classe_bdd() {
		$called_class = get_called_class();
		return $called_class;
	}

	/**
	 * Indique si le nom de la classe est féminin
	 */
	static function feminin() {
		return false;
	}

	/**
	 * Indique si le nom de la classe est singulier
	 */
	static function singulier() {
		return true;
	}

	/**
	 * Indique si la classe est éditable dans l'interface d'administration
	 */
	static function editable() {
		return true;
	}

	/**
	 * Indique si la classe est supprimable dans l'interface d'administration
	 */
	static function supprimable() {
		return true;
	}

	static function supprimable_ajax($id) {
		return false;
	}

	protected function value_ajax() {
		return $this->id();
	}

	/**
	 * Indique si la classe dispose d'une page d'affichage dans l'interface d'administration
	 */
	static function affichable() {
		return false;
	}

	/**
	 * Indique si la classe est dupliquable dans l'interface d'administration
	 */
	static function duplicable() {
		return false;
	}

	/**
	 * Indique si la classe est rafraichissable dans l'interface d'administration
	 */
	static function rafraichissable() {
		return false;
	}

	/**
	 * Indique si l'on doit afficher le bloc filtre dans l'interface d'administration
	 */
	static function display_filter() {
		return false;
	}

	/**
	 * Indique si l'on doit afficher le bloc short code dans l'interface d'administration
	 */
	static function display_all_short_code() {
		return false;
	}

	/**
	 * retourne un shortcode, méthode à hériter
	 * @return string
	 */
	public function shortcode() {
		return "";
	}

	/**
	 * Retourne les options de filtre
	 * @param array $array_selected [optionnal]
	 */
	static function filters_form_arguments($array_selected = array()) {
		?><option class="nb" value="id" data-oldval="id" >Id</option><?php
	}

	/**
	 * Indique si l'on doit afficher le bloc pagination dans l'interface d'administration
	 */
	static function display_pagination() {
		return false;
	}

	/**
	 * Retourne la limite de pagination
	 */
	static function pagination_limit() {
		return 20;
	}

	/**
	 * Méthode de duplication
	 * @param int $id <p>
	 * L'identifiant de l'objet à dupliquer
	 * </p>
	 * @return object L'objet dupliqué
	 */
	static function duplicate($id) {
		$identifiant = static::identifiant();
		$itemd = new static($id);
		$data = $itemd->tabPropValeurs();
		unset($data[$identifiant]);
		$newitem = new static();
		$newitem->insert();
		$newitem->updateChamps($data);
		return $newitem;
	}

	/**
	 * Retourne la clause where par défaut pour l'affichage en liste des objets (par défaut id > 0)
	 */
	static function whereDefault() {
		$identifiant = static::identifiant();
		return "$identifiant > 0 ";
	}

	/**
	 * Retourne la clause group by par défaut pour l'affichage en liste des objets (par défaut vide)
	 */
	static function groupByDefault() {
		return "";
	}

	/**
	 * Retourne l'adresse de redirection après validation du formulaire d'édition
	 */
	static function redirectEdit() {
		$nom_classe = static::nom_classe_bdd();
		$lien = "list.php?class=$nom_classe&edit=ok";
		if (static::isWordpress()) {
			$lien = "admin.php?page=$nom_classe" . "_list&edit=ok";
		}
		return $lien;
	}

	/**
	 * Retourne l'adresse de redirection après une erreur du formulaire d'édition
	 */
	static function redirectError() {
		$nom_classe = static::nom_classe_bdd();
		$lien = "edit.php?class=$nom_classe&erreur_edit=1";
		if (static::isWordpress()) {
			$lien = "admin.php?page=$nom_classe" . "_edit&erreur_edit=1";
		}
		return $lien;
	}

	/**
	 * Retourne l'adresse de redirection après le clic sur le bouton modifier et rester du formulaire d'édition
	 */
	static function redirectStay($id) {
		$nom_classe = static::nom_classe_bdd();
		$lien = "edit.php?class=$nom_classe&id_edit=$id";
		if (static::isWordpress()) {
			$lien = "admin.php?page=$nom_classe" . "_edit&id_edit=$id";
		}
		return $lien;
	}

	/**
	 * Retourne le titre de la page d'affichage en liste sur l'interface d'administration
	 */
	static function titre_page_admin_liste() {
		$nom = "Liste des ";
		$nom.= ucfirst(static::nom_classe_admin());
		if (static::singulier()) {
			$nom.="s";
		}
		return $nom;
	}

	/**
	 * Retourne le message affiché lorsqu'il n'y a (justement) rien à afficher
	 */
	static function messageRienAAfficher() {
		$message = "Aucun";
		if (static::feminin()) {
			$message.="e";
		}
		$message .= " " . static::nom_classe_admin() . " à afficher";

		return $message;
	}

	/**
	 * Retourne le message de confirmation avant supression
	 */
	static function messageConfirmation() {
		$nom_classe = strtolower(static::nom_classe_admin());

		$message = "Voulez vous vraiment supprimer ce";
		if (static::feminin() && static::singulier()) {
			$message.="tte";
		} elseif (!static::singulier()) {
			$message.="s";
		} else /* masculin et singulier */ {
			$liste_voyelle = array("a", "e", "i", "o", "u");
			$premiere_lettre = substr($nom_classe, 0, 1);
			if (in_array($premiere_lettre, $liste_voyelle)) {
				$message.="t";
			}
		}
		$message .= " " . $nom_classe . " ?";

		return $message;
	}

	/**
	 * Retourne des scripts additonnels dans edit.php
	 */
	static function admin_additional_script() {
		//Avec netbeans, le javascript n'est considéré comme tel qu'entre des balises script oiu dans un ficher .js
		if (0) {
			?><script><?php
		}
		?>

		<?php
		if (0) {
			?></script><?php
		}
	}

	/**
	 * Texte du bouton d'ajout d'un nouvel objet
	 */
	static function messageNouveau() {
		return "Ajouter";
	}

	/**
	 * Retourne le nombre d'attributs d'un objet, + les attributs editable, duplicable etc
	 * 
	 * Un objet a les attributs suivants : 
	 * id, nom et est duplicable, éditable pas affichable et pas supprimable. La fonction retourne 4.
	 * 
	 */
	static function nbAttr() {
		$nb = count(static::getListeProprietes());
		if (static::duplicable()) {
			$nb++;
		}
		if (static::editable()) {
			$nb++;
		}
		if (static::rafraichissable()) {
			$nb++;
		}
		if (static::affichable()) {
			$nb++;
		}
		if (static::supprimable()) {
			$nb++;
		}
		return $nb;
	}

	/**
	 * Retourne le préfixe de la table sur la base de données
	 */
	static function prefix_bdd() {
		if (!static::isWordpress()) {
			return "";
		} else {
			global $table_prefix;
			return $table_prefix;
		}
	}

	/**
	 * Retourne le titre de la page d'édition de l'interface d'administration
	 */
	function titreEdit() {
		$message = "Modification ";
		$nouveau = "";
		if (!$this->id()) {
			$message = "Ajout ";

			if (static::feminin()) {
				if (static::singulier()) {
					$nouveau = "nouvelle ";
					$message .= "d'une ";
				} else {
					$nouveau = "nouvelles ";
					$message .= "de ";
				}
			} else {
				if (static::singulier()) {
					$liste_voyelle = array("a", "e", "i", "o", "u");
					$premiere_lettre = substr($this->nom_classe_admin(), 0, 1);
					if (in_array($premiere_lettre, $liste_voyelle)) {
						$nouveau = "nouvel ";
					} else {
						$nouveau = "nouveau ";
					}
					$message .= "d'un ";
				} else {
					$nouveau = "nouveaux ";
					$message .= "de ";
				}
			}
		} else {
			if (static::singulier()) {
				if (static::feminin()) {
					$message .= "d'une ";
				} else {
					$message .= "d'un ";
				}
			} else {
				$message .= "de ";
			}
		}
		$message .= $nouveau . $this->nom_classe_admin();
		return $message;
	}

	/**
	 * Retourne le titre de la page d'affichage de l'interface d'administration
	 */
	function titreDisplay() {
		$message = "Affichage ";
		$nouveau = "";

		if (static::singulier()) {
			if (static::feminin()) {
				$message .= "d'une ";
			} else {
				$message .= "d'un ";
			}
		} else {
			$message .= "de ";
		}

		$message .= $nouveau . $this->nom_classe_admin();
		return $message;
	}

	/**
	 * Retourne col-xs-12
	 */
	protected static function default_class_stuff() {
		return "col-xxs-12 col-xs-12";
	}

	/**
	 * Retourne col-xs-6
	 */
	protected static function class_stuff2Min() {
		return "col-xxs-6 col-xs-6";
	}

	/**
	 * input : Génère l'input correspondant au champ donné en paramètre. Certaines options permettent également l'affichage à la volée
	 * <ul>
	 * <li>les champs id_class génèrent des select de type select option value=id_class >$item->option_value()< </li>
	 * <li>les champs is_* génèrent des checkbox</li>
	 * <li>les champs date_* génèrent des input avec datepicker</li>
	 * <li>les champs prix* génèrent des input normaux suivis d'un €</li>
	 * <li>les champs image* génèrent des champs pour l'ajout d'images</li>
	 * <li>si value est interprété comme étant du code HTML, $options[echo] = 1 et le champ devient un éditeur HTML</li>
	 * </ul>
	 * @param string $champ <p>
	 * le champ dont on veut l'input
	 * </p>
	 * @param string $nom_champ [optional] <p>
	 * le nom du champ tel que l'on souhaite qu'il soit affiché (par défaut $champ)
	 * </p>
	 * @param int $value [optional] <p>
	 * la valeur de l'input tel que l'on souhaite qu'elle soit affichée (par défaut 0)
	 * </p>
	 * @param array $options [optional] <p>
	 * Un tableau d'options pour l'affichage
	 * description : la description de l'input
	 * class : les classes que l'on souhaite ajouter (string ou array)
	 * echo : indique que l'on souhaite echo la valeur dans la fonction
	 * separator : le séparateur entre chaque input
	 * </p>
	 * @return string le code html de l'input et des div autour. Retourne NULL si $options[echo] existe
	 */
	function input($champ, $nom_champ = "", $value = 0, $options = array()) {
		$method_class = $champ . "_class_stuffIA";
		if (method_exists($this, $method_class)) {
			$class_stuff = $this->$method_class();
		} else {
			$class_stuff = static::default_class_stuff();
			if (strpos($champ, "is_") !== false) {
				$class_stuff = static::class_stuff2Min();
			}
		}
		$method_champ = $champ . "_inputIA";
		if (method_exists($this, $method_champ)) {
			$input = $this->$method_champ();
			$label = "";
		} else {
			$class = "";
			if ($nom_champ == "") {
				$nom_champ = $champ;
			}
			$nom_champ = ucfirst($nom_champ);
			if (isset($options["class"])) {
				if (is_array($options["class"])) {
					foreach ($options["class"] as $item) {
						$class.= $item;
					}
				} else {
					$class = $options["class"];
				}
			}
			$description = "<p>";
			if (isset($options["description"])) {
				$description .= $options["description"];
			}
			$description .= "</p>";

			$div = '<div id="' . $champ . '_div" class="stuffbox ' . $class_stuff . ' ">';

			$label = $div . "<h3><label for='$champ'>$nom_champ</label></h3>";
			$input = "<div class='inside' >";

			if (strpos($champ, "id_") !== false && $champ != "id_analytics") {
				$fk = substr($champ, 3);
				if (class_exists($fk)) {
					$input .= "<select id='$champ' name='$champ' class='form-control $class' >";
					foreach ($fk::all_id() as $id_fk) {
						$item = new $fk($id_fk);
						$input.= "<option value='$id_fk' ";
						if ($value == $id_fk) {
							$input .= " selected='selected' ";
						}
						$input.= ">" . utf8_encode($item->option_value()) . "</option> ";
					}
					$input .= "</select>";
				}
//			echo $fk;
			} elseif (strpos($champ, "image") !== false) {

				$input .= "<div class='form-inline'>"
					. "<div class='previsualisation'>

						<img id='image-preview' width='100' height='100' src='$value' alt='image' />

					</div>"
					. "<label for='$champ'>" . __('Photo 1') . "</label><br />"
					. "<div class='item $class form-group'>"
					. "<input id='$champ' type='text' name='$champ' class='form-control' value='$value' />"
					. "<input id='upload_$champ' class='input-upload $champ form-control'  type='button' value='Parcourir' />"
					. "</div>"
					. "</div>"
					. "<div class='spacer'></div>"
					. "<script>"
					. "jQuery('#upload_$champ').click(function(e) {
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
							jQuery('#$champ').val(attachment.url);
							jQuery('#$champ').trigger('keyup');
						});
						custom_uploader.open();"
					. "});jQuery('#$champ').on('keyup', function () {
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
			} elseif (!is_array($value) && (strip_tags($value) != $value))/* Si $value est du texte HTML */ {
				echo $label . $input;
				wp_editor(utf8_encode($value), $champ);
				$options["echo"] = 0;
//			$input .= "<textarea id='$champ' name='$champ' class='wp-editor-area $class' >".utf8_encode($value)."</textarea>  ";
				echo "</div></div>";
				return null;
			} elseif (strpos($champ, "is_") !== false) {
				$checked = "";
				if ($value == 1) {
					$checked = "checked='checked'";
				}
				$input .= "<input type='hidden'  id='$champ' name='$champ' value='$value' />";
				$input .= "<input type='checkbox'  id='$champ-cbx' name='$champ-cbx' class='cbx-data-change form-control' data-change='$champ' $checked />";
			} else {
				$type = "type='text'";
				$value = utf8_encode($value);
				if (strpos($champ, "nb_") !== false) {
					$type = "type='number'";
				}
				$input .= "<input id='$champ' name='$champ' class='form-control $class' $type value='$value' />  ";
			}
			if (strpos($champ, "prix") !== false) {
//				$input .= "&euro;";
			}
			$input.= $description;



			$input .= "</div>";
			$input .= "</div>";
			if (isset($options["separator"])) {
				$input.=$options["separator"];
			}
		}

		if (isset($options["echo"])) {
			echo $label . $input;
		}
		return $label . $input;
	}

	/**
	 * form_edit : Génère le formulaire d'édition de la classe en cours 
	 */
	function form_edit($do_not_display = array()) {
		$liste_prop = static::getListeProprietesFormEdit();

		if (isset($liste_prop["image"])) {
			//On met $liste_prop["image"] en dernière position
			$val = $liste_prop["image"];
			unset($liste_prop["image"]);
			$liste_prop["image"] = $val;
		}
//		var_dump($liste_prop);
		unset($liste_prop["id"]);
		$i = 1;
		foreach ($liste_prop as $prop => $val) {
			if (!in_array($prop, $do_not_display)) {
				$options = array("echo" => 1);
				if (strpos($prop, "date") !== false) {
					$options["class"] = "datepicker";
				}
				if (strpos($prop, "couleur") !== false) {
					$options["class"] = "input-colorpicker";
				}
				if ($i % 2 == 0) {
//					$options["separator"] = "<div class='clearfix'></div>";
				}
				$this->input($prop, $val, $this->$prop(), $options);
			}
			$i++;
		}
	}

	/**
	 * tableHeader : affiche les headers du tableau d'affichage de la liste d'objet sur l'interface d'administration
	 * @param string $propriete <p>
	 * la propriété à afficher
	 * </p>
	 * @param string $nom_propriete [optional] <p>
	 * le nom de la propriété telle que l'on souhaite qu'elle soit affichée (par défaut $propriete)
	 * </p>
	 * @param string $additional_class [optional] <p>
	 * les classes css que l'on souhaite ajouter 
	 * </p>
	 * @param int $width [optional] <p>
	 * la largeur du th
	 * </p>
	 * @param int $is_sortable [optional] <p>
	 * indique si le th possède un lien pour effectuer le tri
	 * </p>
	 * @return null
	 */
	static function tableHeader($propriete, $nom_propriete = null, $additional_class = "", $width = null, $is_sortable = true) {
		$class = get_called_class(); //pour éviter de se retrouver avec une class mère quand on effectue un tri d'une classe héritée 2 fois
		$fonction = $propriete . "_tableHeader";
//		consoleLog($fonction);
		$item = new static();
		if (!method_exists($item, $fonction)) {
//		consoleLog("dontexists");
			$strwidth = "";
			if ($width != null) {
				$strwidth = "width='$width'";
			}
			if ($nom_propriete == null) {
				$nom_propriete = $propriete;
			}
			$sortable = "sortable";
			$ordre = "desc";
			if (isset($_REQUEST['orderby'])) {
				$prop = $_REQUEST['orderby'];
				if (isset($_REQUEST['ordre']) && $prop == $propriete) {
					$ro = $_REQUEST['ordre'];
					if ($ro == "desc") {
						$ordre = "asc";
					}
				}
			}
			$filter = "";
			if (isset($_REQUEST["filter"])) {
				$filter = "&amp;filter=" . $_REQUEST["filter"];
			}
			$lien = "<a href='list.php?class=$class$filter&amp;orderby=$propriete&amp;ordre=$ordre'>";
			if (static::isWordpress()) {
				$lien = "<a href='admin.php?page=$class" . "_list&amp;$filter&amp;orderby=$propriete&amp;ordre=$ordre'>";
			}
			$endlien = "</a>";
			if (!$is_sortable) {
				$lien = "";
				$sortable = "";
				$ordre = "";
				$endlien = "";
			}

			$classecss = "manage-column column-$propriete $sortable $ordre $additional_class";
			?>
			<th class="<?php echo $classecss; ?>" id="<?php echo $propriete; ?>" scope="col" <?php echo $strwidth; ?>>
				<?php echo $lien; ?>
				<span><?php echo ucfirst($nom_propriete); ?></span>
				<span class="sorting-indicator"></span>
				<?php echo $endlien; ?>
			</th>
			<?php
		} else {
//			consoleLog("exists");
			$item->$fonction();
		}
	}

	/**
	 * tableHeader : affiche les headers du tableau d'affichage de la liste d'objet sur l'interface d'administration
	 * @param string $propriete <p>
	 * la propriété à afficher
	 * </p>
	 * @param string $nom_propriete [optional] <p>
	 * le nom de la propriété telle que l'on souhaite qu'elle soit affichée (par défaut $propriete)
	 * </p>
	 * @param string $additional_class [optional] <p>
	 * les classes css que l'on souhaite ajouter 
	 * </p>
	 * @param int $width [optional] <p>
	 * la largeur du th
	 * </p>
	 * @param int $is_sortable [optional] <p>
	 * indique si le th possède un lien pour effectuer le tri
	 * </p>
	 * @return null
	 */
	static function tableHeader2($propriete, $nom_propriete = null, $additional_class = "", $width = null, $is_sortable = true) {
		$class = static::nom_classe_bdd();
		$strwidth = "";
		if ($width != null) {
			$strwidth = "width='$width'";
		}
		if ($nom_propriete == null) {
			$nom_propriete = $propriete;
		}
		$sortable = "sortable";
		$ordre = "desc";
		if (isset($_REQUEST['orderby'])) {
			$prop = $_REQUEST['orderby'];
			if (isset($_REQUEST['ordre']) && $prop == $propriete) {
				$ro = $_REQUEST['ordre'];
				if ($ro == "desc") {
					$ordre = "asc";
				}
			}
		}
		$filter = "";
		if (isset($_REQUEST["filter"])) {
			$filter = "&amp;filter=" . $_REQUEST["filter"];
		}
		$lien = "<a href='list.php?class=$class$filter&amp;orderby=$propriete&amp;ordre=$ordre'>";
		$endlien = "</a>";
		if (!$is_sortable) {
			$lien = "";
			$sortable = "";
			$ordre = "";
			$endlien = "";
		}

		$classecss = "manage-column column-$propriete $sortable $ordre $additional_class";
		?>
		<th class="<?php echo $classecss; ?>" id="<?php echo $propriete; ?>-foot" scope="col" <?php echo $strwidth; ?>>
			<?php echo $lien; ?>
			<span><?php echo ucfirst($nom_propriete); ?></span>
			<span class="sorting-indicator"></span>
			<?php echo $endlien; ?>
		</th>
		<?php
	}

	/**
	 * retourne un clearfix
	 */
	protected function clearfix() {
		return "<div class='clearfix' ></div>";
	}

	/**
	 * retourne un clearfix
	 */
	protected function clearfix_inputIA() {
		return "<div class='clearfix' ></div>";
	}

	/**
	 * retourne les tables headers spéciaux d'une classe
	 */
	private static function tableHeaderDES($additional_class = "", $width = 60, $text = "Éditer", $footer = false) {
		$strwidth = "";
		if ($width != null) {
//			$strwidth = "width='$width'";
		}
		$slug = substr(strtolower($text), 0, 5);
		if ($text == "Éditer") {
			$slug = "edit";
		}
		if ($footer) {
			$slug .= "-footer";
		}
		?>
		<th class="manage-column column-edit <?php echo $additional_class; ?>" id="<?php echo $slug; ?>" <?php echo $strwidth; ?>><span><?php echo $text ?></span></th>
		<?php
	}

	static function tableHeaderSupprimmer($footer, $additional_class = "", $width = 67) {
		return static::tableHeaderDES($additional_class, $width, "Supprimer", $footer);
	}

	static function tableHeaderRefresh($footer, $additional_class = "", $width = 67) {
		return static::tableHeaderDES($additional_class, $width, "Rafraichir", $footer);
	}

	static function tableHeaderEditer($footer, $additional_class = "", $width = 60) {
		return static::tableHeaderDES($additional_class, $width, "Éditer", $footer);
	}

	static function tableHeaderAfficher($footer, $additional_class = "", $width = 60) {
		return static::tableHeaderDES($additional_class, $width, "Afficher", $footer);
	}

	static function tableHeaderDupliquer($footer, $additional_class = "", $width = 65) {
		return static::tableHeaderDES($additional_class, $width, "Dupliquer", $footer);
	}

	/**
	 * headersNotSortable : retourne la liste des paramètres non ordonnables
	 * @return array la liste des paramètres non ordonnables
	 */
	static function headersNotSortable() {
		$proprietes = array_keys(static::getListeProprietes());
		$objet = new static();
		$trueproperties = array_keys($objet->tabPropValeurs());
		$array = array();
		foreach ($proprietes as $prop) {
			if (!in_array($prop, $trueproperties)) {
				$array[] = $prop;
			}
		}
		return $array;
	}

	/**
	 * tableHeaders : Génère les en-têtes de tableau d'une classe, avec des liens pour le tri
	 * @param array $not_sortable [optional]<p>
	 * les champs qui ne sont pas triables, auxquels s'ajoutent les champs obtenus avec headersNotSortable()
	 * </p>
	 * @param array $do_not_display [optional]<p>
	 * les champs que l'on ne souhaite pas afficher [déprécié]
	 * </p>
	 * @param bool $footer [optional] <p>
	 * indique si l'en tête de table est en haut ou en bas (pour éviter les duplications d'id)
	 * </p>
	 * @return null
	 */
	static function tableHeaders($not_sortable = array(), $do_not_display = array(), $footer = false) {
		$not_sortable = array_merge($not_sortable, static::headersNotSortable());
		foreach (static::getListeProprietes() as $prop => $nom_prop) {
			$width = null;
			if ($prop == "id") {
				$max_id = static::last_id();
				$len = strlen($max_id);
				$width = ($len + 1) * 14;
			}
			if (!in_array($prop, $do_not_display)) {
				if ($footer) {
					static::tableHeader2($prop, $nom_prop, "", $width, !in_array($prop, $not_sortable));
				} else {
					static::tableHeader($prop, $nom_prop, "", $width, !in_array($prop, $not_sortable));
				}
			}
		}

		if (static::duplicable() && !in_array("dupliquer", $do_not_display)) {
			static::tableHeaderDupliquer($footer);
		}
		if (static::rafraichissable() && !in_array("rafraichir", $do_not_display)) {
			static::tableHeaderRefresh($footer);
		}
		if (static::editable() && !in_array("editer", $do_not_display)) {
			static::tableHeaderEditer($footer);
		}
		if (static::affichable() && !in_array("afficher", $do_not_display)) {
			static::tableHeaderAfficher($footer);
		}
		if (static::supprimable() && !in_array("supprimer", $do_not_display)) {
			static::tableHeaderSupprimmer($footer);
		}
	}

	/**
	 * ligneValeurs : Génère les cases de valeur pour un affichage en tableau dans une liste
	 * Cette fonction utilise la méthode getListeProprietes() pour générer les collones d'un tableau d'affichage
	 * Ces colonnes sont générées automatiquement, sauf si une méthode nomdelapropriete_ligneIA existe, auquel cas la fonction nomdelapropriete_ligneIA est appellée
	 * Pour la génération auto :
	 * Si la propriété contient la chaine couleur, une case coloré à la couleur hexadécimale est renvoyée
	 * Si la propriété contient la chaine mail, une lien avec un mailto est renvoyé
	 * Si la propriété contient la chaine url_, un lien en target blank est renvoyé
	 * Si la propriété contient la chaine image_, la méthde nomdelapropriete_admin est apellée
	 * Si la propriété contient la chaine prix, la chaine € est ajoutée
	 * Si la propriété contient la chaine is_ (booléen), si la valeur est = à 1 alors "oui" est retourné, sinon "non" est retourné 
	 * @param int $index_troncature [optional]<p>
	 * les champs qui ne sont pas triables, auxquels s'ajoutent les champs obtenus avec headersNotSortable()
	 * </p>
	 * @param array $do_not_show [optional]<p>
	 * les champs que l'on ne souhaite pas afficher [déprécié]
	 * </p>
	 * @return null
	 */
	function ligneValeurs($index_troncature = 150, $do_not_show = array()) {
		foreach (static::getListeProprietes() as $prop => $val) {
			$value = $this->$prop();
			$method_ligne = $prop . "_ligneIA";
			if (!method_exists($this, $method_ligne)) {
				if (strpos($prop, "id_") !== false && $prop != "id_analytics") {
					$called_class = substr($prop, 3);
					$id = $value;
					$item = new $called_class($id);
					$value = $item->option_value();
				}

				if (!is_array($value)) {
					$troncature = utf8_encode(strip_tags(static::tronquer($value, $index_troncature)));
					$title = utf8_encode(strip_tags($value, $index_troncature));
				} else {
					$troncature = $value;
					$title = $value;
				}


				if (strpos($prop, "couleur") !== false) {

					$title = $value;
					$value = "<div style='width:100%;height:100%;background-color:$value;'>$value</div>";
					$troncature = $value;
				}

				if (strpos($prop, "mail") !== false) {

					$title = $value;
					$value = "<a href='mailto:$value' >$value</a>";
					$troncature = $value;
				}
				if (strpos($prop, "url_") !== false) {
					$value = static::value_as_link($value);
					$troncature = $value;
					$title = $value;
				}
				if (strpos($prop, "image_") !== false) {
					$method = $prop . "_admin";
					$value = $this->$method();
					$troncature = $value;
					$title = $value;
				}
				if (strpos($prop, "prix") !== false) {
					$value .= " €";
					$troncature = $value;
					$title = $value;
				}
				if (strpos($prop, "is_") !== false) {
					if ($value == 0) {
						$value = "non";
					} else {
						$value = "oui";
					}
					$troncature = $value;
					$title = $value;
				}
			}

			if (!in_array($prop, $do_not_show)) {
				$method_ligne = $prop . "_ligneIA";

				if (!method_exists($this, $method_ligne)) {
					?>
					<td class="<?php echo $prop; ?>" <?php if ($title != $troncature) { ?> title="<?php echo $title; ?>" <?php } ?> >
						<?php echo $troncature; ?>
					</td>
					<?php
				} else {
					$this->$method_ligne();
				}
			}
		}


		if (static::duplicable() && !in_array("dupliquer", $do_not_show)) {
			$this->cellDuplicate();
		}
		if (static::rafraichissable() && !in_array("rafraichir", $do_not_show)) {
			$this->cellRefresh();
		}
		if (static::editable() && !in_array("editer", $do_not_show)) {
			$this->cellEdit();
		}
		if (static::affichable() && !in_array("afficher", $do_not_show)) {
			$this->cellAfficher();
		}
		if (static::supprimable() && !in_array("supprimer", $do_not_show)) {
			$this->cellDelete();
		}
	}

	/**
	 * cellDuplicate : affiche le code HTML de la cellule contenant le lien pour dupliquer l'objet en cours
	 */
	function cellDuplicate() {
		?>
		<td>
			<span style="cursor:pointer;" class="dup" id="dup-id-<?php echo $this->id(); ?>">
				<img src="images/duplicate.png" />
			</span>
		</td>
		<?php
	}

	/**
	 * cellRefresh : affiche le code HTML de la cellule contenant le lien pour rafraichir l'objet en cours
	 */
	function cellRefresh() {
		?>
		<td>
			<span style="cursor:pointer;" class="ref" id="ref-id-<?php echo $this->id(); ?>">
				<span id="fa-ref-<?php echo $this->id(); ?>" class="fa fa-2x fa-refresh" />
			</span>
		</td>
		<?php
	}

	/**
	 * cellEdit : affiche le code HTML de la cellule contenant le lien pour éditer l'objet en cours
	 */
	function cellEdit() {
		$lien = "edit.php?class=" . static::nom_classe_bdd() . "_display&amp;id_edit=" . $this->id();
		if (static::isWordpress()) {
			$lien = "admin.php?page=" . static::nom_classe_bdd() . "_edit&amp;id_edit=" . $this->id();
		}
		?>
		<td>
			<a href="<?php echo $lien ?>">
				<span class="fa fa-2x fa-pencil"></span>
			</a>
		</td>
		<?php
	}

	/**
	 * cellEdit : affiche le code HTML de la cellule contenant le lien pour éditer l'objet en cours
	 */
	function cellAfficher() {
		$lien = "afficher.php?class=" . static::nom_classe_bdd() . "_display&amp;id_display=" . $this->id();
		if (static::isWordpress()) {
			$lien = "admin.php?page=" . static::nom_classe_bdd() . "_display&amp;id_display=" . $this->id();
		}
		?>
		<td>
			<a href="<?php echo $lien ?>">
				<span class="fa fa-2x fa-eye"></span>
			</a>
		</td>
		<?php
	}

	/**
	 * cellDelete : affiche le code HTML de la cellule contenant le lien pour supprimer l'objet en cours
	 */
	function cellDelete() {
		?>
		<td>
			<span style="cursor:pointer;" class="del" id="del-id-<?php echo $this->id(); ?>">
				<span class="fa fa-2x fa-trash-o"></span>
			</span>
		</td>
		<?php
	}

	//</editor-fold>
	//<editor-fold desc="Gestion BDD">

	static function getPDO() {
		if (!static::$PDO) {
			$pdo = rpdo::getInstance();
			static::$PDO = $pdo;
		} else {
			$pdo = static::$PDO;
		}
		return $pdo;
	}

	/**
	 * Deprecated
	 */
	function select($champs = false, $conds = false, $one = false) {
		if ($conds && !is_array($conds)) {
			$conds = array($conds);
		}
		if ($champs && !is_array($champs)) {
			$champs = array($champs);
		}
		$req = 'SELECT ';
		if ($champs)
			$req .= implode(', ', $champs) . ' ';
		else
			$req .= '* ';
		$req .= 'FROM ' . $this->class_name . ' ';
		if ($conds) {
			$req .= 'WHERE ' . implode(' AND ', $conds) . ' ';
		}
		if ($one) {
			$req .= 'LIMIT 1';
		}
		$pdo = static::getPDO();
		$query_select = $pdo->query($req);
		$array = array();
		while ($row_select = $query_select->fetch(PDO::FETCH_OBJ)) {
			if ($one) {
				foreach ($row_select as $key => $value) {
					if (property_exists($this, $key)) {
						$this->{$key} = $value;
					}
				}
			} else {
				$array[] = $row_select;
			}
		}
		if (count($array))
			return $array;
		else
			return $this;
	}

	/**
	 * Insert : insère un objet dans la base de données avec ses valeur par défaut
	 * @param mixed $param [optional] <p>
	 * Vide
	 * </p>
	 * @return int L'id de l'objet créé
	 */
	function insert($param = false) {
		if ($this->id == 0) {
			$pdo = static::getPDO();
			$class_name = static::prefix_bdd() . static::nom_classe_bdd();
			$cle = array();
			$value = array();

			$cle_str = '';
			$value_str = '';
			if (count($cle) > 0) {
				$cle_str = "," . implode(', ', $cle);
				$value_str = "," . implode(', ', $value);
			}
			$id = static::identifiant();
			$req = "INSERT INTO $class_name ($id" . $cle_str . ") values (''" . $value_str . ")";
//			pre($req);
			$pdo->exec($req);
			$this->id = $pdo->lastInsertId();
			if (property_exists($this, "date_insert")) {
				$this->updateChamps(time(), "date_insert");
			}
			return $this->id;
		} else {
			echo "L'objet existe déjà";
			exit;
		}
	}

	protected static function dateToTimestamp($date, $heure = 0, $minute = 0, $seconde = 0) {
		$val = 0;
		if ($date != '' && $date != null && strpos($date, "/") !== false) {
			$date_expl = explode("/", $date);
			$jour = $date_expl[0];
			$mois = $date_expl[1];
			$annee = $date_expl[2];
			$val = mktime($heure, $minute, $seconde, $mois, $jour, $annee);
		}
		return $val;
	}

	/**
	 * updateChamps : update un objet dans la base de données 
	 * @param mixed $value <p>
	 * si $value est un tableau associatif de type $key=>$val, la fonction execute une requête pour update $key à $val
	 * si $value est un struct (bool, int double, string etc) $value représente la valeur à update
	 * </p>
	 * @param mixed $champs [optional]<p>
	 * représente le champ à update
	 * </p>
	 * @param mixed $where [optional]<p>
	 * la clause where où effectuer l'update, par défaut $where = "where id = $this->id"
	 * </p>
	 */
	function updateChamps($value, $champs = false, $where = false) {
		$identifiant = static::identifiant();
		if ($this->id === 0)
			return false;
		if (!is_array($value) && $champs !== false) {
			$value = array($champs => $value);
		}
		$string = '';
		$array_prepare = array();
		$var_object = get_object_vars($this);
		foreach ($value as $key => $val) {
			if (key_exists($key, $var_object)) {
				if (is_array($val)) {
//					pre($val, "red");
//					pre($key, "orange");
				}
//			$val = addslashes($val);
				$val = utf8_decode($val);
				$val = str_replace("\\", "", $val);



				if ($key === 'id_' . $this->class_name)
					continue;

				if (strpos($key, $this->class_name) === 0) {
					$key_tmp = str_replace($this->class_name . '_', '', $key);
					if (key_exists($key_tmp, $var_object))
						$key = $key_tmp;
				}

				if (strpos($key, 'date_') === 0 && $val != '' && $val != null && strpos($val, "/") !== false) {
					$heure = 0;
					$minute = 0;
					$seconde = 0;
					if (strpos($key, 'date_fin') === 0) {
						$heure = 23;
						$minute = 59;
						$seconde = 59;
					}
					$val = static::dateToTimestamp($val, $heure, $minute, $seconde);
//					pre($val, "blue");
				}
				////
//			echo $val;

				$this->{$key} = $val;
				//pour prepare
				$array_prepare[$key] = $val;
				$string .= " $key = :" . $key;
				$string .= ', ';
			}
		}
		if (strlen($string) > 0) {
			if ($string[strlen($string) - 2] === ',') {
				$string = substr($string, 0, -2);
			}

			$pdo = static::getPDO();
			$class_name = $class_name = static::prefix_bdd() . static::nom_classe_bdd();
			$query_update = "UPDATE $class_name SET $string WHERE ";

			if ($where != false && is_array($where)) {
				foreach ($where as $key => $val) {
					if (key_exists($key, $var_object)) {
						$query_update .= $key . "='" . $val . "' AND ";
					}
				}
				$query_update .=' 1=1';
			} else {
				$query_update .= "$identifiant='" . $this->id . "'";
			}


			$req = $pdo->prepare($query_update);

//			pre($query_update, "purple");

			$retour = $req->execute($array_prepare);
			$pdo = null;
			if ($retour !== false)
				return $this;
		}
		return false;
	}

	/**
	 * deleteStatic : supprimme un objet de la base de données
	 * @param mixed $id <p>
	 * l'id de l'objet à supprimmer
	 * </p>
	 */
	static function deleteStatic($id) {
		$class_name = static::prefix_bdd() . static::nom_classe_bdd();
		$identifiant = static::identifiant();
		if (property_exists($class_name, 'id_delete')) {
			$param = array(
				'id_delete' => $_SESSION['id_user'],
				'date_delete' => time()
			);
			$object = new $class_name($id);
			return $object->updateChamps($param);
		} else {
			$pdo = static::getPDO();
			$return = $pdo->exec("DELETE FROM $class_name WHERE $identifiant = '" . $id . "'");
			$pdo = null;
			return $return;
		}
	}

	/**
	 * deleteWhere : supprimme plusieurs objets de la base de données
	 * @param string $where [optional] <p>
	 * la clause where de la requête
	 * </p>	 
	 */
	static function deleteWhere($where = "") {
		$liste = static::all_id($where);
		foreach ($liste as $id) {
			static::deleteStatic($id);
		}
	}

	/**
	 * Indique si l'ordre par défaut est la propriété ordre
	 * @return boolean
	 */
	public function orderByOrdreDefault() {
		return false;
	}

	/**
	 * Indique l'ordre par défaut
	 * @return boolean
	 */
	public static function nomOrdre() {
		return "ordre";
	}

	static function remiseEnOrdreStatic($id, $filtre = false) {
		if ($filtre == "false") {
			$filtre = false;
		}
		$class_name = static::nom_classe_bdd();
		$nom_ordre = static::nomOrdre();
		$prefix = static::prefix_bdd();
		//
		if (property_exists($class_name, $nom_ordre)) {
			$pdo = static::getPDO();
			$i = 1;
			$object_ordre = new $class_name($id);
//			var_dump($object_ordre);
			$where_requete_ordre = '';
			if ($filtre !== false) {
				$where_requete_ordre = "where " . $filtre . "=" . $object_ordre->$filtre();
			}
			$query = "select id from $prefix$class_name $where_requete_ordre order by $nom_ordre asc";

			$select_object_ordre = $pdo->query($query);
			while ($row_object_ordre = $select_object_ordre->fetch(PDO::FETCH_OBJ)) {
				$object_set_ordre = new $class_name($row_object_ordre->id);
				$param = array(
					'ordre' => $i
				);
				$object_set_ordre->updateChamps($param);
				$i++;
			}
		}
	}

	static function modifOrdreStatic($id, $signe, $filtre = false) {
		if ($filtre == "false") {
			$filtre = false;
		}
		$prefix = static::prefix_bdd();
		$class_name = static::nom_classe_bdd();
		$nom_ordre = static::nomOrdre();
		if (property_exists($class_name, $nom_ordre)) {
			$class_name::remiseEnOrdreStatic($id, $filtre);
			$pdo = static::getPDO();
			$class_name = static::nom_classe_bdd();
			$object_ordre = new $class_name($id);
			$ordre_ancien = $object_ordre->$nom_ordre();

			$where_requete_ordre = "$nom_ordre = (" . $ordre_ancien . $signe . "1)";
			if ($filtre !== false) {
				$where_requete_ordre = $filtre . "=" . $object_ordre->$filtre() . " and " . $where_requete_ordre;
			}

			$query = "select id from $prefix$class_name where $where_requete_ordre order by $nom_ordre asc";
			echo $query;
			$select_inversion_ordre = $pdo->query($query);
			if ($row_inversion_ordre = $select_inversion_ordre->fetch(PDO::FETCH_OBJ)) {
				$object_inversion = new $class_name($row_inversion_ordre->id);
				$object_ordre->updateChamps($object_inversion->$nom_ordre(), $nom_ordre);
				$object_inversion->updateChamps($ordre_ancien, $nom_ordre);
			}
		}
	}

	/**
	 * Supprime l'objet courant de la base de données
	 */
	function delete() {
		if (property_exists($this, 'id_delete')) {
			$param = array(
				'id_delete' => $_SESSION['id_user'],
				'date_delete' => time()
			);
			$object = new $this->class_name($this->id);
			return $object->updateChamps($param);
		} else {
			$pdo = static::getPDO();
			return $pdo->exec("DELETE FROM $this->class_name WHERE id = '" . $this->id . "'");
		}
	}

	/**
	 * all_id : retourne toutes les id d'une classe avec un appel à la base de donnée
	 * @param string $where [optional] <p>
	 * la clause where de la requête
	 * </p>	 
	 * @return array un tableau d'identifiants
	 */
	static function all_id($where = "", $groupBy = "") {
		$pdo = static::getPDO();
		$identifiant = static::identifiant();
		$req = static::baseRequest();
		if ($where != "") {
			$req .= " where " . $where;
		}
		if ($groupBy != "") {
			$req .= " GROUP BY " . $groupBy;
		}
		$select = $pdo->query($req);
		$liste = array();
		bii_write_log($req);
//		pre($req,"red");
		while ($row = $select->fetch()) {
			$liste[] = $row[$identifiant];
		}

		$pdo = null;
		return $liste;
	}

	public static function all_id_where($where = "") {
		$liste = static::all_id($where);
		$sep = "";
		$string = "";
		foreach ($liste as $id) {
			$string.= $sep . $id;
			$sep = ",";
		}
		return $string;
	}

	public static function set_filter(&$limit = "") {
		return setFilter($limit);
	}

	/**
	 * baseRequest : retourne la base de requête selon le paramètre passé en entrée
	 * @param string $typerequest [optional] <p>
	 * ce que l'on cherche : par défaut "id", peut être égal à "max" ou "nb"
	 * </p>	 
	 * @return int le nombre d'ids
	 */
	protected static function baseRequest($typerequest = "id") {
		$what = static::identifiant();
		if ($typerequest == "nb") {
			$what = "count(*) as nbr";
		}
		if ($typerequest == "max") {
			$what = "max($what) as max";
		}
		if ($typerequest == "min") {
			$what = "min($what) as min";
		}
		$class_name = static::prefix_bdd() . static::nom_classe_bdd();
		$req = "select $what from " . $class_name;
		return $req;
	}

	/**
	 * nb : retourne le nombre d'id d'une classe avec un appel à la base de donnée
	 * @param string $where [optional] <p>
	 * la clause where de la requête
	 * </p>	 
	 * @return int le nombre d'ids
	 */
	static function nb($where = "") {
		$pdo = static::getPDO();
		$req = static::baseRequest("nb");
		if ($where != "") {
			$req .= " where " . $where;
		}
		$groupBy = static::groupByDefault();
		if ($groupBy) {
			$req = str_ireplace("GROUP BY $groupBy", "", $req);
		}
//		pre($req,"red");

		$select = $pdo->query($req);
		while ($row = $select->fetch()) {
			$nb = $row["nbr"];
		}
		$pdo = null;
		return $nb;
	}

	/**
	 * last_id : retourne le plus grand id d'une classe avec un appel à la base de donnée
	 * @param string $where [optional] <p>
	 * la clause where de la requête
	 * </p>	 
	 * @return int le plus grand id
	 */
	static function last_id($where = "") {
		$pdo = static::getPDO();
		$req = static::baseRequest("max");
		if ($where != "") {
			$req .= " where " . $where;
		}
//		echo $req;
		$select = $pdo->query($req);
		while ($row = $select->fetch()) {
			$nb = $row["max"];
		}
		return $nb;
	}

	//</editor-fold>
	//<editor-fold desc="Gestion des objets">

	/**
	 * __call : Acesseur magique, créée un acesseur sur toutes les variables d'objet d'une classe
	 * @param string $name <p>
	 * le nom de la fonction à appeller
	 * </p>
	 * @param mixed $arguments <p>
	 * les arguments de la méthode
	 * </p>
	 * @return mixed la valeur de retour de la méthode.
	 */
	function __call($name, $arguments) {
		//setteur    	
		if (substr($name, 0, 4) == 'set_') {
			$varname = strtolower(substr($name, 4));
			if (property_exists($this, $varname)) {
				$this->$varname = $arguments[0];
				$this->updateChamps($arguments[0], $varname);
			} else {
				throw new Exception("La variable n'existe pas (input) : " . $varname, 500);
			}
		} elseif (property_exists($this, $name)) { //getteur
			$varname = strtolower($name);
			return $this->{$varname};
		} else {
			throw new Exception('Mauvaise méthode. (gueteur) : ' . $name, 500);
		}
	}

	/**
	 * tabPropValeurs : retourne un tableau associatif avec les propriétés et les valeurs de l'objet en cours (sauf class_name) 
	 */
	function tabPropValeurs() {
		$array = get_object_vars($this);
		unset($array["class_name"]);
		return $array;
	}

	/**
	 * getListeProprietes : retourne un tableau associatif avec les propriétés et leur nom pour l'affichage (méthode héritée) 
	 */
	static function getListeProprietes() {
		$item = new static();
		$liste = array();
		$tabprop = $item->tabPropValeurs();
		foreach ($tabprop as $prop => $value) {
			$liste[$prop] = $prop; //Value est forcément = 0, on le le récupère pas
		}
		return $liste;
	}

	/**
	 * getListeProprietesFormEdit : retourne un tableau associatif avec les propriétés et leur nom pour la page d'édition (méthode héritée) 
	 */
	static function getListeProprietesFormEdit() {
		$item = new static();
		$liste = array();
		$tabprop = $item->tabPropValeurs();
		foreach ($tabprop as $prop => $value) {
			$liste[$prop] = $prop; //Value est forcément = 0, on le le récupère pas
		}
		return $liste;
	}

	/**
	 * value_as_link : retourne un lien html avec la value comme href 
	 */
	static function value_as_link($value) {
		$value = "<a target='_blank' href='$value' >Lien</a>";
		return $value;
	}

	/**
	 * tronquer : tronque le texte html passé en paramètre, en fermant les balises HTML ouvertes
	 * @param string $texte <p>
	 * le texte à tronquer
	 * </p>
	 * @param string $longueur [optional] <p>
	 * le nombre de caractères à partir duqyuel on effectue la césure, par défaut 300 (this is Sparta)
	 * </p>
	 * @param string $with_point [optional] <p>
	 * le texte à afficher après la césure, par défaut [...]
	 * </p>
	 * @param string $last_string [optional] <p>
	 * le dernier caractère à couper, par défaut le premier espace après les $longeur caractères
	 * </p>
	 */
	static function tronquer($texte, $longueur = 300, $with_point = ' [...]', $last_string = ' ') {
		if (is_array($texte)) {
			return $texte;
		}
		// Test si la longueur du texte dépasse la limite
		if (strlen(strip_tags($texte)) > $longueur) {
			// Séléction du maximum de caractères
			$texte = substr($texte, 0, $longueur);
			// Récupération de la position du dernier espace (afin déviter de tronquer un mot)
			$position_espace = strrpos($texte, $last_string);
			$texte = substr($texte, 0, $position_espace);
			// Ajout des "..."
			if ($with_point) {
				$texte = $texte . $with_point;
			}
		}
		//on retourne le texte
		return $texte . static::close_tag_html($texte);
	}

	/**
	 * close_tag_html : ferme les balises html ouvertes du $text passé en paramètre
	 */
	static function close_tag_html($text) {
		if (is_array($text)) {
			return "";
		}
		preg_match_all("/<[^>]*>/", $text, $bal);
		$liste = array();
		foreach ($bal[0] as $balise) {
			if ($balise{1} != "/") { // opening tag
				preg_match("/<([a-z]+[0-9]*)/i", $balise, $type);
				// add the tag
				$liste[] = $type[1];
			} else { // closing tag
				preg_match("/<\/([a-z]+[0-9]*)/i", $balise, $type);
				// strip tag
				for ($i = count($liste) - 1; $i >= 0; $i--) {
					if ($liste[$i] == $type[1])
						$liste[$i] = "";
				}
			}
		}
		$tags = '';
		for ($i = count($liste) - 1; $i >= 0; $i--) {
			if ($liste[$i] != "" && $liste[$i] != "br")
				$tags .= '</' . $liste[$i] . '>';
		}
		return($tags);
	}

	/**
	 * view : affiche le code HTML de la page d'affichage de l'objet
	 */
	function view() {
		return "";
	}

	//</editor-fold>
	//<editor-fold desc="Gestion CSV">
	/**
	 * getListeProprietesExportCsv : retourne un tableau associatif avec les propriétés et leur nom pour l'export CSV (méthode héritée) 
	 */
	static function getListeProprietesExportCsv() {
		return static::getListeProprietes();
	}

	/**
	 * Indique si la classe est exportable en csv
	 */
	static function exportable() {
		return false;
	}

	/**
	 * headersCSV : retourne la première ligne du csv avec les légendes
	 * @param array $do_not_display [optional] <p>
	 * Les éléments que l'on ne sohaite pas afficher
	 * </p>	 
	 * @return string le texte au format csv
	 */
	static function headersCSV($do_not_display = array(), $display_only = array()) {
		if ($display_only == array()) {
			$display_only = static::getListeProprietesExportCsv();
		}
		$header = array();
		foreach ($display_only as $prop => $nom_prop) {
			if (!in_array($prop, $do_not_display)) {
				$header[] = $nom_prop;
			}
		}
		return $header;
	}

	/**
	 * valuesCSV : retourne la ligne de l'objet en cours au format CSV
	 * @param array $do_not_display [optional] <p>
	 * Les éléments que l'on ne sohaite pas afficher
	 * </p>	 
	 * @return string le texte au format csv
	 */
	function valuesCSV($do_not_display = array(), $display_only = array()) {
		if ($display_only == array()) {
			$display_only = static::getListeProprietesExportCsv();
		}
		$corps = array();
		foreach ($display_only as $prop => $nom_prop) {
			if (!in_array($prop, $do_not_display)) {
				$value = utf8_decode($this->$prop());
				$corps[] .= $value;
			}
		}
		return $corps;
	}

	static function writeCSV($where, $do_not_display = array()) {
		$fp = fopen($where, 'w');
		fputcsv($fp, static::headersCSV($do_not_display));
		global $WHERE_WRITECSV;
		$where = static::whereDefault();
		$where .= " $WHERE_WRITECSV";
		foreach (static::all_id($where) as $id) {
			$item = new static($id);
			fputcsv($fp, $item->valuesCSV($do_not_display));
		}
	}

	//</editor-fold>
	//<editor-fold desc="Gestion des images">

	static function display_image($src, $width = 149, $height = 80) {
		$class_name = static::nom_classe_bdd();
		if ($src == "") {
			$height = 80;
			$src = "http://placehold.it/" . $width . "x$height";
		}
		$strwidth = "width='$width'";
		$strheight = "height='$height'";
		if ($width == null) {
			$strwidth = "";
		}
		if ($height == null) {
			$strheight = "";
		}

		$img = "<img alt='' src='$src' $strwidth $strheight />";
		return $img;
	}

	//</editor-fold>
	//<editor-fold desc="Wordpress">

	/**
	 * Indique si on utilise wordpress
	 * @return boolean
	 */
	static function isWordpress() {
		return true;
	}

	static function wp_capability() {
		return false;
	}

	static function wp_capability_edit() {
		return false;
	}

	static function pathToPlugin() {
		$path = dirname(__DIR__);
		return $path;
	}

	static function pathToAdmin() {
		$path = dirname(__DIR__) . "/admin";
		return $path;
	}

	static function plugin_list() {
		$nom_classe = get_called_class();
		$path = static::pathToAdmin();
		if (static::display_filter()) {
			$identifiant = static::identifiant();
			if (!isset($_REQUEST["filter"])) {
				$_REQUEST["filter"] = $identifiant . '$LIKE$';
			}
		}

		include($path . "/list.php");
	}

	static function plugin_edit() {
		$nom_classe = get_called_class();
		$path = static::pathToAdmin();

		include($path . "/edit.php");
	}

	static function wp_min_role() {
		return Bii_min_role;
	}

	static function wp_dashboard_page() {
		return Bii_dashboard_page;
	}

	static function wp_slug_menu() {
		return Bii_plugin_slug;
	}

	static function wp_titre_menu() {
		return Bii_menu_title;
	}

	static function wp_dashicon_menu() {
		return Bii_dashicon_menu;
	}

	static function wp_nom_menu() {
		return Bii_plugin_name;
	}

	static function wp_nom_plugin() {
		return Bii_plugin_name;
	}

	static function displaySousMenu($role = 'publish_pages', $hide_edit = true) {
		$nom_menu = static::wp_nom_menu();
		$nomListe = static::titre_page_admin_liste();
		$nom = ucfirst(static::nom_classe_admin());
		$slugListe = static::nom_classe_bdd() . "_list";
		$method_list = get_called_class() . "::plugin_list";
		add_submenu_page($nom_menu, __($nomListe), __($nom), $role, $slugListe, $method_list);
		if (static::editable()) {
			$slugEdit = static::nom_classe_bdd() . "_edit";
			$method_edit = get_called_class() . "::plugin_edit";
			if ($hide_edit) {
				$nom_menu.= "-hidden";
			}
			add_submenu_page($nom_menu, __('Ajouter Modifier'), __('&nbsp;&nbsp;Ajouter'), $role, $slugEdit, $method_edit);
		}
	}

	/**
	 * Indique le nom du titre du menu dans wordpress
	 * @return boolean
	 */
	static function wp_title_menu() {
		return ucwords(static::nom_classe_admin());
	}

	static function classe_heritee() {
		return "global_class";
	}

	//</editor-fold>
}
