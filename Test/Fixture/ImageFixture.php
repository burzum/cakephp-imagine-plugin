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

class ImageFixture extends CakeTestFixture {

/**
 * name property
 *
 * @var string
 */
	public $name = 'Image';

/**
 * fields property
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false));

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array('title' => 'First Image'),
		array('title' => 'Second Image'));
}
