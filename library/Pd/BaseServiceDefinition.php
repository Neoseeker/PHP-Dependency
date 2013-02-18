<?php
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpFoundation\Session\Session;
use \Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use \Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcacheSessionHandler;

class Pd_BaseServiceDefinition {

	static $log_memcache_to_file = false;

	static public function create($service) {
		$fn = 'get_'.$service;
		if (method_exists(new static(), $fn)) {
			return static::$fn();
		}
		return null;
	}

	static protected function get_db() {
		return new Db_RW();
	}

	static protected function get_timer() {
		return nTimer::create();
	}

	static protected function get_redis() {
		$redis = new Redis();
		$redis->connect('127.0.0.1');

		return $redis;
	}

	static protected function get_cassandra() {
		$dsn = "cassandra:host=127.0.0.1;port=9160";
		$cassandra = new PDO("cassandra:host=127.0.0.1;port=9160");

		return $cassandra;
	}

	static protected function get_request() {
		return Request::createFromGlobals();
	}

	static protected function get_response() {
		return new Response();
	}

	static protected function get_flash() {
		return new \neolib\FlashMessage();
	}

	static protected function get_session() {
		if (!class_exists('SessionHandlerInterface')) {
			include_once PHP_LIBRARIES_PATH.'vendor/symfony/symfony/src/Symfony/Component/HttpFoundation/Resources/stubs/SessionHandlerInterface.php';
		}
		/** @var \nMemcached $memcache */
		$memcache = \Pd_ServiceMap::get('memcache');
		$storage = new NativeSessionStorage(array(), new MemcacheSessionHandler($memcache));
		$session = new Session($storage);
		return $session;
	}

	/**
	 * @static
	 * @return \nBrowser
	 */
	static protected function get_browser() {
		$user_agent = nBase::get_value('HTTP_USER_AGENT',$_SERVER, null, (PHP_SAPI == 'cli' ? 'cli' : 'api'));
		//set the mobile cookie domain to the root domain so all subdomains are respected
		$cookie_domain = null;
		if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] != '') {
			$cookie_domain = nBase::get_domain('http://'.$_SERVER['HTTP_HOST'].'/');
		}
		$browser = new nBrowser($user_agent,false,$cookie_domain);
		return $browser;
	}

	/**
	 * @static
	 * @return nMemcached
	 */
	static protected function get_memcache() {
		/** @var \nBrowser $browser  */
		$browser = \Pd_ServiceMap::get('browser');
		if ($browser->show_mobile_mode()) {
			if (!defined('MOBILE_MEMCACHE_KEY_SUFFIX')) {
				define('MOBILE_MEMCACHE_KEY_SUFFIX','_mobile');
			}
		} else {
			if (!defined('MOBILE_MEMCACHE_KEY_SUFFIX')) {
				define('MOBILE_MEMCACHE_KEY_SUFFIX','');
			}
		}
		return nMemcached::create(array(
								  'servers' => array(NEO_MEMCACHE_SERVER_IP),
								  'debug'   => false,
								  'compress_threshold' => 1024000,
								  'persistant' => true),
								  static::log_memcache_keys(), static::$log_memcache_to_file);
	}

	/**
	 * @static
	 * @return nMemcached|nTyrantMemcachedAPI
	 */
	static protected function get_memcachedb() {
		if (\GlobalDefines::is_in_dev_mode() && defined('DEVELOPER') && DEVELOPER == 'hhh') {
			$memcachedb = nTyrantMemcachedAPI::create(array(	'servers' => array(NEO_TOKYO_TYRANT_SERVER_IP),
			                                                    'debug'   => false,
			                                                    'compress_threshold' => 128000,
			                                                    'persistant' => false
			), static::log_memcache_keys(), static::$log_memcache_to_file);
		} else {
			$memcachedb = nTyrantMemcached::create(array(	'servers' => array(NEO_TOKYO_TYRANT_SERVER_IP),
			                                                 'debug'   => false,
			                                                 'compress_threshold' => 128000,
			                                                 'persistant' => false
			),static::log_memcache_keys(), static::$log_memcache_to_file);
		}
		return $memcachedb;
	}

	//Helper functions

	static function log_memcache_keys() {
		static $log_memcache_keys;
		if (!isset($log_memcache_keys)) {
			$log_memcache_keys = (GlobalDefines::is_in_debug_ips() ? true : false);
		}
		return $log_memcache_keys;
	}

	/**
	 * @static
	 * @return neolib\Sphinx\SphinxClient
	 */
	static protected function get_sphinx() {
		return new \neolib\Sphinx\SphinxClient();
	}
}

?>