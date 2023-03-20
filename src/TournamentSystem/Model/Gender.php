<?php

namespace TournamentSystem\Model;

use TournamentSystem\Database\Inline;

#[Inline(Inline::BACKED_ENUM)]
enum Gender: int {
	case NOT_KNOWN = 0;
	case MALE = 1;
	case FEMALE = 2;
	case NOT_APPLICABLE = 9;
}
