<?php

namespace TournamentSystem\Database\Generation;

class ReferenceData implements Data {
	/**
	 * @var ColumnData[]
	 */
	public array $columns;
	public TableData $refTable;
	public KeyData $refKey;

	/**
	 * @param ColumnData[] $columns
	 */
	public function __construct(array $columns, TableData $table) {
		$this->columns = $columns;
		$this->refTable = $table;
		$this->refKey = $table->primaryKey;
	}

	public function creationString(): string {
		$result = 'FOREIGN KEY (';
		$result .= implode(', ', array_map(fn($col) => $col->name, $this->columns));
		$result .= ') REFERENCES ';
		$result .= $this->refTable->name;
		$result .= ' (';
		$result .= implode(', ', array_map(fn($col) => $col->name, $this->refKey->columns));
		$result .= ') ON DELETE CASCADE';

		return $result;
	}
}
