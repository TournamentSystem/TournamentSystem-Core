<?php

namespace TournamentSystem\Module;

use ReflectionClass;
use ReflectionProperty;
use TournamentSystem\TournamentSystem;

abstract class ModuleSettings {
	private Module $module;
	
	public final function __construct(Module $module) {
		$this->module = $module;
	}
	
	public final function load(): void {
		$stmt = TournamentSystem::instance()->getDatabase()->prepare('SELECT settings FROM tournament_modules WHERE name=?');
		
		$name = $this->module->name;
		$stmt->bind_param('s', $name);
		$stmt->execute();
		
		if($result = $stmt->get_result()) {
			$settings = $result->fetch_row()[0];
			$settings = json_decode($settings, true);
			
			$this->__unserialize($settings);
			
			$result->free();
		}
	}
	
	public final function save(): void {
		$stmt = TournamentSystem::instance()->getDatabase()->prepare('UPDATE tournament_modules SET settings=? WHERE name=?');
		
		$settings = $this->__serialize();
		$settings = json_encode($settings);
		
		$name = $this->module->name;
		$stmt->bind_param('ss', $settings, $name);
		
		$stmt->execute();
	}
	
	public final function __serialize(): array {
		$class = new ReflectionClass($this);
		$properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);
		
		$data = [];
		foreach($properties as $property) {
			$data[$property->getName()] = $property->getValue($this);
		}
		
		return $data;
	}
	
	public final function __unserialize(array $data): void {
		$class = new ReflectionClass($this);
		$properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);
		
		foreach($properties as $property) {
			$property->setValue($this, $data[$property->getName()]);
		}
	}
}
