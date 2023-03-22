<?php

namespace TournamentSystem\Database\Generation;

class ColumnData implements Data {
	public string $name;
	public string $type;
	public bool $nullable;

	public function __construct(string $name, string $type, bool $nullable = false) {
		$this->name = $name;
		$this->type = $type;
		$this->nullable = $nullable;
	}

	public function creationString(): string {
		return $this->name . ' ' . $this->type . ($this->nullable ? '' : ' NOT NULL');
	}
}
