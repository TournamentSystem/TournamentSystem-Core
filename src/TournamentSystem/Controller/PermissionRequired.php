<?php

namespace TournamentSystem\Controller;

use Attribute;
use TournamentSystem\Model\Permission;

#[Attribute(Attribute::TARGET_METHOD)]
final class PermissionRequired {
	/**
	 * @var Permission[] $permissions
	 */
	private array $permissions;
	
	/**
	 * @param string|string[] $permission
	 */
	public function __construct(string|array $permission) {
		if(is_array($permission)) {
			$this->permissions = array_map(fn($s) => new Permission($s), $permission);
		}else {
			$this->permissions[] = new Permission($permission);
		}
	}
	
	public function check(): bool {
		if(!session_exists()) {
			return false;
		}
		
		global $_TS;
		return $_TS['user']->hasPermissions($this->permissions);
	}
}
