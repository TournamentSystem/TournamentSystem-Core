<?php

namespace TournamentSystem\Model;

use DateTime;
use TournamentSystem\Database\Column;
use TournamentSystem\Database\Table;

#[Table('id')]
class Person {
	public readonly int $id;
	#[Column(size: 128)]
	public readonly string $firstname;
	#[Column(size: 64)]
	public readonly string $name;
	public readonly DateTime $birthday;
	public readonly Gender $gender;

	public function __construct(int $id, string $firstname, string $name, DateTime $birthday, Gender $gender) {
		$this->id = $id;
		$this->firstname = $firstname;
		$this->name = $name;
		$this->birthday = $birthday;
		$this->gender = $gender;
	}

	public function getDisplayName(): string {
		return "$this->name, $this->firstname";
	}
}
