<?php

namespace TournamentSystem\Controller\Admin;

use TournamentSystem\Controller\Controller;
use TournamentSystem\Controller\LoginRequired;
use TournamentSystem\Module\Module;
use TournamentSystem\View\Admin\ModulesView;

class ModulesController extends Controller {

	public function __construct() {
		parent::__construct([
			'SELECT * FROM tournament_modules',
			'INSERT INTO tournament_modules(name, module) VALUES(?, ?)',
			'DELETE FROM tournament_modules WHERE name=?',
		]);
	}

	#[LoginRequired]
	protected function get(): int {
		$this->stmt[0]->execute();

		if($result = $this->stmt[0]->get_result()) {
			$modules = [];

			foreach($result->fetch_all(MYSQLI_ASSOC) as $module) {
				$modules[] = new \TournamentSystem\Model\Module(
					$module['name'],
					$module['module']
				);
			}

			parent::render(new ModulesView($modules));
			$result->free();
		}

		return parent::OK;
	}

	#[LoginRequired]
	protected function post(): int {
		if(array_key_exists('action', $_POST)) {
			global $_TS;

			$name = $_POST['name'];

			switch($_POST['action']) {
				case 'add':
					if(!$_TS['user']->hasPermission(Permissions::MODULE_ADD)) {
						return self::FORBIDDEN;
					}

					$this->stmt[1]->bind_param('ss', $name, $_POST['module']);
					$this->stmt[1]->execute();

					Module::load($name)->install();

					return self::get();
				case 'delete':
					if(!$_TS['user']->hasPermission(Permissions::MODULE_DELETE)) {
						return self::FORBIDDEN;
					}

					Module::load($name)->uninstall();

					$this->stmt[2]->bind_param('s', $name);
					$this->stmt[2]->execute();

					return self::get();
				case 'settings':
					if(!$_TS['user']->hasPermission(Permissions::MODULE_SETTINGS->value . '.' . $name)) {
						return self::FORBIDDEN;
					}

					//Module::load($name)->settings();

					return self::get();
			}
		}

		return parent::NOT_IMPLEMENTED;
	}
}
