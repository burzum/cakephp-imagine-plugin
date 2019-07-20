<?php
declare(strict_types=1);

/**
 * Copyright 2011-2017, Florian Kramer
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * Copyright 2011-2017, Florian Kramer
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace Burzum\Imagine\Test\App\Model\Table;

use Cake\ORM\Table;

/**
 * Class ImagineTestTable
 *
 * @package Burzum\Imagine\Test\TestCase\Model\Behavior
 * @method getImageProcessor(): \Burzum\Imagine\Lib\ImageProcessor
 * @method operationsToString($operations, $separators = [], $hash = false)
 * @method processImage($image, $output = null, $imagineOptions = [], $operations = [])
 * @method getImageSize($image)
 */
class ImagineTestTable extends Table
{
    public $name = 'ImagineTestModel';
}
