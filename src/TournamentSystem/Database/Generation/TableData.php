<?php

namespace TournamentSystem\Database\Generation;

use Exception;

class TableData implements Data {
	public string $name;
	/**
	 * @var ColumnData[]
	 */
	public array $columns = [];
	public ?KeyData $primaryKey = null;
	/**
	 * @var ReferenceData[]
	 */
	public array $foreignKeys = [];

	public function __construct(string $name) {
		$this->name = $name;
	}

	public function getColumn(string $name): ColumnData {
		foreach($this->columns as $column) {
			if($column->name === $name) {
				return $column;
			}
		}

		throw new Exception("Column '$name' not found in table '$this->name'");
	}

	/**
	 * @param string[] $columns
	 */
	public function setKey(array $columns): void {
		$columns = array_map(fn($col) => $this->getColumn($col), $columns);

		$this->primaryKey = new KeyData($columns);
	}

	/**
	 * @param string[] $columns
	 */
	public function addReference(array $columns, TableData $table): void {
		$columns = array_map(fn($col) => $this->getColumn($col), $columns);

		$this->foreignKeys[] = new ReferenceData($columns, $table);
	}

	public function creationString(): string {
		$result = 'CREATE TABLE ' . $this->name . ' (';
		$result .= implode(', ', array_map(fn($col) => $col->creationString(), $this->columns));
		if($this->primaryKey !== null) {
			$result .= ', ' . $this->primaryKey->creationString();
		}
		foreach($this->foreignKeys as $key) {
			$result .= ', ' . $key->creationString();
		}
		$result .= ');';

		return $result;
	}
}
