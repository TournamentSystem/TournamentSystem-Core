<?php

namespace TournamentSystem\Controller\Admin;

use TournamentSystem\Controller\Controller;

class ModulesController extends Controller {
	
	public function __construct() {
		parent::__construct('SELECT * FROM tournament_modules');
	}
	
	protected function get(): int {
		return parent::get();
	}
}