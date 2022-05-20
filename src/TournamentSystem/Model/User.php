<?php

namespace TournamentSystem\Model;

class User {
	private string $name;
	private string $password_hash;
	/**
	 * @var Permission[] $permissions
	 */
	private array $permissions;
	
	/**
	 * @param string $name
	 * @param string $password_hash
	 * @param Permission[] $permissions
	 */
	public function __construct(string $name, string $password_hash, array $permissions) {
		$this->name = $name;
		$this->password_hash = $password_hash;
		$this->permissions = $permissions;
	}
	
	public function hasPermission(string|Permission $permission): bool {
		if(is_string($permission)) {
			$permission = new Permission($permission);
		}
		
		return Permission::checkPermissions([$permission], $this->permissions);
	}
	
	/**
	 * @param string[]|Permission[] $permissions
	 */
	public function hasPermissions(array $permissions): bool {
		if(count($permissions) === 0) {
			return true;
		}
		if(is_string($permissions[0])) {
			$permissions = array_map(fn($s) => new Permission($s), $permissions);
		}
		
		return Permission::checkPermissions($permissions, $this->permissions);
	}
	
	public function getName(): string {
		return $this->name;
	}
	
	public function getPasswordHash(): string {
		return $this->password_hash;
	}
	
	/**
	 * @return Permission[]
	 */
	public function getPermissions(): array {
		return $this->permissions;
	}
}
