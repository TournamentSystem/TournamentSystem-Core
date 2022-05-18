<?php

namespace TournamentSystem;

use DateInterval;
use DateTimeInterface;
use Latte\Attributes\TemplateFilter;
use Latte\Essential\Filters;
use TournamentSystem\View\View;

class LatteFilters {
	
	#[TemplateFilter]
	public static function time(string|int|DateTimeInterface|DateInterval $time, ?string $format = null): ?string {
		return Filters::date($time, $format ?? View::$config->general->time_format);
	}
	
	#[TemplateFilter]
	public static function date(string|int|DateTimeInterface|DateInterval $date, ?string $format = null): ?string {
		return Filters::date($date, $format ?? View::$config->general->date_format);
	}
	
	#[TemplateFilter]
	public static function datetime(string|int|DateTimeInterface|DateInterval $datetime, ?string $format = null): ?string {
		return Filters::date($datetime, $format ?? View::$config->general->datetime_format());
	}
}
