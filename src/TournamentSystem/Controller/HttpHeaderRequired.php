<?php

namespace TournamentSystem\Controller;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class HttpHeaderRequired {
	private string $header;
	private mixed $value;
	
	public function __construct(string $header, mixed $value = null) {
		$this->header = $header;
		$this->value = $value;
	}
	
	public function check(): bool {
		$headers = getallheaders();
		
		if(!isset($headers[$this->header])) {
			return false;
		}
		if($this->value === null) {
			return true;
		}
		
		return $headers[$this->header] === $this->value;
	}
}
