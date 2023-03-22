<?php

namespace TournamentSystem\Database\Generation;

use Ds\Queue;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use TournamentSystem\Database\Column;
use TournamentSystem\Database\Inline;
use TournamentSystem\Database\Table;

class TableGenerator {
	private readonly string $prefix;

	/**
	 * @var array<string, TableData>
	 */
	private array $result = [];
	private Queue $queue;

	public function __construct(string $prefix) {
		$this->prefix = $prefix;
		$this->queue = new Queue();
	}

	/**
	 * @return array<string, TableData>
	 * @throws ReflectionException
	 */
	public function generate(ReflectionClass $class): array {
		$this->queue->push(['generateTable', [$class]]);

		while(!$this->queue->isEmpty()) {
			[$method, $arguments] = $this->queue->pop();

			$this->$method(...$arguments);
		}

		return $this->result;
	}

	/**
	 * @throws ReflectionException
	 */
	private function generateTable(ReflectionClass $class): TableData {
		$name = $this->getTableName($class);
		if(isset($this->result[$name])) {
			return $this->result[$name];
		}
		$table = $this->createTable($name);

		$properties = $class->getProperties();
		$this->processProperties($table, $class, $properties);
		$this->fillTableKey($table, $class);

		return $table;
	}

	/**
	 * @throws ReflectionException
	 */
	private function generateListTable(string $tableName, ReflectionClass $class1, ReflectionClass $class2): TableData {
		if(isset($this->result[$tableName])) {
			return $this->result[$tableName];
		}
		$table = $this->createTable($tableName);

		$key1 = $this->getTableKey($class1);
		if(count($key1->columns) === 1) {
			$name = strtolower($class1->getShortName());
			$table->columns[] = new ColumnData($name, $key1->columns[0]->type);
			$table->addReference([$name], $this->result[$this->getTableName($class1)]);
		}else {
			$this->processProperties($table, $class1, $key1->columns);
			$table->addReference(array_map(fn($col) => $col->name, $key1->columns), $this->result[$this->getTableName($class1)]);
		}

		if(($inlineProp = $this->tableInlinedProperty($class2)) !== null) {
			$this->processProperty($table, $class2, $inlineProp, strtolower($class2->getShortName()));
		}else {
			$key2 = $this->getTableKey($class2);
			$this->processProperties($table, $class2, $key2->columns);
			$table->addReference(array_map(fn($col) => $col->name, $key2->columns), $this->result[$this->getTableName($class2)]);
		}

		$table->setKey(array_map(fn($col) => $col->name, $table->columns));

		return $table;
	}

	/**
	 * @throws ReflectionException
	 */
	private function generateArrayTable(string $tableName, ReflectionClass $class, string $type): TableData {
		if(isset($this->result[$tableName])) {
			return $this->result[$tableName];
		}
		$table = $this->createTable($tableName);

		$key = $this->getTableKey($class);
		if(count($key->columns) === 1) {
			$name = strtolower($class->getShortName());
			$table->columns[] = new ColumnData($name, $key->columns[0]->type);
			$table->addReference([$name], $this->result[$this->getTableName($class)]);
		}else {
			$this->processProperties($table, $class, $key->columns);
			$table->addReference(array_map(fn($col) => $col->name, $key->columns), $this->result[$this->getTableName($class)]);
		}

		$table->columns[] = new ColumnData('key', 'INT UNSIGNED');
		$table->setKey(array_map(fn($col) => $col->name, $table->columns));

		$table->columns[] = new ColumnData('value', $type);

		return $table;
	}

	/**
	 * @param ReflectionProperty[] $properties
	 * @throws ReflectionException
	 */
	private function processProperties(TableData $table, ReflectionClass $class, array $properties): void {
		foreach($properties as $prop) {
			$this->processProperty($table, $class, $prop);
		}
	}

	/**
	 * @throws ReflectionException
	 */
	private function processProperty(TableData $table, ReflectionClass $class, ReflectionProperty $property, ?string $name = null): void {
		$name = $name ?? $this->getColumnName($property);
		$type = $property->getType();

		if($type instanceof ReflectionNamedType) {
			$typeName = $type->getName();

			if($type->isBuiltin()) {
				if($typeName === 'array') {
					$arrayTable = $table->name . '_' . $name;
					$arrayType = $this->columnArrayType($property);
					$dbType = $this->builtinToType($arrayType, $property);

					if($dbType === null) {
						$this->queue->push(['generateListTable', [$arrayTable, $class, new ReflectionClass($arrayType)]]);
					}else {
						$this->queue->push(['generateArrayTable', [$arrayTable, $class, $dbType]]);
					}
				}else {
					$dbType = $this->builtinToType($typeName, $property);
					$table->columns[] = new ColumnData($name, $dbType);
				}
				return;
			}

			if($typeName === 'DateTime') {
				$table->columns[] = new ColumnData($name, 'DATE');
				return;
			}

			$typeClass = new ReflectionClass($type->getName());
			$inlineAttrs = $typeClass->getAttributes(Inline::class);

			if(!empty($inlineAttrs)) {
				$inlineProp = $typeClass->getProperty($inlineAttrs[0]->newInstance()->property);

				$this->processProperty($table, $typeClass, $inlineProp, $name);
			}else {
				$refTable = $this->generateTable($typeClass);
				$refCols = $refTable->primaryKey->columns;

				$table->columns += $refCols;
				$table->addReference($refCols, $refTable);
			}
		}
	}

	private function builtinToType(string $type, ?ReflectionProperty $property = null): ?string {
		return match ($type) {
			'int' => 'INT',
			'float' => 'FLOAT',
			'string' => 'VARCHAR(' . $this->columnTypeSize($property, 256) . ')',
			'bool' => 'BOOL',
			default => null
		};
	}

	private function createTable(string $name): TableData {
		return $this->result[$name] = new TableData($name);
	}

	private function columnTypeSize(?ReflectionProperty $property, int $default): int {
		$attributes = $property?->getAttributes(Column::class);

		if(empty($attributes)) {
			return $default;
		}

		return $attributes[0]->newInstance()->size ?? $default;
	}

	private function columnArrayType(?ReflectionProperty $property, string $default = ''): string {
		$attributes = $property?->getAttributes(Column::class);

		if(empty($attributes)) {
			return $default;
		}

		return $attributes[0]->newInstance()->arrayType ?? $default;
	}

	/**
	 * @throws ReflectionException
	 */
	private function tableInlinedProperty(ReflectionClass $class): ?ReflectionProperty {
		$attributes = $class->getAttributes(Inline::class);

		if(empty($attributes)) {
			return null;
		}

		return $class->getProperty($attributes[0]->newInstance()->property);
	}

	private function getTableKey(ReflectionClass $class): KeyData {
		return $this->result[$this->getTableName($class)]->primaryKey;
	}

	private function fillTableKey(TableData $table, ReflectionClass $class): void {
		$attributes = $class->getAttributes(Table::class);

		if(empty($attributes)) {
			return;
		}

		$table->setKey(array_map(fn($key) => $class->getProperty($key)->name, $attributes[0]->newInstance()->keys));
	}

	public function getTableName(ReflectionClass $class): string {
		return $this->prefix . $class->getShortName();
	}

	public function getColumnName(ReflectionProperty $property): string {
		return $property->getName();
	}
}
