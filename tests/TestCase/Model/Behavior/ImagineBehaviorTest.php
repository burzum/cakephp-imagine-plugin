<?php
/**
 * Copyright 2011-2015, Florian Krämer
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * Copyright 2011-2015, Florian Krämer
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace Burzum\Imagine\Test\TestCase\Model\Behavior;

use Cake\TestSuite\TestCase;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Core\Plugin;
use Imagine\Filter\Transformation;

class ImagineTestModel extends Table {
	public $name = 'ImagineTestModel';
}

class ImagineBehaviorTest extends TestCase {

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
	public $fixtures = [
		'plugin.Burzum\Imagine.Image'
	];

	/**
	 * setUp
	 *
	 * @return void
	 */
	public function setUp() {
		$this->Model = TableRegistry::get('ImagineTestModel');
		$this->Model->addBehavior('Burzum/Imagine.Imagine');
	}

	/**
	 * tearDown
	 *
	 * @return void
	 */
	public function tearDown() {
		unset($this->Model);
		TableRegistry::clear();
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
	public function testOperationsToString() {
		$operations = [
			'thumbnail' => [
				'width' => 200,
				'height' => 150
			]
		];
		$result = $this->Model->operationsToString($operations);
		$this->assertEquals($result, '.thumbnail+width-200+height-150');
	}

	/**
	 * getImageSize
	 *
	 * @return void
	 */
	public function getImageSize() {
		$image = Plugin::path('Burzum/Imagine') . 'tests' . DS . 'Fixture' . DS . 'cake.icon.png';
		$result = $this->Model->getImageSize($image);
		$this->assertEquals($result, [20, 20]);
	}

	/**
	 * testCropInvalidArgumentException
	 *
	 * @expectedException \InvalidArgumentException
	 * @return void
	 */
	public function testCropInvalidArgumentException() {
		$image = Plugin::path('Burzum/Imagine') . 'tests' . DS . 'Fixture' . DS . 'titus.jpg';
		$this->Model->processImage($image, TMP . 'crop.jpg', [], [
			'crop' => []
		]);
	}

	/**
	 * testCrop
	 *
	 * @return void
	 */
	public function testResize() {
		$image = Plugin::path('Burzum/Imagine') . 'tests' . DS . 'Fixture' . DS . 'titus.jpg';
		$this->Model->processImage($image, TMP . 'resize.jpg', [], [
			'resize' => [
				'height' => 150,
				'width' => 200
			]
		]);
		$result = $this->Model->getImageSize(TMP . 'resize.jpg');
		$this->assertEquals($result, [200, 150, 'x' => 200, 'y' => 150]);
	}

	/**
	 * testCrop
	 *
	 * @return void
	 */
	public function testCrop() {
		$image = Plugin::path('Burzum/Imagine') . 'tests' . DS . 'Fixture' . DS . 'titus.jpg';
		$this->Model->processImage($image, TMP . 'crop.jpg', [], [
			'crop' => [
				'height' => 300,
				'width' => 300
			]
		]);
		$result = $this->Model->getImageSize(TMP . 'crop.jpg');
		$this->assertEquals($result, [300, 300, 'x' => 300, 'y' => 300]);
	}

	/**
	 * testThumbnail
	 *
	 * @return void
	 */
	public function testThumbnail() {
		$image = Plugin::path('Burzum/Imagine') . 'tests' . DS . 'Fixture' . DS . 'titus.jpg';

		$this->Model->processImage($image, TMP . 'thumbnail.jpg', [], [
			'thumbnail' => [
				'mode' => 'outbound',
				'height' => 300,
				'width' => 300
			]
		]);

		$result = $this->Model->getImageSize(TMP . 'thumbnail.jpg');
		$this->assertEquals($result, [300, 300, 'x' => 300, 'y' => 300]);

		$this->Model->processImage($image, TMP . 'thumbnail2.jpg', [], [
				'thumbnail' => [
					'mode' => 'inset',
					'height' => 300,
					'width' => 300
				]
			]
		);

		$result = $this->Model->getImageSize(TMP . 'thumbnail2.jpg');
		$this->assertEquals($result, [226, 300, 'x' => 226, 'y' => 300]);
	}

	/**
	 * testSquareCenterCrop
	 *
	 * @return void
	 */
	public function testSquareCenterCrop() {
		$image = Plugin::path('Burzum/Imagine') . 'tests' . DS . 'Fixture' . DS . 'titus.jpg';
		$this->Model->processImage($image, TMP . 'testSquareCenterCrop.jpg', [], [
			'squareCenterCrop' => [
				'size' => 255
			]
		]);
		$result = $this->Model->getImageSize(TMP . 'testSquareCenterCrop.jpg');
		$this->assertEquals($result, [255, 255, 'x' => 255, 'y' => 255]);
	}

	/**
	 * testgetImageSize
	 *
	 * @return void
	 */
	public function testGetImageSize() {
		$image = Plugin::path('Burzum/Imagine') . 'tests' . DS . 'Fixture' . DS . 'titus.jpg';
		$result = $this->Model->getImageSize($image);
		$this->assertEquals($result, [500, 664, 'x' => 500, 'y' => 664]);
	}

	/**
	 * testWidenAndHeighten
	 *
	 * @return void
	 */
	public function testWidenAndHeighten() {
		$image = Plugin::path('Burzum/Imagine') . 'tests' . DS . 'Fixture' . DS . 'titus.jpg';

		$result = $this->Model->getImageSize($image);
		$this->assertEquals($result, [500, 664, 'x' => 500, 'y' => 664]);

		// Width
		$this->Model->processImage($image, TMP . 'widen.jpg', [], [
			'widen' => [
				'size' => 200
			]
		]);

		$result = $this->Model->getImageSize(TMP . 'widen.jpg');
		$this->assertEquals($result, [200, 266, 'x' => 200, 'y' => 266]);

		// Height
		$this->Model->processImage($image, TMP . 'heighten.jpg', [], [
				'heighten' => [
					'size' => 200
				]
			]
		);

		$result = $this->Model->getImageSize(TMP . 'heighten.jpg');
		$this->assertEquals($result, [151, 200, 'x' => 151, 'y' => 200]);
	}

	/**
	 * testScale
	 *
	 * @return void
	 */
	public function testScale() {
		$image = Plugin::path('Burzum/Imagine') . 'tests' . DS . 'Fixture' . DS . 'titus.jpg';

		// Scale
		$this->Model->processImage($image, TMP . 'scale-factor2.jpg', [], [
			'scale' => [
				'factor' => 2
			]
		]);

		$result = $this->Model->getImageSize(TMP . 'scale-factor2.jpg');
		$this->assertEquals($result, [1000, 1328, 'x' => 1000, 'y' => 1328]);

		// Scale2
		$this->Model->processImage($image, TMP . 'scale-factor1.25.jpg', [], [
			'scale' => [
				'factor' => 1.25
			]
		]);

		$result = $this->Model->getImageSize(TMP . 'scale-factor1.25.jpg');
		$this->assertEquals($result, [625, 830, 'x' => 625, 'y' => 830]);
	}
}
