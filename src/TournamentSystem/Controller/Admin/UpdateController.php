<?php

namespace TournamentSystem\Controller\Admin;

use TournamentSystem\Controller\Controller;
use TournamentSystem\Controller\LoginRequired;
use TournamentSystem\View\Admin\UpdateView;

class UpdateController extends Controller {
	
	#[LoginRequired]
	protected function get(): int {
		parent::render(new UpdateView());
		
		return parent::OK;
	}
}
