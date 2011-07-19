<?php
App::import('Lib', 'Imagine.ImagineLoader');

class ImagineBehavior extends ModelBehavior {

/**
 * Settings array
 *
 * @var array
 */
	public $settings = array();

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
 * @param object Model instance
 * @param string image path
 * @param
 * @return boolean
 */
	public function processImage(Model $Model, $image = '', $output = false,  $rules = array()) {
		if (empty($rules) || !($this->checkSignature($Model, $rules))) {
			//return false;
		}

		$ImageObject = $this->Imagine->open($image);

		foreach ($rules as $operation  => $params) {
			if (method_exists($Model, $operation)) {
				$object->{$operation}($ImageObject, $this->buildParams($Model, $params));
			} elseif (method_exists($this, $operation)) {
				$object = $this;
			} else {
				return false;
			}

			if (is_string($params)) {
				$params = $this->buildParams($Model, $params);
			}

			$object->{$operation}(&$ImageObject, $params);
		}

		if ($output === false) {
			return $ImageObject;
		}

		return $ImageObject->save($output);
	}

/**
 * Test
 *
 * @param array
 * @return array
 */
	public function buildParams(Model $Model, $params = array()) {
		$tmpParams = explode(';', $params);
		$resultParams = array();
		foreach ($tmpParams as &$param) {
			list($key, $value) = explode('|', $param);
			$resultParams[$key] = $value;
		}
		return $resultParams;
	}

	public function crop($Image, $options = array()) {
		$Image->resize(new Imagine\Image\Box(150, 150))
			->crop(new Imagine\Image\Point(0, 0), new Imagine\Image\Box(150, 150));
	}

	public function thumbnail($Image, $options = array()) {
		$Image = $Image->thumbnail(new Imagine\Image\Box($options['width'], $options['height']));
	}

	public function resize($Image, $options = array()) {
		$Image->resize(new Imagine\Image\Box($options['width'], $options['height']));
	}

/**
 * Checks the hash for signed url params
 *
 * @param 
 * @param 
 * @return 
 */
	public function checkSignature(Model $Model, $options = array()) {
		$mediaSalt = Configure::read('Imagine.salt');
		if (empty($mediaSalt)) {
			throw new Exception(__('Please configure Media.salt using Configure::write(\'Imagine.salt\', \'YOUR-SALT-VALUE\')', true));
		}

		if (isset($options['hash'])) {
			$signature = $options['hash'];
			unset($options['hash']);
		} else {
			return false;
		}

		foreach ($options as $key => $val) {
			$options[$key] = urlencode($val);
		}

		App::uses('Security', 'Utility');
		ksort($options);
		return urlencode(Security::hash(serialize($options) . $mediaSalt)) == $signature;
	}

}