<?php

namespace TournamentSystem\View\Admin;

use TournamentSystem\Module\ModuleSettings;
use TournamentSystem\View\View;

class SettingsView extends View {
	private array $settings;
	private array $types;

	public function __construct(ModuleSettings $settings) {
		parent::__construct('Settings', 'admin_settings');

		$this->settings = $settings->__serialize();
		$this->types = $this->getType($this->settings);
	}

	public function render(): void {
		print(self::template('templates/admin/settings.latte', [
			'settings' => $this->settings,
			'types' => $this->types
		]));
	}

	private function getType(mixed $value): string|array {
		if(is_bool($value)) return 'switch';
		if(is_int($value)) return 'number';
		if(is_float($value)) return 'number';
		if(is_string($value)) return 'text';
		if(is_array($value)) return array_map([$this, 'getType'], $value);

		return 'hidden';
	}
}
