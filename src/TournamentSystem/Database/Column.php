<?php

namespace TournamentSystem\Database;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Column {
	public readonly int $size;
	public readonly string $arrayType;
	
	public function __construct(int $size = -1, string $arrayType = '') {
		$this->size = $size;
		$this->arrayType = $arrayType;
	}
}
