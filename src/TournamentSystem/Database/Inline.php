<?php

namespace TournamentSystem\Database;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class Inline {
	public const BACKED_ENUM = 'value';
	
	public readonly string $property;
	
	public function __construct(string $property) {
		$this->property = $property;
	}
}
