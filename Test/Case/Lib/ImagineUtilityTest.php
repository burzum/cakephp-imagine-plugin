<?php
/**
 * Copyright 2011-2012, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * Copyright 2011-2012, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::import('Lib', 'ImagineUtility');

class ImagineUtilityTest extends CakeTestCase {

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
	}

/**
 * endTest
 *
 * @return void
 */
	public function endTest() {
	}

/**
 *
 */
	public function testOperationsToString() {
		$operations = array(
			'thumbnail' => array(
				'width' => 200,
				'height' => 150));
		$result = ImagineUtility::operationsToString($operations);
		$this->assertEqual($result, '.thumbnail+width-200+height-150');
	}

/**
 * 
 */
	public function testHashImageOperations() {
		$operations = array(
			'thumbnail' => array(
				'width' => 200,
				'height' => 150));
		$result = ImagineUtility::hashImageOperations();
	}

}