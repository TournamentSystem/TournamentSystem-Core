<?php

namespace TournamentSystem\Controller\Admin;

use TournamentSystem\Controller\Controller;
use TournamentSystem\Controller\LoginRequired;
use TournamentSystem\View\Admin\DashboardView;

class DashboardController extends Controller {
	
	#[LoginRequired]
	protected function get(): int {
		parent::render(new DashboardView());
		
		return parent::OK;
	}
}
