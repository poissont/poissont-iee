<?php

if (isset($_REQUEST["option"])) {
	$option = $_REQUEST["option"];
	if (isset($_REQUEST["newval"])) {
		$val = $_REQUEST["newval"];
		update_option($option, $val);
		pre([$option=>$val]);
	}
}
