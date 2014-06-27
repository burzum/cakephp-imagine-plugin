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

App::uses('Imagine', 'Imagine.Lib');

class ImagineTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		$this->Imagine = new Imagine();
	}

/**
 * testOperationsToString
 *
 * @return void
 */
	public function testOpenAndSave() {
		$image = CakePlugin::path('Imagine') . 'Test' . DS . 'Fixture' . DS . 'titus.jpg';
		$Image = $this->Imagine->open($image);

		$file = TMP . 'foo.jpg';
		$this->Imagine->save($Image, $file);
		$this->assertTrue(file_exists($file));
		unlink($file);
	}

}
