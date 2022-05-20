<?php

namespace TournamentSystem\View;

use Latte\Engine;
use Latte\RuntimeException;
use ReflectionClass;
use TournamentSystem\Config\Config;
use TournamentSystem\LatteFilters;

abstract class View {
	public static Engine $latte;
	public static Config $config;
	
	protected string $title;
	protected string $type;
	
	public function __construct(string $title, string $type) {
		$this->title = $title;
		$this->type = $type;
	}
	
	protected final function renderView(?string $body = ''): void {
		self::$latte->render(__DIR__ . '/../../../' . 'templates/base.latte', [
			'title' => $this->title,
			'head' => '',
			'logo' => $this->getLogo(),
			'type' => $this->type,
			'body' => $body
		]);
	}
	
	public function template(string $name, array $params = []): string {
		$firstException = null;
		
		foreach([$this, self::class] as $context) {
			try {
				return self::getTemplate($context, $name, $params);
			}catch(RuntimeException $e) {
				if($firstException === null) {
					$firstException = $e;
				}
			}
		}
		
		throw $firstException;
	}
	
	
	public static function getTemplate(object|string $context, string $name, array $params = []): string {
		global $_TS;
		
		$filters = new LatteFilters();
		$filters->_TS = $_TS;
		
		foreach($params as $key => $value) {
			$filters->$key = $value;
		}
		
		$reflectContext = new ReflectionClass($context);
		$levels = substr_count($reflectContext->getNamespaceName(), '\\') + 3;
		
		return self::$latte->renderToString(dirname($reflectContext->getFileName(), $levels) . "/templates/$name", $filters);
	}
	
	public abstract function render(): void;
	
	private function getLogo(): string {
		$logo = self::$config->general->logo;
		
		if(str_ends_with($logo, '.svg') && !str_starts_with($logo, 'http')) {
			return file_get_contents(__ROOT__ . $logo);
		}
		
		return "<img src='$logo' alt='Logo'/>";
	}
}
