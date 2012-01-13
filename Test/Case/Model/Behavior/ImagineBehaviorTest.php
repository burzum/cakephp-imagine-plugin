<?php
/**
 * Copyright 2011, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2011, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');
App::uses('Security', 'Utility');

class ImagineTestModel extends Model {
	public $name = 'ImagineTestModel';
	public $useTable = false;
}

class ImagineBehaviorTest extends CakeTestCase {

/**
 * Holds the instance of the model
 *
 * @var mixed
 */
	public $Article = null;

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

/**
 * startTest
 *
 * @return void
 */
	public function startTest() {
		$this->Model = ClassRegistry::init('ImagineTestModel');
		$this->Model->Behaviors->load('Imagine.Imagine');
	}

/**
 * endTest
 *
 * @return void
 */
	public function endTest() {
		unset($this->Model);
		ClassRegistry::flush();
	}

/**
 * testImagineObject
 *
 * @return void
 */
	public function testImagineObject() {
		$result = $this->Model->imagineObject();
		$this->assertTrue(is_a($result, 'Imagine\Gd\Imagine'));
	}

/**
 * testParamsAsFileString
 *
 * @return void
 */
	public function testParamsAsFileString() {
		$operations = array(
			'thumbnail' => array(
				'width' => 200,
				'height' => 150));
		$result = $this->Model->paramsAsFilestring($operations);
		$this->assertEqual($result, '.thumbnail+width-200+height-150');
	}

}