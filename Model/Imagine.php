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
namespace Imagine\Model;

use Cake\Core\Model;

class Imagine extends AppModel {

/**
 * Name
 *
 * @var string
 */
	public $name = 'Imagine';

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Imagine.Imagine'
	);

}