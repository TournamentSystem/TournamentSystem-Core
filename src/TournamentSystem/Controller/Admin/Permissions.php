<?php

namespace TournamentSystem\Controller\Admin;

enum Permissions: string {
	case MODULE_ADD = 'admin.module.add';
	case MODULE_DELETE = 'admin.module.delete';
	case MODULE_SETTINGS = 'admin.module.settings';
}
