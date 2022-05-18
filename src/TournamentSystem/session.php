<?php

session_set_cookie_params([
	'httponly' => true,
	'samesite' => 'strict'
]);

function session_exists(): bool {
	if(!isset($_COOKIE[session_name()])) {
		return false;
	}
	if(session_status() === PHP_SESSION_ACTIVE) {
		return true;
	}
	if(session_start()) {
		return isset($_SESSION['user']);
	}
	
	return false;
}

function session_stop(): void {
	session_destroy();
	
	$options = session_get_cookie_params();
	$options['expires'] = 1;
	
	unset($options['lifetime']);
	
	setcookie(session_name(), '', $options);
}
