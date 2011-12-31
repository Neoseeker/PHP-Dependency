<?php

class Pd_BaseServiceDefinition {
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

	/**
	 * @static
	 * @return nMemcached
	 */
	static protected function get_memcache() {
		$log_memcache_keys = (isset($_SERVER["SERVER_ADDR"]) && in_array(nBase::get_value('REMOTE_ADDR',$_SERVER), unserialize(DEBUG_IPS)) ? true : false);
		$log_memcache_to_file = false;
		return nMemcached::create(array(
								  'servers' => array(NEO_MEMCACHE_SERVER_IP),
								  'debug'   => false,
								  'compress_threshold' => 1024000,
								  'persistant' => true),
								  $log_memcache_keys, $log_memcache_to_file);
	}
}

?>