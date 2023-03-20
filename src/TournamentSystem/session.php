<?php

session_set_cookie_params([
	'httponly' => true,
	'samesite' => 'strict'
]);


function session_cookie_exists(): bool {
	return isset($_COOKIE[session_name()]);
}

function session_exists(): bool {
	if(!session_cookie_exists()) {
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

function session_init($user): void {
	$_SESSION['user'] = $user;
	$_SESSION['user_agent'] = md5($_SERVER['HTTP_USER_AGENT']);
}

function session_verify(): bool {
	if(md5($_SERVER['HTTP_USER_AGENT']) !== $_SESSION['user_agent']) {
		session_stop();
		return false;
	}
	
	return true;
}
