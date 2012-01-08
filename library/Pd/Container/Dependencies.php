<?php

/**
 * Holds all of the dependencies in an array by
 * name (key) and dependency (value).
 *
 */
class Pd_Container_Dependencies {

	private $containerName;

	private $_dependencies = array();

	public function __construct($container = 'main') {
		$this->containerName = $container;
	}

	/**
	 * Returns a dependency by name.  If dependency is not found,
	 * null is returned.
	 *
	 * @param string $name
	 * @param bool $disable_lazy_load   disable lazy loading (default: false)
	 * @return mixed dependency
	 */
	public function get($name, $disable_lazy_load = false) {

		if (isset($this->_dependencies[$name])) {
			return $this->_dependencies[$name]['instance'];
		} else {
			if (!$disable_lazy_load) {
				$dependency = \Pd_ServiceMap::get($name, $this->containerName);
				if (!is_null($dependency)) {
					return $dependency;
				}
			}
			return null;
		}

	}

	/**
	 * Sets a depenedency by name
	 *
	 * @param string $name
	 * @param mixed $dependency resource
	 */
	public function set($name, $dependency) {
		$this->_dependencies[$name] = array(
			'instance' => $dependency,
		);
	}

}