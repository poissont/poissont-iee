<?php

class bii_ID_FES extends ID_FES {

	protected $formshort = false;

	function not_display_in_short() {
		return [
//			"project_category", "project_faq", "project_image2", "project_image3", "project_image4",
//			"project_levels", "project_level_title[]", "project_level_price[]", "project_level_limit[]","project_updates",
//			"project_fund_type[]", "level_description[]", "level_long_description[]", "project_short_description", "project_end_type","project_fesave"
		];
	}

	function form_short() {
		if (!$this->formshort) {
			$this->genformshort();
		}
		return $this->formshort;
	}

	function genformshort() {
		$form = $this->form;
//		pre($form,"red");
		$project_end = [
			"name" => "project_end_type", "value" => "closed", "type" => "hidden", "wclass" => "hide",
		];
		$formshort = [$project_end];
		$haystack = $this->not_display_in_short();
		$div_open_count = 0;
		foreach ($form as $item) {
			$caninsert = false;
			if ((!isset($item["name"])) || (!in_array($item["name"], $haystack))) {
				$caninsert = true;
				if ($item["type"] == "wpeditor") {
					$item["type"] = "textarea";
				}
				if ($item["name"] == "project_fesubmit") {
					$item["value"] = "Enregistrer votre projet !";
				}
			}
//				if (isset($item["before"]) && (strpos($item["before"],"fin de campagne"  )!== false)) {
//					$item["before"] = "<!--".$item["before"]."-->";
//					$div_open_count += 2;
//				}
//				if($div_open_count && isset($item["before"]) && $item["before"] == "</div>" ){
//					$item["before"] = "<!--</div>-->";
//					--$div_open_count;
//				}
			if ($caninsert) {
				$formshort[] = $item;
			}
		}

		$this->formshort = $formshort;
	}

	function display_form_short() {
//		$id_form = new bii_ID_Form($this->form_short());
//
//		$output = $id_form->build_form($this->vars);
//		return $output;
		ob_start();
		if (isset($_GET["numero_projet"])) {
			$np = $_GET["numero_projet"];
			$bp = bii_project::fromIdPost($np);			
		}else{
			$bp = new bii_project();			
		}
		$bp->form_edit_front();
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}

}

class bii_ID_Form extends ID_Form {

	function build_form($vars = null) {
		$output = '<ul>';
//		pre($this->fields);
		foreach ($this->fields as $field) {
			$misc = $type = $name = $label = '';
			$value = $options = $wclass = $id = null;
			if (isset($field['id'])) {
				$id = $field['id'];
				$class = $id;
			}
			if (isset($field['label'])) {
				$label = $field['label'];
			}
			if (isset($field['name'])) {
				$name = $field['name'];
			}
			if (isset($field['wclass'])) {
				$wclass = $field['wclass'];
			}
			if (isset($field['class'])) {
				$class = $field['class'];
			}
			if (isset($field['type'])) {
				$type = $field['type'];
			}
			if (isset($field['options'])) {
				$options = $field['options'];
			}
			if (isset($field['value'])) {
				$value = apply_filters('idcf_fes_' . $name . '_value', $field['value'], (isset($vars['post_id']) ? $vars['post_id'] : null));
			}
			if (isset($field['misc'])) {
				$misc = $field['misc'];
			}
			// Start Building
			ob_start();
			$post_id = (isset($vars['post_id']) ? $vars['post_id'] : null);
			do_action('fes_' . $name . '_before', $post_id);
			$output .= ob_get_contents();
			ob_end_clean();
			if (isset($field['before'])) {
				$output .= $field['before'];
			}
			$output .= '<li ' . (isset($wclass) ? 'class="' . $wclass . '"' : '') . '>';
			switch ($type) {
				case 'text':
					if (!empty($label)) {
						$output .= '<p><label for="' . $id . '">' . apply_filters('fes_' . $name . '_label', $label) . '</label>';
					}
					$output .= '<input type="text" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $value . '" ' . $misc . '/>';
					if (!empty($label)) {
						$output .= '</p>';
					}
					break;
				case 'email':
					if (!empty($label)) {
						$output .= '<p><label for="' . $id . '">' . apply_filters('fes_' . $name . '_label', $label) . '</label>';
					}
					$output .= '<input type="email" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $value . '" ' . $misc . '/>';
					if (!empty($label)) {
						$output .= '</p>';
					}
					break;
				case 'number':
					if (!empty($label)) {
						$output .= '<p><label for="' . $id . '">' . apply_filters('fes_' . $name . '_label', $label) . '</label>';
					}
					$output .= '<input type="number" id="' . $id . '" name="' . $name . '" class="' . $class . ' number-field" value="' . ((!empty($value)) ? $value : 0) . '" ' . $misc . '/>';
					if (!empty($label)) {
						$output .= '</p>';
					}
					break;
				case 'password':
					if (!empty($label)) {
						$output .= '<p><label for="' . $id . '">' . apply_filters('fes_' . $name . '_label', $label) . '</label>';
					}
					$output .= '<input type="password" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $value . '" ' . $misc . '/>';
					if (!empty($label)) {
						$output .= '</p>';
					}
					break;
				case 'file':
					if (!empty($label)) {
						$output .= '<p><label for="' . $id . '">' . apply_filters('fes_' . $name . '_label', $label) . '</label>';
					}
					$output .= '<input type="file" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $value . '" ' . $misc . '/>';
					if (!empty($label)) {
						$output .= '</p>';
					}
					break;
				case 'date':
					if (!empty($label)) {
						$output .= '<p><label for="' . $id . '">' . apply_filters('fes_' . $name . '_label', $label) . '</label>';
					}
					$output .= '<input type="date" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $value . '" ' . $misc . '/>';
					if (!empty($label)) {
						$output .= '</p>';
					}
					break;
				case 'tel':
					if (!empty($label)) {
						$output .= '<p><label for="' . $id . '">' . apply_filters('fes_' . $name . '_label', $label) . '</label>';
					}
					$output .= '<input type="tel" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $value . '" ' . $misc . '/>';
					if (!empty($label)) {
						$output .= '</p>';
					}
					break;
				case 'hidden':
					$output .= '<input type="hidden" id="' . $id . '" name="' . $name . '" value="' . $value . '" ' . $misc . '/>';
					break;
				case 'select':
					if (!empty($label)) {
						$output .= '<p><label for="' . $id . '">' . apply_filters('fes_' . $name . '_label', $label) . '</label>';
					}
					$output .= '<select id="' . $id . '" name="' . $name . '" class="' . $class . '" >';
					foreach ($options as $option) {
						$output .= '<option value="' . $option['value'] . '" ' . ($option['value'] == $value ? 'selected="selected"' : '') . ' ' . $misc . ' ' . (isset($option['misc']) ? $option['misc'] : '') . '>' . $option['title'] . '</option>';
					}
					$output .='</select></p>';
					break;
				case 'checkbox':
					$output .= '<input type="checkbox" id="' . $id . '" name="' . $name . '" class="' . $class . '"  value="' . $value . '" ' . $misc . '/>';
					if (!empty($label)) {
						$output .= '<label for="' . $id . '">' . apply_filters('fes_' . $name . '_label', $label) . '</label>';
					}
					break;
				case 'radio':
					$output .= '<input type="radio" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $value . '" ' . $misc . '/>';
					if (!empty($label)) {
						$output .= ' <label for="' . $id . '">' . apply_filters('fes_' . $name . '_label', $label) . '</label>';
					}
					break;
				case 'textarea':
					if (!empty($label)) {
						$output .= '<p><label for="' . $id . '">' . apply_filters('fes_' . $name . '_label', $label) . '</label>';
					}
					$output .= '<textarea id="' . $id . '" name="' . $name . '" class="' . $class . '" ' . $misc . '>' . $value . '</textarea>';
					if (!empty($label)) {
						$output .= '</p>';
					}
					break;
				case 'wpeditor':
					ob_start();
					if (!empty($label)) {
						echo '<p><label for="' . $id . '">' . apply_filters('fes_' . $name . '_label', $label) . '</label></p>';
					}
					wp_editor(html_entity_decode($value), $id, array('editor_class' => $class, 'textarea_name' => $name, 'media_buttons' => 1, 'textarea_rows' => 6));
					/* if (!empty($label)) {
					  echo '</p>';
					  } */
					$output .= ob_get_contents();
					ob_end_clean();
					break;
				case 'submit':
					$output .= '<p><input type="submit" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $value . '"/>';
					if (!empty($label)) {
						$output .= '</p>';
					}
					break;
			}
			$output .= '</li>';
			if (isset($field['after'])) {
				$output .= $field['after'];
			}
			ob_start();
			do_action('fes_' . $name . '_after', $post_id);
			$output .= ob_get_contents();
			ob_end_clean();
		}
		$output .= '</ul>';
		return $output;
	}

	

}
