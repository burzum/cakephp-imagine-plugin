<?php
/**
 * Copyright 2011-2014, Florian Krämer
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * Copyright 2011-2014, Florian Krämer
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace Imagine\Test\TestCase\Lib;

use Cake\TestSuite\TestCase;
use Imagine\Lib\ImagineUtility;

class ImagineUtilityTest extends TestCase {

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
		$result = ImagineUtility::operationsToString($operations);
		$this->assertEquals($result, '.thumbnail+width-200+height-150');
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
		$result = ImagineUtility::hashImageOperations($operations);
		$this->assertEquals($result, array(
			'SomeModel' => array(
			't200x150' => '38b1868f')));
	}

}
