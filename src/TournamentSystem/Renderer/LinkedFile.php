<?php

namespace TournamentSystem\Renderer;

abstract class LinkedFile extends HeadElement {
	private string $path;
	private string $integrity;
	private string $crossorigin;

	public function __construct(string $path, ?string $integrity = '', ?string $crossorigin = 'anonymous') {
		$this->path = $path;
		$this->integrity = $integrity;
		$this->crossorigin = $crossorigin;
	}

	public function getCSP(): ?string {
		if(str_starts_with($this->path, 'http')) {
			return $this->path;
		}

		return null;
	}

	public final function getPath(): string {
		return $this->path;
	}

	public final function getIntegrity(): string {
		return $this->integrity;
	}

	public final function getCrossorigin(): string {
		return $this->crossorigin;
	}
}
