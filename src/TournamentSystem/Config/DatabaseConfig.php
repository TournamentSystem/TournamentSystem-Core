<?php

namespace TournamentSystem\Config;

class DatabaseConfig extends ConfigJson {
	public ?string $host = null;
	public ?string $user = null;
	public ?string $password = null;
	public string $database = '';
	public ?int $port = null;
	public ?string $socket = null;
	public ?string $prefix = 'tournament_';
}
