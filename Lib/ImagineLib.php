<?php

use Imagine\Image\ImageInterface;

/**
 * Convenience wrappers for common image operations.
 *
 */
class ImagineLib {

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
		'engine' => 'Gd',
		'jpeg_quality' => null,
		'png_compression_level' => null,
		'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
		'resampling-filter' => ImageInterface::FILTER_LANCZOS,
	);

	public function __construct(array $settings = array()) {
		$this->_defaults = (array)Configure::read('Imagine') + $this->_defaults;
		$this->settings = $settings + $this->_defaults;
	}

/**
 * Imagine::create()
 *
 * @return Imagine object
 */
	public function create() {
		$class = 'Imagine\\' . $this->settings['engine'] . '\Imagine';
		return new $class();
	}

/**
 * Imagine::open()
 *
 * @param string $file
 * @return Imagine object
 */
	public function open($file) {
		$ImagineObject = $this->create();
		return $ImagineObject->open($file);
	}

/**
 * Imagine::save()
 *
 * @param Imagine $Image
 * @param string $file
 * @param array $options
 * @return bool Success
 */
	public function save($Image, $file, $options = array()) {
		$options += $this->_defaults;
		$options = array_filter($options);
		return $Image->save($file, $options);
	}

/**
 * Wrapper for Imagines crop
 *
 * @param $Image
 * @param array Array of options for processing the image
 * @throws InvalidArgumentException
 * @return void
 */
	public function crop($Image, $options = array()) {
		if (empty($options['height']) || empty($options['width'])) {
			throw new InvalidArgumentException(__d('Imagine', 'You have to pass height and width in the options!'));
		}

		$defaults = array(
			'cropX' => 0,
			'cropY' => 0
		);
		$options += $defaults;

		$Image->crop(new Imagine\Image\Point($options['cropX'], $options['cropY']), new Imagine\Image\Box($options['width'], $options['height']));
	}

/**
 * Crops an image based on its widht or height, crops it to a square and resizes it to the given size
 *
 * @param $Image
 * @param array Array of options for processing the image
 * @throws InvalidArgumentException
 * @return void
 */
	public function squareCenterCrop($Image, $options = array()) {
		if (empty($options['size'])) {
			throw new InvalidArgumentException(__d('Imagine', 'You have to pass size in the options!'));
		}

		$imageSize = $this->getImageSize($Image);

		$width = $imageSize[0];
		$height = $imageSize[1];

		if ($width > $height) {
			$x2 = $height;
			$y2 = $height;
			$x = ($width - $height) / 2;
			$y = 0;
		} else {
			$x2 = $width;
			$y2 = $width;
			$x = 0;
			$y = ($height - $width) / 2;
		}

		$Image->crop(new Imagine\Image\Point($x, $y), new Imagine\Image\Box($x2, $y2));
		$Image->resize(new Imagine\Image\Box($options['size'], $options['size']));
	}

/**
 * Widen
 *
 * @param $Image
 * @param array $options
 * @throws InvalidArgumentException
 * @return void
 */
	public function widen($Image, $options = array()) {
		if (empty($options['size'])) {
			throw new InvalidArgumentException(__d('Imagine', 'You must pass a size value!'));
		}
		$this->widenAndHeighten($Image, array('width' => $options['size']));
	}

/**
 * Heighten
 *
 * @param $Image
 * @param array $options
 * @throws InvalidArgumentException
 * @return void
 */
	public function heighten($Image, $options = array()) {
		if (empty($options['size'])) {
			throw new InvalidArgumentException(__d('Imagine', 'You must pass a size value!'));
		}
		$this->widenAndHeighten($Image, array('height' => $options['size']));
	}

/**
 * WidenAndHeighten
 *
 * @param $Image
 * @param array $options
 * @throws InvalidArgumentException
 * @return void
 */
	public function widenAndHeighten($Image, $options = array()) {
		if (empty($options['height']) && empty($options['width']) && empty($options['size'])) {
			throw new InvalidArgumentException(__d('Imagine', 'You have to pass a height, width or size!'));
		}

		if (!empty($options['height']) && !empty($options['width'])) {
			throw new InvalidArgumentException(__d('Imagine', 'You can only scale by width or height!'));
		}

		if (isset($options['width'])) {
			$size = $options['width'];
			$method = 'widen';
		} elseif (isset($options['height'])) {
			$size = $options['height'];
			$method = 'heighten';
		} else {
			$size = $options['size'];
			$method = 'scale';
		}

		$imageSize = $this->getImageSize($Image);
		$width = $imageSize[0];
		$height = $imageSize[1];

		if (isset($options['noUpScale'])) {
			if ($method === 'widen') {
				if ($size > $width) {
					throw new InvalidArgumentException(__d('Imagine', 'You can not scale up!'));
				}
			} elseif ('heighten') {
				if ($size > $height) {
					throw new InvalidArgumentException(__d('Imagine', 'You can not scale up!'));
				}
			}
		}

		if (isset($options['noDownScale'])) {
			if ($method === 'widen') {
				if ($size < $width) {
					throw new InvalidArgumentException(__d('Imagine', 'You can not scale down!'));
				}
			} elseif ('heighten') {
				if ($size < $height) {
					throw new InvalidArgumentException(__d('Imagine', 'You can not scale down!'));
				}
			}
		}

		$Box = new Imagine\Image\Box($width, $height);
		$Box = $Box->{$method}($size);
		$Image->resize($Box);
	}

/**
 * Heighten
 *
 * @param $Image
 * @param array $options
 * @throws InvalidArgumentException
 * @return void
 */
	public function scale($Image, $options = array()) {
		if (empty($options['factor'])) {
			throw new InvalidArgumentException(__d('Imagine', 'You must pass a factor value!'));
		}

		$imageSize = $this->getImageSize($Image);
		$width = $imageSize[0];
		$height = $imageSize[1];

		$Box = new Imagine\Image\Box($width, $height);
		$Box = $Box->scale($options['factor']);
		$Image->resize($Box);
	}

/**
 * Wrapper for Imagine flipHorizontally and flipVertically
 *
 * @param $Image
 * @param array Array of options for processing the image
 * @throws InvalidArgumentException
 * @return void
 */
	public function flip($Image, $options = array()) {
		$defaults = array(
			'direction' => 'vertically'
		);
		$options += $defaults;

		if (!in_array($options['direction'], array('vertically', 'horizontall'))) {
			throw new InvalidArgumentException(__d('Imagine', 'Invalid direction, use verticall or horizontall'));
		}
		$method = 'flip' . $options['direction'];
		$Image->{$method}();
	}

/**
 * Wrapper for rotate
 *
 * @param object Imagine Image Object
 * @param array Array of options for processing the image
 */
	public function rotate($Image, $options = array()) {
		$Image->rotate($options['degree']);
	}

/**
 * Wrapper for Imagines thumbnail
 *
 * @param object Imagine Image Object
 * @param array Array of options for processing the image
 */
	public function thumbnail(&$Image, $options = array()) {
		if (empty($options['height']) || empty($options['width'])) {
			throw new InvalidArgumentException(__d('Imagine', 'You have to pass height and width in the options!'));
		}

		$mode = ImageInterface::THUMBNAIL_INSET;
		if (isset($options['mode']) && $options['mode'] === 'outbound') {
			$mode = ImageInterface::THUMBNAIL_OUTBOUND;
		}
		$Image = $Image->thumbnail(new Imagine\Image\Box($options['width'], $options['height']), $mode);
	}

/**
 * Wrapper for Imagines resize
 *
 * @param object Imagine Image Object
 * @param array Array of options for processing the image
 */
	public function resize($Image, $options = array()) {
		if (empty($options['height']) || empty($options['width'])) {
			throw new InvalidArgumentException(__d('Imagine', 'You have to pass height and width in the options!'));
		}

		$Image->resize(new Imagine\Image\Box($options['width'], $options['height']));
	}

/**
 * Gets the size of an image
 *
 * @param mixed Imagine Image object or string of a file name
 * @return array first value is width, second height
 * @see ImageInterface::getSize()
 */
	public function getImageSize($Image) {
		if (is_string($Image)) {
			$class = 'Imagine\\' . $this->settings['engine'] . '\Imagine';
			$Imagine = new $class();
			$Image = $Imagine->open($Image);
		}

		$BoxInterface = $Image->getSize($Image);

		return array(
			$BoxInterface->getWidth(),
			$BoxInterface->getHeight(),
			'x' => $BoxInterface->getWidth(),
			'y' => $BoxInterface->getHeight()
		);
	}

}
