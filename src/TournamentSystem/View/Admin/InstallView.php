<?php

namespace TournamentSystem\View\Admin;

use TournamentSystem\View\View;

class InstallView extends View {
	
	public function __construct() {
		parent::__construct('Install', 'admin_install');
	}
	
	public function render(): void {
		parent::renderView(parent::template('admin/install.latte'));
	}
}