<?php

namespace TournamentSystem\Controller\Admin;

use TournamentSystem\Controller\Controller;
use TournamentSystem\Controller\LoginRequired;

class LogoutController extends Controller {

	#[LoginRequired]
	protected function get(): int {
		session_stop();

		return parent::SEE_OTHER('/');
	}
}
