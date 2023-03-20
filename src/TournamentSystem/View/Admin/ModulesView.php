<?php

namespace TournamentSystem\View\Admin;

use TournamentSystem\Model\Module;
use TournamentSystem\View\View;

class ModulesView extends View {
	/**
	 * @var Module[]
	 */
	private array $modules;
	
	/**
	 * @param Module[] $modules
	 */
	public function __construct(array $modules) {
		parent::__construct('Modules', 'admin_modules');
		
		$this->modules = $modules;
	}
	
	public function render(): void {
		parent::renderView('templates/admin/modules.latte', [
			'modules' => $this->modules
		]);
	}
}
