<?php

namespace TournamentSystem\Renderer;

abstract class HeadElements extends HeadElement {

	/**
	 * @return HeadElement[]
	 */
	protected abstract function elements(): array;

	/**
	 * @param class-string|null $type
	 * @return HeadElement[]
	 */
	public final function getElements(?string $type = null): array {
		$elements = $this->elements();

		if($type === null) {
			return $elements;
		}

		return array_filter($elements, function($element) use ($type) {
			return $element instanceof $type;
		});
	}

	/**
	 * @return HeadElement[]
	 */
	public final function getElementsFlatten(): array {
		$elements = $this->getElements();

		return array_reduce($elements, function(array $carry, HeadElement $element) {
			if($element instanceof HeadElements) {
				return array_merge($carry, $element->getElementsFlatten());
			}

			return [...$carry, $element];
		}, []);
	}
}
