<?php

namespace TournamentSystem\Controller\Admin;

use TournamentSystem\Controller\Controller;
use TournamentSystem\View\Admin\LoginView;

class LoginController extends Controller {
	
	public function __construct() {
		parent::__construct('SELECT password FROM tournament_user WHERE name=?');
	}
	
	protected function get(): int {
		if(session_exists()) {
			return parent::SEE_OTHER('/admin/dashboard/');
		}
		
		parent::render(new LoginView());
		
		return parent::OK;
	}
	
	protected function post(): int {
		$username = $_POST['username'];
		
		$this->stmt[0]->bind_param('s', $username);
		$this->stmt[0]->execute();
		
		if($result = $this->stmt[0]->get_result()) {
			if($hash = $result->fetch_row()) {
				$hash = $hash[0];
				
				if(password_verify($_POST['password'], $hash)) {
					$result->free();
					
					session_start();
					$_SESSION['user'] = $username;
					
					return parent::SEE_OTHER('/admin/dashboard/');
				}
			}
			
			$result->free();
		}
		
		parent::render(new LoginView(true));
		
		return parent::OK;
	}
}
