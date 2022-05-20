<?php

namespace TournamentSystem\Model;

class Permission {
	private string $name;
	/**
	 * @var string[] $parts
	 */
	private array $parts;
	
	public function __construct(string $name) {
		$this->name = $name;
		$this->parts = explode('.', $name);
	}
	
	public function check(Permission $actual): bool {
		if($this->name === $actual->name) return true;
		if(count($this->parts) < count($actual->parts)) return false;
		
		for($i = 0; $i < count($actual->parts); $i++) {
			if($this->parts[$i] !== $actual->parts[$i] && $actual->parts[$i] !== '*') return false;
		}
		
		return true;
	}
	
	public function getName(): string {
		return $this->name;
	}
	
	/**
	 * @param Permission[] $expected
	 * @param Permission[] $actual
	 */
	public static function checkPermissions(array $expected, array $actual): bool {
		foreach($expected as $expPerm) {
			foreach($actual as $actPerm) {
				if($expPerm->check($actPerm)) {
					continue 2;
				}
			}
			
			return false;
		}
		
		return true;
	}
}
