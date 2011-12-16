<?php
App::import('Lib', 'Imagine.ImagineLoader');

class ImagineBehavior extends ModelBehavior {

/**
 * Settings array
 *
 * @var array
 */
	public $settings = array();

/**
 * Default settings array
 *
 * @var array
 */
	protected $_defaults = array(
		'engine' => 'Gd');

/**
 * Setup
 *
 * @param AppModel $Model
 * @param array $settings
 * @return void
 */
	public function setup(Model $Model, $settings = array()) {
		$this->settings = array_merge($this->_defaults, $settings);
		$class = 'Imagine\\' . $this->settings['engine'] . '\Imagine';
		$this->Imagine = new $class();
	}

/**
 * Loads an image and applies operations on it
 *
 * Caching and taking care of the file storage is NOT the purpose of this method!
 *
 * @param Model $Model
 * @param string $image source image path
 * @param mixed 
 * @param array Imagine image objects save() 2nd parameter options
 * @return boolean
 */
	public function processImage(Model $Model, $image, $output = null, $imagineOptions = array(), $operations = array()) {
		$ImageObject = $this->Imagine->open($image);
		foreach ($operations as $operation  => $params) {
			if (method_exists($Model, $operation)) {
				$Model->{$operation}(&$ImageObject, $params);
			} elseif (method_exists($this, $operation)) {
				$this->{$operation}($Model, &$ImageObject, $params);
			} else {
				return false;
			}
		}

		if (is_null($output)) {
			return $ImageObject;
		}

		return $ImageObject->save($output, $imagineOptions);
	}

/**
 * Wrapper for Imagines crop
 *
 * @param object Model
 * @param object Imagine Image Object
 * @param array Array of options for processing the image
 */
	public function crop(Model $Model, $Image, $options = array()) {
		$Image->resize(new Imagine\Image\Box($options['width'], $options['height']))
			->crop(new Imagine\Image\Point(0, 0), new Imagine\Image\Box($options['width'], $options['height']));
	}

/**
 * Wrapper for Imagines thumbnail
 *
 * @param object Model
 * @param object Imagine Image Object
 * @param array Array of options for processing the image
 */
	public function thumbnail(Model $Model, $Image, $options = array()) {
		$Image = $Image->thumbnail(new Imagine\Image\Box($options['width'], $options['height']));
	}

/**
 * Wrapper for Imagines resize
 *
 * @param object Model
 * @param object Imagine Image Object
 * @param array Array of options for processing the image
 */
	public function resize(Model $Model, $Image, $options = array()) {
		$Image->resize(new Imagine\Image\Box($options['width'], $options['height']));
	}

}