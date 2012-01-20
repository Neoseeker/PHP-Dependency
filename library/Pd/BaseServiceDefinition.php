<?php
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

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


	static protected function get_request() {
		return Request::createFromGlobals();
	}

	static protected function get_response() {
		return new Response();
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

	//Helper functions

	static function log_memcache_keys() {
		static $log_memcache_keys;
		if (!isset($log_memcache_keys)) {
			$log_memcache_keys = (isset($_SERVER["SERVER_ADDR"]) && in_array(nBase::get_value('REMOTE_ADDR',$_SERVER), unserialize(DEBUG_IPS)) ? true : false);
		}
		return $log_memcache_keys;
	}
}

?>