<?php

namespace TournamentSystem\Database\Generation;

class KeyData implements Data {
	/**
	 * @var ColumnData[]
	 */
	public array $columns;

	public function __construct(array $columns = []) {
		$this->columns = $columns;
	}

	public function creationString(): string {
		return 'PRIMARY KEY (' . implode(', ', array_map(fn($col) => $col->name, $this->columns)) . ')';
	}
}
