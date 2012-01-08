<?php

class Pd_ServiceMap {
	/**
	 * @var string
	 */
	protected $fromContainer;
	static protected $meta = array();
	static protected $serviceDefinition = 'Pd_BaseServiceDefinition';

	/**
	 * Set the service definition class - you can only run this once
	 * @static
	 * @param string $serviceDefinition
	 */
	static public function set_ServiceDefinition($serviceDefinition) {
		if (!isset(self::$meta['define_ServiceDefinition'])) {
			self::$serviceDefinition = $serviceDefinition;
			self::$meta['define_ServiceDefinition'] = true;
		}
	}

	static protected function get_ServiceDefinition() {
		return self::$serviceDefinition;
	}

	/**
	 * @static
	 * @param string $service           service name, ie. neoapi, db
	 * @param string $fromContainer     'main' is the default name used in Pd_Container - do not change
	 * @return mixed
	 */
	static public function get($service, $fromContainer = 'main') {
		$serviceMap = new self($fromContainer);
		$serviceObj = $serviceMap->getPdContainer()->get($service, true);
		if (!is_null($serviceObj)) {
			return $serviceObj;
		}
		return $serviceMap->buildService($service);
	}

	protected function __construct($fromContainer) {
		$this->fromContainer = $fromContainer;
	}

	public function getPdContainer() {
		return Pd_Container::get($this->fromContainer)->dependencies();
	}

	protected function buildService($service) {
		$serviceDefinition = self::get_ServiceDefinition();
		$serviceObj = $serviceDefinition::create($service);
		if (!is_null($serviceObj)) {
			$this->getPdContainer()->set($service, $serviceObj);
		}
		return $serviceObj;
	}
}

?>