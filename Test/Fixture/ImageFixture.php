<?php
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
