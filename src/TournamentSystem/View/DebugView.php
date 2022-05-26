<?php

namespace TournamentSystem\View;

class DebugView extends View {
	
	public function __construct() {
		parent::__construct('Debug View', 'debug');
	}
	
	public function render(): void {
		$isLocal = $_SERVER['REMOTE_ADDR'] === '127.0.0.1';
		
		ob_start();
		
		if($isLocal) {
			var_dump($GLOBALS);
		}else {
			var_dump([
				'_GET' => $_GET,
				'_POST' => $_POST,
				'_COOKIE' => $_COOKIE,
				'_FILES' => $_FILES,
				'_REQUEST' => $_REQUEST,
				'_SERVER' => $_SERVER,
			]);
		}
		$dump = ob_get_clean();
		
		ob_end_clean();
		
		parent::renderView("<p>GLOBALS :</p>$dump");
	}
}
