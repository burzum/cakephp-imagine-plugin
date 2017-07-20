<?php
/**
 * Copyright 2011-2017, Florian Krämer
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * Copyright 2011-2017, Florian Krämer
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace Burzum\Imagine\Test\TestCase\Lib;

use Burzum\Imagine\Lib\ImagineUtility;
use Cake\Core\Plugin;
use Cake\TestSuite\TestCase;

class ImagineUtilityTest extends TestCase {

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = [];

	/**
	 * testOperationsToString
	 *
	 * @return void
	 */
	public function testOperationsToString() {
		$operations = [
			'thumbnail' => [
				'width' => 200,
				'height' => 150
			]
		];
		$result = ImagineUtility::operationsToString($operations);
		$this->assertEquals($result, '.thumbnail+width-200+height-150');
	}

	/**
	 * testHashImageOperations
	 *
	 * @return void
	 */
	public function testHashImageOperations() {
		$operations = [
			'SomeModel' => [
				't200x150' => [
					'thumbnail' => [
						'width' => 200,
						'height' => 150
					]
				]
			]
		];
		$result = ImagineUtility::hashImageOperations($operations);
		$this->assertEquals($result, [
			'SomeModel' => ['t200x150' => '38b1868f']
		]);
	}

	/**
	 * testGetImageOrientation
	 *
	 * @return void
	 */
	public function testGetImageOrientation() {
		$image = Plugin::path('Burzum/Imagine') . 'tests' . DS . 'Fixture' . DS . 'titus.jpg';
		$result = ImagineUtility::getImageOrientation($image);
		$this->assertEquals($result, 0);

		$image = Plugin::path('Burzum/Imagine') . 'tests' . DS . 'Fixture' . DS . 'Portrait_6.jpg';
		$result = ImagineUtility::getImageOrientation($image);
		$this->assertEquals($result, -90);

		try {
			ImagineUtility::getImageOrientation('does-not-exist');
			$this->fail('No \RuntimeException thrown as expected!');
		} catch (\RuntimeException $e) {
		}
	}

}
