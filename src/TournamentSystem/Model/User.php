<?php

namespace TournamentSystem\Model;

class User {
	private string $name;
	private string $password_hash;
	
	public function __construct(string $name, string $password_hash) {
		$this->name = $name;
		$this->password_hash = $password_hash;
	}
	
	public function getName(): string {
		return $this->name;
	}
	
	public function getPasswordHash(): string {
		return $this->password_hash;
	}
}
