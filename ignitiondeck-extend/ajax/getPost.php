<?php

ini_set('display_errors', '1');

$post = $meta = $reponse = [];
if (isset($_REQUEST["id"])) {
	$id = $_REQUEST["id"];
	$reponse[] = getReponse($id);
}
if (isset($_REQUEST["list_id"])) {
	$list_id_str = $_REQUEST["list_id"];
	$list_id = explode(",", $list_id_str);
	foreach ($list_id as $id) {
		$reponse[] = getReponse($id);
	}
}


echo json_encode($reponse);

function getReponse($id) {
	$post = get_post($id, ARRAY_A);
	$meta = get_post_meta($id);
	$post["meta"] = $meta;
	return $post;
}
