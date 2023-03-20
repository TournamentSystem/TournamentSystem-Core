<?php

if(!function_exists('str_starts_with')) {
	function str_starts_with(string $haystack, string $needle): bool {
		$length = strlen($needle);
		
		return substr($haystack, 0, $length) === $needle;
	}
}
if(!function_exists('str_ends_with')) {
	function str_ends_with(string $haystack, string $needle): bool {
		$length = strlen($needle);
		
		if(!$length) {
			return true;
		}
		
		return substr($haystack, -$length) === $needle;
	}
}

if(!function_exists('array_is_list')) {
	function array_is_list(array $array): bool {
		return array_keys($array) === range(0, count($array) - 1);
	}
}
function is_assoc(mixed $array): bool {
	if(!is_array($array)) return false;
	
	return !array_is_list($array);
}
