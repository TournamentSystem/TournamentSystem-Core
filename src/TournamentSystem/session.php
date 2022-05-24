<?php

session_set_cookie_params([
	'httponly' => true,
	'samesite' => 'strict'
]);

function session_exists(): bool {
	if(!isset($_COOKIE[session_name()])) {
		return false;
	}
	
	$status = session_status();
	if($status === PHP_SESSION_DISABLED) {
		return false;
	}
	if($status === PHP_SESSION_NONE && !session_start()) {
		return false;
	}
	
	return isset($_SESSION['user']);
}

function session_stop(): void {
	session_destroy();
	
	$options = session_get_cookie_params();
	$options['expires'] = 1;
	
	unset($options['lifetime']);
	
	setcookie(session_name(), '', $options);
}
