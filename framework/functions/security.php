<?php

function checkPermission($f) {
	if(fileperms($f) & 0x0007) {
		$isDir = (is_dir($f) ? "folder" : "file");
		echo "Please unset all world-permissions on " . $isDir . " " . $f . " : ";

		if(fileperms($f) & 0x0004)
			echo ucwords($isDir) . " is world readable. ";
		if(fileperms($f) & 0x0002)
			echo ucwords($isDir) . " is world writable. ";
		if(fileperms($f) & 0x0001)
			echo ucwords($isDir) . " is world executable. ";
		
		echo "<br />";
		return false;
	}
	return true;
}

function localSecurityChecks($folder) {
	global $successful_Check;
	foreach(glob($folder . "*") as $element) {
		$res = checkPermission($element);
		$successful_Check = ($res ? $successful_Check : false);
		if (is_dir($element))
			localSecurityChecks($element . "/");
	}

	
}

function php_iniChecks() {
	global $successful_Check;
	
	$checks = array(
		'display_errors'			=> false,
		'expose_php'				=> false,
		'allow_url_include'			=> false,
		'allow_url_fopen'			=> false,
		'sql.safe_mode'				=> true,
		'post_max_size'				=> 1024,
		'max_execution_time'		=> 30,
		'max_input_time'			=> 30,
		'memory_limit'				=> 40*1024*1024,
		'register_globals'			=> false,
		'session.cookie_httponly'	=> true,
		'session.hash_function'		=> true
		); 


	foreach ($checks as $key => $value) {
		if(is_bool($value) && (bool)ini_get($key) !== $value) {
			$successful_Check = false;
			echo "The directive " . $key . " is set to <b>" . ($value ? "false" : "true") . "</b>. You should set it to <b>" . ($value ? "true" : "false") . "</b>. <br />";
		}
		elseif(is_int($value) && intval(return_bytes(ini_get($key))) > $value) {
			$successful_Check = false;
			echo "The directive " . $key . " is set to <b>" . intval(return_bytes(ini_get($key))) .  "</b>. You should set it to <b>" . $value . "</b>. <br />";
		}
	}
}

?>
