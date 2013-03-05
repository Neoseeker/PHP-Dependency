<?php


class Pd_BaseServiceDefinition {

	static public function create($service) {
		$fn = 'get_'.$service;
		if (method_exists(new static(), $fn)) {
			return static::$fn();
		}
		return null;
	}

}

?>