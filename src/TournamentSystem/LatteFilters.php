<?php

namespace TournamentSystem;

use DateInterval;
use DateTimeInterface;
use Latte\Attributes\TemplateFilter;
use Latte\Essential\Filters;
use TournamentSystem\Config\GeneralConfig;

class LatteFilters {

	#[TemplateFilter]
	public static function time(string|int|DateTimeInterface|DateInterval $time, ?string $format = null): ?string {
		return Filters::date($time, $format ?? self::config()->time_format);
	}

	#[TemplateFilter]
	public static function date(string|int|DateTimeInterface|DateInterval $date, ?string $format = null): ?string {
		return Filters::date($date, $format ?? self::config()->date_format);
	}

	#[TemplateFilter]
	public static function datetime(string|int|DateTimeInterface|DateInterval $datetime, ?string $format = null): ?string {
		return Filters::date($datetime, $format ?? self::config()->datetime_format());
	}

	private static function config(): GeneralConfig {
		return TournamentSystem::instance()->getConfig();
	}
}
