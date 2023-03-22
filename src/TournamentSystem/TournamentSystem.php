<?php

namespace TournamentSystem;

define('__ROOT__', $_SERVER['DOCUMENT_ROOT']);

global $_TS;
$_TS = [
	'module' => null,
	'action' => null,
	'page' => null
];
$_REQUEST['_TS'] = &$_TS;

require_once 'utility.php';
require_once 'session.php';

use Latte\Engine;
use TournamentSystem\Config\Config;
use TournamentSystem\Controller\Admin\DashboardController;
use TournamentSystem\Controller\Admin\InstallController;
use TournamentSystem\Controller\Admin\LoginController;
use TournamentSystem\Controller\Admin\LogoutController;
use TournamentSystem\Controller\Admin\ModulesController;
use TournamentSystem\Controller\Admin\SettingsController;
use TournamentSystem\Controller\Admin\UpdateController;
use TournamentSystem\Database\Database;
use TournamentSystem\Database\DbStatement;
use TournamentSystem\Model\Permission;
use TournamentSystem\Model\Person;
use TournamentSystem\Model\User;
use TournamentSystem\Module\Module;
use TournamentSystem\Renderer\Bundle;
use TournamentSystem\Renderer\LatteRenderer;
use TournamentSystem\Renderer\Script;
use TournamentSystem\Renderer\Stylesheet;
use TournamentSystem\View\DebugView;

class TournamentSystem {
	private static TournamentSystem $INSTANCE;

	private Config $config;
	private Database $database;
	private LatteRenderer $renderer;

	private function __construct() {
		$this->config = new Config('config.ini');
		$this->database = new Database($this->config);

		$this->initRenderer();
	}

	private function initRenderer(): void {
		$headElements = [];

		$jquery = new Bundle('jQuery', scripts: [
			new Script(
				'https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js',
				'sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK'
			)
		]);
		$headElements[] = $jquery;

		$bootstrap = new Bundle('Bootstrap', styles: [
			new Stylesheet(
				'https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css',
				'sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx'
			)
		], scripts: [
			new Script(
				'https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js',
				'sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa'
			)
		]);
		$headElements[] = $bootstrap;

		$ts = new Bundle('TournamentSystem', styles: [
			new Stylesheet('/resources/css/tournament_system.min.css')
		], scripts: [
			new Script('/resources/js/polyfills.js'),
			new Script('/resources/js/buttons.js')
		]);
		$headElements[] = $ts;

		$this->renderer = new LatteRenderer(new Engine(), 'templates/base.latte', $headElements);

		$this->renderer->baseParams['logo'] = $this->getLogo();
	}

	private function getLogo(): string {
		$logo = $this->config->general->logo;

		if(str_ends_with($logo, '.svg') && !str_starts_with($logo, 'http')) {
			return file_get_contents(__ROOT__ . $logo);
		}

		return "<img src='$logo' alt='Logo'/>";
	}

	private function getContentSecurityPolicy(): string {
		$csp = "Content-Security-Policy:";

		$headElems = $this->renderer->getHeadElements()->getElementsFlatten();
		$scripts = array_filter($headElems, fn($elem) => $elem instanceof Script);
		$styles = array_filter($headElems, fn($elem) => $elem instanceof Stylesheet);

		$csp .= "default-src 'self';";
		$csp .= "script-src 'self' " . implode(' ', array_map(fn($elem) => $elem->getCSP(), $scripts)) . ';';
		$csp .= "style-src 'self' " . implode(' ', array_map(fn($elem) => $elem->getCSP(), $styles)) . ';';
		$csp .= "img-src 'self' data:;";

		return $csp;
	}

	private function sendHeaders(): void {
		header($this->getContentSecurityPolicy());
		header('Referrer-Policy: origin-when-cross-origin, strict-origin-when-cross-origin');
	}

	public function handle(): void {
		global $_TS;

		if(session_exists()) {
			if(!session_verify()) {
				header('Location: /');
				$this->sendHeaders();
				return;
			}

			$stmt = new DbStatement([
				'SELECT * FROM tournament_user WHERE name=?',
				'SELECT * FROM tournament_permissions WHERE user=?'
			]);

			$user = $_SESSION['user'];

			$stmt[0]->bind_param('s', $user);
			$stmt[1]->bind_param('s', $user);
			$stmt[0]->execute();

			if($result0 = $stmt[0]->get_result()) {
				$permissions = [];

				if($stmt[1]->execute() && $result1 = $stmt[1]->get_result()) {
					foreach($result1->fetch_all(MYSQLI_ASSOC) as $perm) {
						$permissions[] = new Permission($perm['permission']);
					}

					$result1->free();
				}

				if($user = $result0->fetch_assoc()) {
					$_TS['user'] = new User(
						$user['name'],
						$user['password'],
						$permissions
					);
				}

				$result0->free();
			}
		}

		$controller = null;

		if(array_key_exists('_module', $_GET)) {
			$_TS['module'] = $module = $_GET['_module'];

			$query = explode('?', $_SERVER['REQUEST_URI'], 2);
			$query = $query[1] ?? '';
			parse_str($query, $_TS['params']);

			if($module === 'admin') {
				$_TS['action'] = $action = $_GET['_action'] ?? null;

				$controller = match ($action) {
					null, '', 'dashboard' => new DashboardController(),
					'login' => new LoginController(),
					'logout' => new LogoutController(),
					'update' => new UpdateController(),
					'modules' => new ModulesController(),
					'settings' => new SettingsController(),
					'install' => new InstallController(),

					default => null
				};
			}elseif($module !== 'none') {
				$_TS['module'] = $module = Module::load($module) ?? $module;
				$_TS['page'] = $page = explode('/', $_GET['_page'] ?? null);

				if($module instanceof Module) {
					$controller = $module->handle($page);
				}
			}
		}

		$this->sendHeaders();

		if($controller) {
			$controller->handleRequest();
		}else {
			$page = new DebugView();
			$page->render();
		}

		$this->renderer->reset();
	}

	public function postInstall(): void {
		$this->database->createTable(User::class);
		$this->database->createTable(Person::class);
		$this->database->createTable(Model\Module::class);
	}

	public function getConfig(): Config {
		return $this->config;
	}

	public function getDatabase(): Database {
		return $this->database;
	}

	public function getRenderer(): LatteRenderer {
		return $this->renderer;
	}

	public static function instance(): TournamentSystem {
		if(!isset(self::$INSTANCE)) {
			self::$INSTANCE = new TournamentSystem();
		}

		return self::$INSTANCE;
	}
}
