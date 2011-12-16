<?php
/**
 * Copyright 2011, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010, Cake Development Corporation (http://cakedc.com)
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
 * testBuildParams
 *
 * @return void
 */
	public function testBuildImageParams() {
		$result = $this->Model->buildImageParams('width|300;height|300');
		$this->assertEqual($result, array('width' => 300, 'height' => 300));
	}

/**
 * testCheckSignature
 *
 * @return void
 */
	public function testCheckSignature() {
		Configure::write('Imagine.salt', 'test');

		$url = array('test' => 'foo');
		$url['hash'] = Security::hash(serialize($url) . Configure::read('Imagine.salt'));
		$this->assertTrue($this->Model->checkSignature($url));
		$this->assertFalse($this->Model->checkSignature());

		Configure::delete('Imagine.salt');
		$this->expectException('Exception');
		$this->Model->checkSignature();
	}

	public function testProcessImage() {
		
	}

}