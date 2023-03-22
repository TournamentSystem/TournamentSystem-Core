<?php

namespace TournamentSystem\Database;

use mysqli;
use ReflectionClass;
use ReflectionException;
use TournamentSystem\Config\Config;
use TournamentSystem\Database\Generation\TableGenerator;

class Database extends mysqli {
	private readonly string $prefix;

	public function __construct(Config $config) {
		parent::__construct($config->database->hostname, $config->database->username, $config->database->password, $config->database->database, $config->database->port, $config->database->socket);

		$this->prefix = &$config->database->prefix;
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
