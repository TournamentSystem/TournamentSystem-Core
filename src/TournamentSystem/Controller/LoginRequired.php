<?php

namespace TournamentSystem\Controller;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class LoginRequired {
}
