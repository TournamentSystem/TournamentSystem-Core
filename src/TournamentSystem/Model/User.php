<?php

namespace TournamentSystem\Model;

use BackedEnum;
use StringBackedEnum;
use TournamentSystem\Database\Column;
use TournamentSystem\Database\Table;

#[Table('name')]
class User {
	#[Column(size: 32)]
	public readonly string $name;
	public readonly string $password_hash;
	/**
	 * @var Permission[] $permissions
	 */
	#[Column(arrayType: Permission::class)]
	public readonly array $permissions;

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

	/**
	 * @param string|Permission|StringBackedEnum $permission
	 */
	public function hasPermission(string|Permission|BackedEnum $permission): bool {
		if(is_string($permission)) {
			$permission = new Permission($permission);
		}elseif($permission instanceof BackedEnum) {
			$permission = new Permission($permission->value);
		}

		return Permission::checkPermissions([$permission], $this->permissions);
	}

	/**
	 * @param string[]|Permission[]|StringBackedEnum[] $permissions
	 */
	public function hasPermissions(array $permissions): bool {
		if(empty($permissions)) {
			return true;
		}
		if(is_string($permissions[0])) {
			$permissions = array_map(fn($s) => new Permission($s), $permissions);
		}
		if($permissions[0] instanceof BackedEnum) {
			$permissions = array_map(fn($s) => new Permission($s->value), $permissions);
		}

		return Permission::checkPermissions($permissions, $this->permissions);
	}
}
