<?php

namespace TournamentSystem\Renderer;

class Stylesheet extends LinkedFile {

	public function render(): string {
		$html = '<link';
		$html .= ' rel="stylesheet"';
		$html .= ' href="' . $this->getPath() . '"';
		$html .= ' integrity="' . $this->getIntegrity() . '"';
		$html .= ' crossorigin="' . $this->getCrossorigin() . '"';
		$html .= '>';

		return $html;
	}
}
