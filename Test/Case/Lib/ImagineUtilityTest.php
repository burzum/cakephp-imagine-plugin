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
 * testOperationsToString
 *
 * @return void
 */
	public function testOperationsToString() {
		$operations = array(
			'thumbnail' => array(
				'width' => 200,
				'height' => 150));
		$result = \Imagine\ImagineUtility::operationsToString($operations);
		$this->assertEqual($result, '.thumbnail+width-200+height-150');
	}

/**
 * testHashImageOperations
 *
 * @return void
 */
	public function testHashImageOperations() {
		$operations = array(
			'SomeModel' => array(
				't200x150' => array(
					'thumbnail' => array(
						'width' => 200,
						'height' => 150))));
		$result = \Imagine\ImagineUtility::hashImageOperations($operations);
		$this->assertEqual($result, array(
			'SomeModel' => array(
			't200x150' => '38b1868f')));
	}

}
