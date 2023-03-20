<?php

namespace TournamentSystem\Renderer;

use Latte\Engine;
use TournamentSystem\LatteFilters;

class LatteRenderer {
	private Engine $engine;
	private string $base;
	/**
	 * @var HeadElement[]
	 */
	private array $headElements;
	
	/**
	 * @var array<string, mixed>
	 */
	public array $baseParams = [];
	/**
	 * @var HeadElement[]
	 */
	public array $moduleHeadElements = [];
	
	/**
	 * @param Engine $engine
	 * @param string $base
	 * @param HeadElement[] $headElements
	 */
	public function __construct(Engine $engine, string $base, array $headElements = []) {
		$this->engine = $engine;
		$this->base = $base;
		$this->headElements = $headElements;
	}
	
	public function template(string $name, array $params = []): string {
		global $_TS;
		
		$filters = new LatteFilters();
		$filters->_TS = $_TS;
		
		foreach($params as $key => $value) {
			$filters->$key = $value;
		}
		
		return $this->engine->renderToString(__ROOT__ . "/$name", $filters);
	}
	
	public function render(string $name, array $params = []): void {
		$this->renderBase($this->template($name, $params));
	}
	
	public function renderBase(?string $body = ''): void {
		$this->engine->render(__ROOT__ . "/$this->base", [
			...$this->baseParams,
			'head' => $this->getHeadElements()->render(),
			'body' => $body
		]);
	}
	
	public function reset(): void {
		$this->moduleHeadElements = [];
	}
	
	public function getHeadElements(): HeadElements {
		return new RendererElements([
			...$this->headElements,
			...$this->moduleHeadElements
		]);
	}
}
