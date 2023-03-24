<?php

namespace TournamentSystem\Config;

abstract class Config {
	public readonly string $file;

	public function __construct(string $filename) {
		$this->file = $filename;

		if(file_exists($this->file)) {
			$this->load();
		}else {
			$this->save();
		}
	}

	public abstract function load(): void;

	public abstract function save(): void;
}
