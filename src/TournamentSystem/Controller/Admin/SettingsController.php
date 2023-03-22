<?php

namespace TournamentSystem\Controller\Admin;

use TournamentSystem\Controller\Controller;
use TournamentSystem\Controller\LoginRequired;
use TournamentSystem\Module\DummySettings;
use TournamentSystem\Module\Module;
use TournamentSystem\View\Admin\SettingsView;

class SettingsController extends Controller {

	#[LoginRequired]
	protected function get(): int {
		global $_TS;

		$settings = new DummySettings(Module::load($_TS['params']['module']));

		$this->render(new SettingsView($settings));

		return parent::OK;
	}
}
