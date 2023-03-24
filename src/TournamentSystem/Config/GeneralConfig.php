<?php

namespace TournamentSystem\Config;

class GeneralConfig extends ConfigIni {
	public bool $debug = false;
	public string $logo = '/resources/svg/logo.svg';
	public string $time_format = 'H:i:s';
	public string $date_format = 'd.m.Y';

	public function datetime_format(): string {
		return $this->date_format . ' ' . $this->time_format;
	}
}
