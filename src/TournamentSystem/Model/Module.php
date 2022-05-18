<?php

namespace TournamentSystem\Model;

class Module {
	private string $name;
	private string $module;
	
	public function __construct(string $name, string $module) {
		$this->name = $name;
		$this->module = $module;
	}
	
	public function getName(): string {
		return $this->name;
	}
	
	public function getModule(): string {
		return $this->module;
	}
}
