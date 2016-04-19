<?php
class terms extends global_class {
	protected $term_id;
	protected $name;
	protected $slug;
	protected $term_group;
	
	
	public static function identifiant() {
		return "term_id";
	}
	
	public static function add($name){
		$slug = stripAccentsLiens($name);
		$id = 0;
		if(!static::exists($slug)){
			$id = static::insertDefault($name, $slug);
		}else{
			$id = static::from_slug($slug);
		}
		return $id;
	}
	
	public static function insertDefault($name,$slug){
		$values = ["name"=>$name,"slug"=>$slug,"term_group"=>0];
		$item = new static();
		$item->insert();
		$item->updateChamps($values);
		return $item->id();
	}
	
	public static function exists($slug){
		return (bool)static::nb("slug = '$slug'");
	}
	
	public static function from_slug($slug){
		$liste = static::all_id("slug = '$slug'");
		foreach($liste as $id){
			return $id;
		}
	}
	
	public static function addVille($name){
		$term_id = static::add($name);
		term_taxonomy::addPropertyLocation($term_id);
		return $term_id;
	}
	public static function addType($name){
		$term_id = static::add($name);
		term_taxonomy::addPropertyType($term_id);
		return $term_id;
	}
	
	public function option_value(){
		return $this->name;
	}
	 
	public static function array_slug_name($where){
		$liste = static::all_id($where);
		$r = [];
		foreach($liste as $id){
			$item = new static($id);
			$r[$item->slug] = $item->name;
		}
		return $r;
	}
}


