<?php

namespace TournamentSystem\Controller;

use ReflectionException;
use ReflectionMethod;
use TournamentSystem\Database\DbStatement;
use TournamentSystem\View\View;

abstract class Controller {
	/**
	 * @var string[]
	 */
	public const ALLOWED_METHODS = ['GET', 'POST', 'PUT', 'DELETE'];
	
	protected const OK = 200;
	protected const NO_CONTENT = 204;
	protected const SEE_OTHER = 303;
	protected const TEMPORARY_REDIRECT = 307;
	protected const PERMANENT_REDIRECT = 308;
	protected const UNAUTHORIZED = 401;
	protected const FORBIDDEN = 403;
	protected const NOT_FOUND = 404;
	protected const METHOD_NOT_ALLOWED = 405;
	protected const NOT_IMPLEMENTED = 501;
	
	protected DbStatement $stmt;
	
	/**
	 * @param string|string[]|null $query
	 */
	public function __construct(string|array|null $query = null) {
		if($query !== null) {
			$this->stmt = new DbStatement($query);
		}
	}
	
	protected final function render(View $view): void {
		$view->render();
	}
	
	protected function get(): int {
		return self::NOT_IMPLEMENTED;
	}
	
	protected function post(): int {
		return self::NOT_IMPLEMENTED;
	}
	
	protected function put(): int {
		return self::NOT_IMPLEMENTED;
	}
	
	protected function delete(): int {
		return self::NOT_IMPLEMENTED;
	}
	
	public final function handleRequest(): void {
		$method = $_SERVER['REQUEST_METHOD'];
		
		if(!in_array($method, self::ALLOWED_METHODS)) {
			header('Allow: ' . implode(', ', self::ALLOWED_METHODS), true, self::METHOD_NOT_ALLOWED);
			exit;
		}
		
		$method = strtolower($method);
		
		try {
			$reflectMethod = new ReflectionMethod($this::class, $method);
			
			$loginRequired = $reflectMethod->getAttributes(LoginRequired::class);
			if(!empty($loginRequired) && !session_exists()) {
				http_response_code(self::UNAUTHORIZED());
				return;
			}
			
			$permissionRequired = $reflectMethod->getAttributes(PermissionRequired::class);
			if(!empty($permissionRequired)) {
				if(!session_exists()) {
					http_response_code(self::UNAUTHORIZED());
					return;
				}
				
				foreach($permissionRequired as $perm) {
					if(!$perm->check()) {
						http_response_code(self::FORBIDDEN);
						return;
					}
				}
			}
		}catch(ReflectionException) {
		}
		
		http_response_code($this->$method());
	}
	
	private function setHeader_Location(string $loc): void {
		header("Location: $loc");
	}
	
	protected final function SEE_OTHER(string $loc): int {
		$this->setHeader_Location($loc);
		
		return self::SEE_OTHER;
	}
	
	protected final function TEMPORARY_REDIRECT(string $loc): int {
		$this->setHeader_Location($loc);
		
		return self::TEMPORARY_REDIRECT;
	}
	
	protected final function PERMANENT_REDIRECT(string $loc): int {
		$this->setHeader_Location($loc);
		
		return self::PERMANENT_REDIRECT;
	}
	
	protected final function UNAUTHORIZED(): int {
		print('<meta http-equiv="refresh" content="0;URL=/admin/login/"/>');
		print('<script src="/resources/js/redirect-to-login.js" integrity="" crossorigin="anonymous"></script>');
		
		header('WWW-Authenticate: Cookie realm="TournamentSystem" form-action="/admin/login/" cookie-name="' . session_name() . '"');
		return self::UNAUTHORIZED;
	}
}
