<?php

namespace TournamentSystem;

use DateInterval;
use DateTimeInterface;
use Latte\Attributes\TemplateFilter;
use Latte\Essential\Filters;
use TournamentSystem\Config\Config;

class LatteFilters {
	
	#[TemplateFilter]
	public static function time(string|int|DateTimeInterface|DateInterval $time, ?string $format = null): ?string {
		return Filters::date($time, $format ?? self::config()->general->time_format);
	}
	
	#[TemplateFilter]
	public static function date(string|int|DateTimeInterface|DateInterval $date, ?string $format = null): ?string {
		return Filters::date($date, $format ?? self::config()->general->date_format);
	}
	
	#[TemplateFilter]
	public static function datetime(string|int|DateTimeInterface|DateInterval $datetime, ?string $format = null): ?string {
		return Filters::date($datetime, $format ?? self::config()->general->datetime_format());
	}
	
	private static function config(): Config {
		return TournamentSystem::instance()->getConfig();
	}
}
