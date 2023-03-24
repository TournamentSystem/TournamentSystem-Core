<?php

namespace TournamentSystem\Config;

abstract class ConfigIni extends Config {

	public function load(): void {
		$config = parse_ini_file($this->file, false, INI_SCANNER_TYPED);

		foreach($config as $key => $value) {
			if($value === null || $value === '') {
				continue;
			}

			$this->$key = $value;
		}
	}

	public function save(): void {
		// TODO: Implement save() method.
	}
}
