<?php

namespace TournamentSystem\Database;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class Table {
	/**
	 * @var string[]
	 */
	public readonly array $keys;
	
	/**
	 * @param string|string[] $keys
	 */
	public function __construct(string|array $keys = []) {
		if(is_string($keys)) {
			$this->keys = [$keys];
		}else {
			$this->keys = $keys;
		}
	}
}
