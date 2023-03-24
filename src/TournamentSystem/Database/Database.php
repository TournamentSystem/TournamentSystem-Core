<?php

namespace TournamentSystem\Database;

use mysqli;
use ReflectionClass;
use ReflectionException;
use TournamentSystem\Config\DatabaseConfig;
use TournamentSystem\Database\Generation\TableGenerator;

class Database extends mysqli {
	private readonly ?string $prefix;

	public function __construct(DatabaseConfig $config) {
		parent::__construct($config->host, $config->user, $config->password, $config->database, $config->port, $config->socket);

		$this->prefix = &$config->prefix;
	}

	/**
	 * @param class-string $class
	 * @throws ReflectionException
	 */
	public function createTable(string $class): string {
		$tables = (new TableGenerator($this->prefix))->generate(new ReflectionClass($class));

		$query = implode(PHP_EOL, array_map(fn($table) => $table->creationString(), $tables));

		//$this->query($query);
		return $query;
	}
}
