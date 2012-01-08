<?php

class PdTests_ContainerTests_DependenciesTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Pd_Container_Dependencies
	 */
	private $containerDependencies;

	static function setUpBeforeClass() {
		Pd_ServiceMap::set_ServiceDefinition('TestServiceDefinition');
	}

	protected function setUp() {
		$this->containerDependencies = new Pd_Container_Dependencies();
	}

	public function testGet() {
		$object = new stdClass();
		$object->name = 'testName';
		$this->containerDependencies->set('test', $object);

		$getObject = $this->containerDependencies->get('test');

		$this->assertEquals(
			'testName',
			$getObject->name
		);

	}

	public function testGetNotFoundNull() {
		$this->assertNull(
			$this->containerDependencies->get('doesntExist')
		);
	}

	public function test_lazyLoadFromGet() {
		$getObject = $this->containerDependencies->get('dummyClass');
		$this->assertSame('dummyClass', $getObject->name);
	}
}

class TestServiceDefinition extends Pd_BaseServiceDefinition {
	static protected function get_dummyClass() {
		$class = new stdClass();
		$class->name = 'dummyClass';
		return $class;
	}
}