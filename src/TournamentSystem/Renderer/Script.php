<?php

namespace TournamentSystem\Renderer;

class Script extends LinkedFile {
	
	public function render(): string {
		$html = '<script';
		$html .= ' src="' . $this->getPath() . '"';
		$html .= ' integrity="' . $this->getIntegrity() . '"';
		$html .= ' crossorigin="' . $this->getCrossorigin() . '"';
		$html .= '></script>';
		
		return $html;
	}
}
