<?php

namespace TournamentSystem\Module;

use ReflectionClass;
use TournamentSystem\Controller\Controller;
use TournamentSystem\Database\Database;

abstract class Module {
	public static Database $db;
	
	/**
	 * @var array<string, Module>
	 */
	private static array $INSTANCES = [];
	
	private string $name;
	private string $path;
	
	protected function __construct($name) {
		$this->name = $name;
		$this->path = dirname((new ReflectionClass($this))->getFileName(), 4);
	}
	
	/**
	 * @param string[] $page
	 */
	public abstract function handle(array $page): ?Controller;
	
	public final function install(): void {
		self::$db->multi_query(file_get_contents($this->path . '/sql/create.sql'));
		
		while(self::$db->more_results()) {
			self::$db->next_result();
		}
	}
	
	public final function uninstall(): void {
		self::$db->query('SET FOREIGN_KEY_CHECKS = FALSE');
		
		self::$db->multi_query(file_get_contents($this->path . '/sql/drop.sql'));
		while(self::$db->more_results()) {
			self::$db->next_result();
		}
		
		self::$db->query('SET FOREIGN_KEY_CHECKS = TRUE');
	}
	
	protected static function set(string $name, mixed $value): void {
		$_REQUEST[$name] = $value;
	}
	
	public final function getName(): string {
		return $this->name;
	}
	
	public static function load(string $name): ?Module {
		if(!array_key_exists($name, self::$INSTANCES)) {
			$stmt = self::$db->prepare("SELECT module FROM tournament_modules WHERE name=?");
			$stmt->bind_param("s", $name);
			$stmt->execute();
			
			if($result = $stmt->get_result()) {
				if($module = $result->fetch_row()) {
					$module = $module[0];
					
					self::$INSTANCES[$name] = new ('\TournamentSystem\Module\\' . $module)($name);
					
					$result->free();
				}else {
					$result->free();
					
					return null;
				}
			}
		}
		
		return self::$INSTANCES[$name];
	}
}
