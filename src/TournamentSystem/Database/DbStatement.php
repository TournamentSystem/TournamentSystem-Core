<?php

namespace TournamentSystem\Database;

use ArrayAccess;
use Exception;
use mysqli_stmt;
use TournamentSystem\TournamentSystem;

class DbStatement implements ArrayAccess {
	/**
	 * @var string[] $stmt
	 */
	private array $stmt;
	/**
	 * @var mysqli_stmt[] $cache
	 */
	private array $cache;

	/**
	 * @param string|string[] $query
	 */
	public function __construct(string|array $query) {
		if(is_array($query)) {
			foreach($query as $q) {
				$this->stmt[] = $q;
			}
		}else {
			$this->stmt[] = $query;
		}
	}

	public function offsetExists(mixed $offset): bool {
		return isset($this->stmt[$offset]);
	}

	public function offsetGet(mixed $offset): ?mysqli_stmt {
		if($this->offsetExists($offset)) {
			if(!isset($this->cache[$offset])) {
				$this->cache[$offset] = TournamentSystem::instance()->getDatabase()->prepare($this->stmt[$offset]);
			}

			return $this->cache[$offset];
		}

		return null;
	}

	public function offsetSet(mixed $offset, mixed $value): void {
		throw new Exception('Cannot set statement');
	}

	public function offsetUnset(mixed $offset): void {
		throw new Exception('Cannot unset statement');
	}
}
