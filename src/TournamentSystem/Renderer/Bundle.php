<?php

namespace TournamentSystem\Renderer;

class Bundle extends HeadElements {
	private string $name;
	/**
	 * @var Stylesheet[]
	 */
	private array $styles;
	/**
	 * @var Script[]
	 */
	private array $scripts;
	/**
	 * @var LinkedFile[]
	 */
	private array $others;
	
	/**
	 * @param Stylesheet[] $styles
	 * @param Script[] $scripts
	 * @param LinkedFile[] $others
	 */
	public function __construct(string $name, array $styles = [], array $scripts = [], array $others = []) {
		$this->name = $name;
		$this->styles = $styles;
		$this->scripts = $scripts;
		$this->others = $others;
	}
	
	public function render(): string {
		$html = '<!-- ' . $this->name . ' -->' . PHP_EOL;
		
		foreach($this->styles as $style) {
			$html .= $style->render() . PHP_EOL;
		}
		foreach($this->scripts as $script) {
			$html .= $script->render() . PHP_EOL;
		}
		foreach($this->others as $other) {
			$html .= $other->render() . PHP_EOL;
		}
		
		return $html;
	}
	
	public function getName(): string {
		return $this->name;
	}
	
	/**
	 * @return Stylesheet[]
	 */
	public function getStyles(): array {
		return $this->styles;
	}
	
	/**
	 * @return Script[]
	 */
	public function getScripts(): array {
		return $this->scripts;
	}
	
	/**
	 * @return LinkedFile[]
	 */
	public function getOthers(): array {
		return $this->others;
	}
	
	/**
	 * @return HeadElement[]
	 */
	protected function elements(): array {
		return array_merge($this->styles, $this->scripts, $this->others);
	}
}
