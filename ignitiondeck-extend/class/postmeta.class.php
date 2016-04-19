<?php

class postmeta extends global_class {

	protected $meta_id;
	protected $post_id;
	protected $meta_key;
	protected $meta_value;

	public static function identifiant() {
		return "meta_id";
	}

	public static function add($post_id, $key, $value, $replace = true) {

		$id = 0;
		if (!static::exists($post_id, $key)) {
			$id = static::insertDefault($post_id, $key, $value);
		} else {
			$id = static::from_id_key($post_id, $key);
			if ($replace) {
				$value = ["post_id" => $post_id, "meta_key" => $key, "meta_value" => $value];
				pre($value, "orange");
				$item = new static($id);
				$item->updateChamps($value);
			}
		}
		return $id;
	}

	public static function insertDefault($post_id, $key, $value) {
		$values = ["post_id" => $post_id, "meta_key" => $key, "meta_value" => $value];
pre($values, "green");
		$item = new static();
		$item->insert();
		$item->updateChamps($values);
		return $item->id();
	}

	public static function exists($post_id, $key) {
		if ($key != "REAL_HOMES_property_images") {
			$bool = (bool) static::nb("post_id = '$post_id' AND meta_key = '$key'");
			return $bool;
		} else {
			return false;
		}
	}

	public static function from_id_key($post_id, $key) {
		$liste = static::all_id("post_id = '$post_id' AND meta_key = '$key'");
		foreach ($liste as $id) {
			return $id;
		}
	}

}
