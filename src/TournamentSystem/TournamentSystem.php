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
use TournamentSystem\Controller\Admin\UpdateController;
use TournamentSystem\Database\Database;
use TournamentSystem\Database\DbStatement;
use TournamentSystem\Model\Permission;
use TournamentSystem\Model\User;
use TournamentSystem\Module\Module;
use TournamentSystem\View\DebugView;
use TournamentSystem\View\View;

class TournamentSystem {
	private static TournamentSystem $INSTANCE;
	
	private Config $CONFIG;
	private Database $DB;
	
	private function __construct() {
		$this->CONFIG = new Config('config.ini');
		$this->DB = new Database($this->CONFIG);
		
		$this->initStaticVars();
	}
	
	private function initStaticVars(): void {
		DbStatement::$db = $this->DB;
		Module::$db = $this->DB;
		
		View::$config = $this->CONFIG;
		View::$latte = new Engine();
	}
	
	public function handle(): void {
		global $_TS;
		
		if(session_exists()) {
			if(!session_verify()) {
				header('Location: /');
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
			
			if($module === 'admin') {
				$_TS['action'] = $action = $_GET['_action'] ?? null;
				
				$controller = match ($action) {
					null, '', 'dashboard' => new DashboardController(),
					'login' => new LoginController(),
					'logout' => new LogoutController(),
					'update' => new UpdateController(),
					'modules' => new ModulesController(),
					'install' => new InstallController(),
					
					default => null
				};
			}else if($module !== 'none') {
				$_TS['module'] = $module = Module::load($module) ?? $module;
				$_TS['page'] = $page = explode('/', $_GET['_page'] ?? null);
				
				if($module instanceof Module) {
					$controller = $module->handle($page);
				}
			}
		}
		
		if($controller) {
			$controller->handleRequest();
		}else {
			$page = new DebugView();
			$page->render();
		}
	}
	
	public static function instance(): TournamentSystem {
		if(!isset(self::$INSTANCE)) {
			self::$INSTANCE = new TournamentSystem();
		}
		
		return self::$INSTANCE;
	}
}
