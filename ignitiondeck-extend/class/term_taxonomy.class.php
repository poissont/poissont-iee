<?php

class term_taxonomy extends global_class {

	protected $term_taxonomy_id;
	protected $term_id;
	protected $taxonomy;
	protected $description;
	protected $parent;
	protected $count;

	public static function identifiant() {
		return "term_taxonomy_id";
	}

	public static function add($term_id, $taxonomy) {
		$values = ["term_id" => $term_id, "taxonomy" => $taxonomy];
		if (!static::exists($term_id)) {
			$item = new static();
			$item->insert();
		} else {
			$item = static::from_term_id($term_id);
			unset($values["term_id"]);
		}
		$item->updateChamps($values);
	}

	public static function addPropertyLocation($term_id) {
		static::add($term_id, "property-location");
	}

	public static function addPropertyType($term_id) {
		static::add($term_id, "property-type");
	}

	public static function exists($term_id) {
		return (bool) static::nb("term_id = '$term_id'");
	}

	public static function from_term_id($term_id) {
		$liste = static::all_id("term_id = '$term_id'");
		$id_item = 0;
		foreach ($liste as $id) {
			$id_item = $id;
		}
		return new static($id_item);
	}

	public function get_term() {
		if (!isset($this->term)) {
			$this->term = new terms($this->term_id);
		}
		return $this->term;
	}

	public function option_value() {
		return $this->get_term()->name();
	}
	
	public static function get_term_ids($where){
		$liste = static::all_id($where);
		$lister = [];
		foreach($liste as $id){
			$item = new static($id);
			$lister[] = $item->term_id();
		}
		return $lister;
	}

}
