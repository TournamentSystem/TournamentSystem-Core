<?php

namespace TournamentSystem\Controller\Admin;

use TournamentSystem\Controller\Controller;
use TournamentSystem\Controller\LoginRequired;
use TournamentSystem\View\Admin\InstallView;

class InstallController extends Controller {
	
	#[LoginRequired]
	protected function get(): int {
		parent::render(new InstallView());
		
		return parent::OK;
	}
	
	#[LoginRequired]
	protected function post(): int {
		return parent::OK;
	}
}
