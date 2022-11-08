// Решение тестовой задачи на PHP

<?php

if (! function_exists("array_key_last")) {
    function array_key_last($array) {
        if (!is_array($array) || empty($array)) {
            return NULL;
        }

        return array_keys($array)[count($array)-1];
    }
}

function slash_search($s, $ind=[]) {
	// функция для поиска индексов расположения "\" в строке

	if ((count($ind) > 0) && ($ind[array_key_last($ind)] != count($ind)-1)) {
		$search_index = $ind[array_key_last($ind)] + 1;
	} else {
		$search_index = 0;
	}

	$res = strpos($s, "\\", $search_index);

	if ($res === false) {
		return $ind;
	} else {
		array_push($ind, $res);
        return slash_search($s, $ind);
	}
}

function string_extend($string) {
	// функция расширения строки

	if (is_numeric($string)) {
		throw new Exception("Несоответствующее значение");
	} else if ($string == "" || ctype_alpha($string)) {
		return $string;
	} else {
        $slash_indices = slash_search($string);

        $new_string = "";
        $start_ind = 0;

        preg_match_all('/\d/', $string, $matches, PREG_OFFSET_CAPTURE);

        foreach ($matches[0] as $m) {
        	$ind = $m[1];
        	if (in_array($ind-1, $slash_indices)) {
        		if (!in_array($ind-2, $slash_indices)) {
        			$new_string = $new_string . substr($string, $start_ind, $ind - $start_ind - 1);
                    $start_ind = $ind;
        		} else if (in_array($ind-2, $slash_indices)) {
                    $new_string = $new_string . substr($string, $start_ind, $ind - $start_ind - 1) . str_repeat($string[$ind-1], (int)$m[0]-1);
                    $start_ind = $ind + 1;
                }
        	} else {
        		if ($string[$ind-1] and $ind-1 >= 0) {
        			$new_string = $new_string . substr($string, $start_ind, $ind - $start_ind) . str_repeat($string[$ind-1], (int)$m[0]-1);
        			$start_ind = $ind + 1;
        		}
        	}
        }

        if ($start_ind < strlen($string)) {
        	$new_string = $new_string . substr($string, $start_ind);
        }

		return $new_string;
	}
}

// $string = "v4bc3d5e";
// $string = "abcd";
// $string = "45";
// $string = "";
// $string = "qwe\\4\\5";
// $string = "qwe\\45";
$string = "qwe\\\\5";

print_r(string_extend($string));
