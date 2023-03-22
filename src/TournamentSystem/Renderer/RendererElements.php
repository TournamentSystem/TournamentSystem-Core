<?php

namespace TournamentSystem\Renderer;

class RendererElements extends HeadElements {
	/**
	 * @var HeadElement[]
	 */
	private array $elements;

	/**
	 * @param HeadElement[] $elements
	 */
	public function __construct(array $elements) {
		$this->elements = $elements;
	}

	public function render(): string {
		return implode(PHP_EOL, array_map(fn($elem) => $elem->render(), $this->elements));
	}

	/**
	 * @return HeadElement[]
	 */
	protected function elements(): array {
		return $this->elements;
	}
}
