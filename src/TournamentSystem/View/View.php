<?php

namespace TournamentSystem\View;

use TournamentSystem\TournamentSystem;

abstract class View {
	protected string $title;
	protected string $type;

	public function __construct(string $title, string $type) {
		$this->title = $title;
		$this->type = $type;
	}

	protected final function renderView(string $name, array $params = []): void {
		$renderer = TournamentSystem::instance()->getRenderer();

		$renderer->baseParams['title'] = $this->title;
		$renderer->baseParams['type'] = $this->type;

		$renderer->render($name, $params);
	}

	protected final function renderHTML(string $body): void {
		$renderer = TournamentSystem::instance()->getRenderer();

		$renderer->baseParams['title'] = $this->title;
		$renderer->baseParams['type'] = $this->type;

		$renderer->renderBase($body);
	}

	public function template(string $name, array $params = []): string {
		$renderer = TournamentSystem::instance()->getRenderer();

		return $renderer->template($name, $params);
	}

	public abstract function render(): void;
}
