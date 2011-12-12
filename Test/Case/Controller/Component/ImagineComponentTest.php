<?php
App::import('Controller', 'Controller', false);
App::import('Imagine', 'Imagine.Controller/Component');

if (!class_exists('ArticlesTestController')) {
	class ImagineImagesTestController extends Controller {

	/**
	 * @var string
	 */
		public $name = 'Images';

	/**
	 * @var array
	 */
		public $uses = array('Images');

	/**
	 * @var array
	 */
		public $components = array(
			'Session',
			'Imagine.Imagine');

	/**
	 * Redirect url
	 * @var mixed
	 */
		public $redirectUrl = null;
		
	/**
	 * 
	 */
		public function beforeFilter() {
			parent::beforeFilter();
			$this->Imagine->userModel = 'UserModel';
		}

	/**
	 * 
	 */
		public function redirect($url, $status = NULL, $exit = true) {
			$this->redirectUrl = $url;
		}
	}
}

/**
 * Imagine Component Test
 *
 * @package Imagine
 * @subpackage Imagine.tests.cases.components
 */
class ImagineComponentTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.Imagine.Image');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Configure::write('Imagine.salt', 'this-is-a-nice-salt');
		$this->Controller = new ImagineImagesTestController();
		$this->Controller->constructClasses();
		$this->Controller->Components->init($this->Controller);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Controller);
		ClassRegistry::flush();
	}

	public function testGetHash() {
		$this->Controller->Imagine->getHash();
	}

	public function testCheckHash() {
		$this->Controller->Imagine->checkHash();
	}

}
