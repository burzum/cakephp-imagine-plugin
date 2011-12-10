<?php
App::uses('ImagineHelper', 'Imagine.View/Helper');
App::uses('View', 'View');

/**
 * ImagineHelperTest class
 *
 * @package       Imagine.Test.Case.View.Helper
 */
class ImagineHelperTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		Configure::write('Imagine.salt', 'this-is-a-nice-salt');
		$controller = null;
		$View = new View($controller);
		$this->Imagine = new ImagineHelper($View);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Imagine);
	}

/**
 * testUrl method
 *
 * @return void
 */
	public function testUrl() {
		$result = $this->Imagine->url(
			array(
				'controller' => 'Image',
				'action' => 'display',
				1),
			array(
				'thumbnail' => array
					'width' => 200,
					'height' => 150));

		$expected = '/image/display/1/thumbnail:width|200;height|150';
		$this->assertEqual($result, $expected);
	}

/**
 * testUrl method
 *
 * @return void
 */
	public function testSign() {
		
	}

/**
 * testUrl method
 *
 * @return void
 */
	public function testPack() {
		$result = $this->Imagine->pack(array(
			'thumbnail' => array(
				'width' => 200,
				'height' => 150)));

		$this->assertEqual($result, array('thumbnail' => 'width|200;height|150'));
	}

}
