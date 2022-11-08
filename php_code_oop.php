<?php

abstract class Device {
	public $device_name,
	$processed_result;

	function __construct($device_name) {
		$this->device_name = $device_name;
	}

	abstract function processData($input_data);
}

class Receiver extends Device {
	function __construct($device_name) {
		parent::__construct($device_name);
	}

	function processData($input_data) {
		// здесь я хотела сделать экранирование слэшей

		// но у меня так и не получилось добиться того, чтобы "\\" воспринимались
		// как 2 отдельных слэша, не знаю, возможно ли такое вообще

		// $input_data = preg_quote($input_data);

		$this->processed_result = $input_data;
	}
}

class Processor extends Device {
	function __construct($device_name) {
		parent::__construct($device_name);
	}

	function processData($input_data) {
		//функция расширения строки

		if (! function_exists("array_key_last")) {
			function array_key_last($array) {
				if (!is_array($array) || empty($array)) {
					return NULL;
				}

				return array_keys($array)[count($array)-1];
			}
		}

		function slash_search($s, $ind = []) {
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

		if (is_numeric($input_data)) {
			throw new Exception("Несоответствующее значение");
		} else if ($input_data == "" || ctype_alpha($input_data)) {
			$result = $input_data;
		} else {
			$slash_indices = slash_search($input_data);

			$result = "";
			$start_ind = 0;

			preg_match_all('/\d/', $input_data, $matches, PREG_OFFSET_CAPTURE);		//указывает позицию в байтах, для русских символов не подходит

			foreach ($matches[0] as $m) {
				$ind = $m[1];
				if (in_array($ind-1, $slash_indices)) {
					if (!in_array($ind-2, $slash_indices)) {
						$result = $result . mb_substr($input_data, $start_ind, $ind - $start_ind - 1, 'utf-8');
						$start_ind = $ind;
					} else if (in_array($ind-2, $slash_indices)) {
						$repeated_symbol = mb_substr($input_data, $ind-1, 1, 'utf-8');

						$result = $result . mb_substr($input_data, $start_ind, $ind - $start_ind - 1, 'utf-8') . str_repeat($repeated_symbol, (int)$m[0]-1);
						$start_ind = $ind + 1;
					}
				} else {
					if ($ind == 0) {
						$result = $result . mb_substr($input_data, $start_ind, $ind - $start_ind, 'utf-8');
						$start_ind = $ind + 1;
					} else if ($ind-1 >= 0) {

						$repeated_symbol = mb_substr($input_data, $ind-1, 1, 'utf-8');
						print_r("{$repeated_symbol}\n");

						$result = $result . mb_substr($input_data, $start_ind, $ind - $start_ind, 'utf-8') . str_repeat($repeated_symbol, (int)$m[0]-1);
						$start_ind = $ind + 1;
					}
				}
			}

			if ($start_ind < mb_strlen($input_data, 'utf-8')) {
				$result = $result . mb_substr($input_data, $start_ind, null, 'utf-8');
			}

			$this->processed_result = $result;
		}
	}
}

class Printer extends Device {
	function __construct($device_name) {
		parent::__construct($device_name);
	}

	function processData($input_data) {
		//вывод результата
		$this->processed_result = $input_data;

		echo "Результат обработки: {$this->processed_result}";
	}
}

// $string = 'v4bc3d5e';
// $string = 'abcd';
// $string = '45';
// $string = '';
// $string = 'qwe\4\5';
// $string = 'qwe\45';
$string = 'qwe\\\5';
// $string = '2v4bc3d5e';
// $string = 'абв5';

$my_receiver = new Receiver('My handy receiver');
$my_receiver->processData($string);

$my_processor = new Processor(('My smart processor'));
$my_processor->processData($my_receiver->processed_result);

$my_printer = new Printer(('My little printer'));
$my_printer->processData($my_processor->processed_result);
