<?php

namespace TournamentSystem\Module;

use ReflectionClass;
use TournamentSystem\Controller\Controller;
use TournamentSystem\TournamentSystem;

abstract class Module {
	/**
	 * @var array<string, Module>
	 */
	private static array $INSTANCES = [];
	
	public readonly string $name;
	public readonly string $path;
	
	protected function __construct($name) {
		$this->name = $name;
		$this->path = dirname((new ReflectionClass($this))->getFileName(), 4);
	}
	
	/**
	 * @param string[] $page
	 */
	public abstract function handle(array $page): ?Controller;
	
	public final function install(): void {
		$db = TournamentSystem::instance()->getDatabase();
		
		$db->multi_query(file_get_contents($this->path . '/sql/create.sql'));
		
		while($db->more_results()) {
			$db->next_result();
		}
	}
	
	public final function uninstall(): void {
		$db = TournamentSystem::instance()->getDatabase();
		
		$db->query('SET FOREIGN_KEY_CHECKS = FALSE');
		
		$db->multi_query(file_get_contents($this->path . '/sql/drop.sql'));
		while($db->more_results()) {
			$db->next_result();
		}
		
		$db->query('SET FOREIGN_KEY_CHECKS = TRUE');
	}
	
	protected static function set(string $name, mixed $value): void {
		$_REQUEST[$name] = $value;
	}
	
	public static function load(string $name): ?Module {
		if(!array_key_exists($name, self::$INSTANCES)) {
			$db = TournamentSystem::instance()->getDatabase();
			
			$stmt = $db->prepare("SELECT module FROM tournament_modules WHERE name=?");
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
