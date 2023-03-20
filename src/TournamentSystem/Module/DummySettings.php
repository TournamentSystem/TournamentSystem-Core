<?php

namespace TournamentSystem\Module;

class DummySettings extends ModuleSettings {
	public string $s = 'Hello World!';
	public int $i = 42;
	public bool $b = true;
	public array $a = ['Hello', 'World'];
	public float $f = 3.141592653589793;
	public array $aa = ['key' => 'value'];
}
