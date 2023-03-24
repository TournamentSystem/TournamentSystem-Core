<?php

namespace TournamentSystem\Config;

abstract class ConfigJson extends Config {

	public function load(): void {
		$config = json_decode(file_get_contents($this->file), true);

		foreach($config as $key => $value) {
			$this->$key = $value;
		}
	}

	public function save(): void {
		file_put_contents($this->file, json_encode($this, JSON_PRETTY_PRINT));
	}
}
