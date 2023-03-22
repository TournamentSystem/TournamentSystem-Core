<?php

namespace TournamentSystem\Model;

use TournamentSystem\Database\Column;
use TournamentSystem\Database\Table;

#[Table('name')]
class Module {
	#[Column(size: 32)]
	public readonly string $name;
	#[Column(size: 16)]
	public readonly string $module;

	public function __construct(string $name, string $module) {
		$this->name = $name;
		$this->module = $module;
	}
}
