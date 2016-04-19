<?php

class wpdbExtended extends wpdb {

	public function __construct(wpdb $wpdbItem) {
		$dbuser = $wpdbItem->dbuser;
		$dbpassword = $wpdbItem->dbpassword;
		$dbname = $wpdbItem->dbname;
		$dbhost = $wpdbItem->dbhost;

		parent::__construct($dbuser, $dbpassword, $dbname, $dbhost);
	}

	public function connexionArray() {
		$connexion = array();
		$connexion["host"] = $this->dbhost;
		$connexion["name"] = $this->dbname;
		$connexion["user"] = $this->dbuser;
		$connexion["pwd"] = $this->dbpassword;
		return $connexion;
	}

}

class cpdo {
//Base de données des communes
	private static $instance = null;

	public static function getInstance() {
		//singleton
		if (!self::$instance) {
			$rpdo_host = "localhost";
			$rpdo_name = "jador2";
			$rpdo_user = "jador";
			$rpdo_pwd = "GJqcTCp4";

			$db = new PDO('mysql:host=' . $rpdo_host . ';dbname=' . $rpdo_name, $rpdo_user, $rpdo_pwd);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			self::$instance = $db;
		}
		return self::$instance;
	}

}

class ppdo {
//Base de données du plugin biiproducts
	private static $instance = null;

	public static function getInstance() {
		//singleton
		if (!self::$instance) {

			$rpdo_host = "localhost";
			$rpdo_name = "lhavrais2";
			$rpdo_user = "lhavrais";
			$rpdo_pwd = "R6cyMLv9";


			$db = new PDO('mysql:host=' . $rpdo_host . ';dbname=' . $rpdo_name, $rpdo_user, $rpdo_pwd);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			self::$instance = $db;
		}
		return self::$instance;
	}

}

class rpdo {
//Base de connées du site
	private static $instance = null;

	public static function getInstance() {
		//singleton
		if (!self::$instance) {

			global $wpdb;
			$wpextended = new wpdbExtended($wpdb);
			$connexionArray = $wpextended->connexionArray();

			$rpdo_host = $connexionArray["host"];
			$rpdo_name = $connexionArray["name"];
			$rpdo_user = $connexionArray["user"];
			$rpdo_pwd = $connexionArray["pwd"];

			$db = new PDO('mysql:host=' . $rpdo_host . ';dbname=' . $rpdo_name, $rpdo_user, $rpdo_pwd);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

			self::$instance = $db;
		}
		return self::$instance;
	}

	public static function getConnexionVendeur($vendeur) {
		$db = new PDO('mysql:host=' . $vendeur->pdo_host() . ';dbname=' . $vendeur->pdo_name(), $vendeur->pdo_user(), $vendeur->pdo_pwd());
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

		return $db;
	}

}