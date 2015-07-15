<?php

function array_parse($items, $indentation = 1) {
	echo "[ <br />";
	foreach($items as $key => $value) {
		for($i = 0; $i<$indentation; $i++)
			echo "&nbsp;&nbsp;&nbsp;&nbsp;";
		if(is_array($value)) {
			echo "$key ";
			array_parse($value, $indentation + 1);
		}
		else {
			echo "$key => $value <br />";
		}
	}
	for($i = 0; $i<$indentation-1; $i++) {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
	}
	echo "] <br />";
}

function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        // Le modifieur 'G' est disponible depuis PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}

?>
