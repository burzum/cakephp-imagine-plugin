<?php
declare(strict_types = 1);

/**
 * Copyright 2011-2017, Florian Krämer
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * Copyright 2011-2017, Florian Krämer
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace Burzum\Imagine\Lib;

use Cake\Core\InstanceConfigTrait;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use InvalidArgumentException;
use RuntimeException;

/**
 * Image Processor
 */
class ImageProcessor
{

    use InstanceConfigTrait;

    /**
     * Default settings
     *
     * @var array
     */
    protected $_defaultConfig = [
        'engine' => 'Gd'
    ];

    /**
     * Imagine Engine Instance
     *
     * @var \Imagine\Image\AbstractImagine;
     */
    protected $_imagine = null;

    /**
     * Image object instance
     *
     * @var \Imagine\Image\AbstractImage
     */
    protected $_image = null;

    /**
     * Constructor
     *
     * @param array $config Configuration options
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    /**
     * Get the imagine object
     *
     * @param bool $renew Renew the imagine instance, default is false
     * @return \Imagine\Image\ImageInterface; Imagine image object
     */
    public function imagine($renew = false)
    {
        if (empty($this->_imagine) || $renew === true) {
            $engine = $this->getConfig('engine');

            if (($engine === 'Imagick' || $engine === 'imagick') && !extension_loaded('imagick')) {
                throw new RuntimeException('Imagick php extension is not loaded! Please see http://php.net/manual/en/imagick.installation.php');
            }

            $class = '\Imagine\\' . $this->getConfig('engine') . '\Imagine';

            if (!class_exists($class)) {
                throw new RuntimeException(sprintf('Imagine engine `%s` does not exist!', $class));
            }

            $this->_imagine = new $class();

            return $this->_imagine;
        }

        return $this->_imagine;
    }

    /**
     * Opens an image file for processing.
     *
     * @param string $image Image file.
     * @return $this
     */
    public function open($image)
    {
        if (!file_exists($image)) {
            throw new \RuntimeException(sprintf('File `%s` does not exist!', $image));
        }
        $this->_image = $this->imagine()->open($image);

        return $this;
    }

    /**
     * Loads an image from a binary $string
     *
     * @param string $image Binary image string
     * @return $this
     */
    public function load($image)
    {
        $this->_image = $this->imagine()->load($image);

        return $this;
    }

    /**
     * Loads an image from a resource $resource
     *
     * @param resource $image Image resource
     * @return $this
     */
    public function read($image)
    {
        $this->_image = $this->imagine()->read($image);

        return $this;
    }

    /**
     * Gets the image object.
     *
     * @return \Imagine\Image\AbstractImage;
     */
    public function image()
    {
        return $this->_image;
    }

    /**
     * Loads an image and applies operations on it
     *
     * Caching and taking care of the file storage is NOT the purpose of this method!
     *
     * @param null|string $output Output file
     * @param array $operations Operations
     * @param array $imagineOptions Imagine options
     * @throws \BadMethodCallException
     * @internal param string $image source image path
     * @internal param $mixed
     * @internal param \Imagine $array image objects save() 2nd parameter options
     * @return bool|\Imagine\Image\AbstractImage
     */
    public function batchProcess($output = null, array $operations = [], array $imagineOptions = [])
    {
        foreach ($operations as $operation => $params) {
            if (method_exists($this, $operation)) {
                $this->{$operation}($params);
            } else {
                throw new BadMethodCallException(sprintf('Unsupported image operation %s!', $operation));
            }
        }

        if ($output === null) {
            return $this->_image;
        }

        return $this->save($output, $imagineOptions);
    }

    /**
     * Saves an image.
     *
     * @param string $output Output filename.
     * @param array $options Imagine image saving options.
     * @return bool
     */
    public function save($output, array $options = [])
    {
        $this->_image->save($output, $options);
        $this->_image = null;

        return true;
    }

    /**
     * Compatibility method for legacy reasons.
     *
     * @deprecated Use batchProcess() instead.
     * @param null|string $output Output file
     * @param array $imagineOptions Imagine options
     * @param array $operations Operations
     * @return bool|\Imagine\Image\AbstractImage
     */
    public function processImage($output = null, array $imagineOptions = [], array $operations = [])
    {
        user_error('processImage() is deprecated, use batchProcess() instead!', E_NOTICE);

        return $this->batchProcess($output, $operations, $imagineOptions);
    }

    /**
     * Turns the operations and their params into a string that can be used in a
     * file name to cache an image.
     *
     * Suffix your image with the string generated by this method to be able to
     * batch delete a file that has versions of it cached. The intended usage of
     * this is to store the files as my_horse.thumbnail+width-100-height+100.jpg
     * for example.
     *
     * So after upload store your image meta data in a db, give the filename the
     * id of the record and suffix it with this string and store the string also
     * in the db. In the views, if no further control over the image access is
     * needed, you can simply directly link the image like $this->Html->image('/images/05/04/61/my_horse.thumbnail+width-100-height+100.jpg');
     *
     * @param array $operations Imagine image operations
     * @param array $separators Separators
     * @param bool $hash Hash the string, default false
     * @return string Filename compatible String representation of the operations
     * @link http://support.microsoft.com/kb/177506
     */
    public function operationsToString($operations, $separators = [], $hash = false)
    {
        return ImagineUtility::operationsToString($operations, $separators, $hash);
    }

    /**
     * Generates a hash based on an array of image operations.
     *
     * @param array $operations Operations to hash
     * @param int $hashLength Hash length, default 8
     * @return string
     */
    public function hashImageOperations($operations, $hashLength = 8)
    {
        return ImagineUtility::hashImageOperations($operations, $hashLength);
    }

    /**
     * Wrapper for Imagines crop
     *
     * @param array $options Array of options for processing the image
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function crop(array $options = [])
    {
        if (empty($options['height']) || empty($options['width'])) {
            throw new \InvalidArgumentException('You have to pass height and width in the options!');
        }

        $defaults = [
            'cropX' => 0,
            'cropY' => 0
        ];

        $options = array_merge($defaults, $options);

        $this->_image->crop(
            new Point($options['cropX'], $options['cropY']),
            new Box($options['width'], $options['height'])
        );

        return $this;
    }

    /**
     * Crops an image based on its widht or height, crops it to a square and resizes it to the given size
     *
     * @param array $options Array of options for processing the image.
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function squareCenterCrop(array $options = [])
    {
        if (empty($options['size'])) {
            throw new \InvalidArgumentException(__d('imagine', 'You have to pass size in the options!'));
        }

        $imageSize = $this->getImageSize($this->_image);

        $width = $imageSize['x'];
        $height = $imageSize['y'];

        if (isset($options['preventUpscale']) && $options['preventUpscale'] === true) {
            if ($options['size'] > $width || $options['size'] > $height) {
                return $this;
            }
        }

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

        $this->_image->crop(new \Imagine\Image\Point($x, $y), new \Imagine\Image\Box($x2, $y2));
        $this->_image->resize(new \Imagine\Image\Box($options['size'], $options['size']));

        return $this;
    }

    /**
     * Widen
     *
     * @param array $options Array of options for processing the image.
     * @throws \InvalidArgumentException
     * @return void
     */
    public function widen(array $options = [])
    {
        if (empty($options['size'])) {
            throw new \InvalidArgumentException(__d('imagine', 'You must pass a size value!'));
        }

        if (isset($options['preventUpscale']) && $options['preventUpscale'] === true) {
            $imageSize = $this->getImageSize();
            if ($options['size'] > $imageSize['x']) {
                return;
            }
        }

        $this->widenAndHeighten(['width' => $options['size']]);

        return $this;
    }

    /**
     * Heighten
     *
     * @param array $options Array of options for processing the image.
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function heighten(array $options = [])
    {
        if (empty($options['size'])) {
            throw new InvalidArgumentException(__d('imagine', 'You must pass a size value!'));
        }

        if (isset($options['preventUpscale']) && $options['preventUpscale'] === true) {
            $imageSize = $this->getImageSize();
            if ($options['size'] > $imageSize['y']) {
                return $this;
            }
        }

        $this->widenAndHeighten(['height' => $options['size']]);

        return $this;
    }

    /**
     * WidenAndHeighten
     *
     * Options:
     * - `width`: Width of the image
     * - `height`: Height of the image
     * - `noUpScale` Boolean
     *
     * @param array $options Array of options for processing the image.
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function widenAndHeighten(array $options = [])
    {
        if (empty($options['height']) && empty($options['width']) && empty($options['size'])) {
            throw new \InvalidArgumentException(__d('imagine', 'You have to pass a height, width or size!'));
        }

        if (!empty($options['height']) && !empty($options['width'])) {
            throw new \InvalidArgumentException(__d('imagine', 'You can only scale by width or height!'));
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

        $imageSize = $this->getImageSize($this->_image);
        $width = $imageSize[0];
        $height = $imageSize[1];

        if (isset($options['noUpScale'])) {
            if ($method === 'widen') {
                if ($size > $width) {
                    throw new \InvalidArgumentException('You can not scale up!');
                }
            } elseif ('heighten') {
                if ($size > $height) {
                    throw new \InvalidArgumentException('You can not scale up!');
                }
            }
        }

        if (isset($options['noDownScale'])) {
            if ($method === 'widen') {
                if ($size < $width) {
                    throw new \InvalidArgumentException('You can not scale down!');
                }
            } elseif ('heighten') {
                if ($size < $height) {
                    throw new \InvalidArgumentException('You can not scale down!');
                }
            }
        }

        $Box = new Box($width, $height);
        $Box = $Box->{$method}($size);
        $this->_image->resize($Box);

        return $this;
    }

    /**
     * Heighten
     *
     * Options:
     * - `factor`: Float value, factor to scale the image up
     * - `preventUpscale`: Boolean
     *
     * @param array $options Options
     * @throws \InvalidArgumentException
     * @return void
     */
    public function scale(array $options = [])
    {
        if (empty($options['factor'])) {
            throw new \InvalidArgumentException(__d('imagine', 'You must pass a factor value!'));
        }

        if (isset($options['preventUpscale']) && $options['preventUpscale'] === true && $options['factor'] > 1.0) {
            return;
        }

        $imageSize = $this->getImageSize();
        $width = $imageSize[0];
        $height = $imageSize[1];

        $Box = new Box($width, $height);
        $Box = $Box->scale($options['factor']);
        $this->_image->resize($Box);

        return $this;
    }

    /**
     * Wrapper for Imagine flipHorizontally and flipVertically
     *
     * @param array $options Array of options for processing the image.
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function flip(array $options = [])
    {
        if (!isset($options['direction'])) {
            $options['direction'] = 'vertically';
        }

        if (!in_array($options['direction'], ['vertically', 'horizontally'])) {
            throw new \InvalidArgumentException(__d('imagine', 'Invalid direction, use vertically or horizontally'));
        }

        $method = 'flip' . $options['direction'];
        $this->_image->{$method}();

        return $this;
    }

    /**
     * Wrapper for rotate
     *
     * @param array $options Array of options for processing the image.
     * @return $this
     */
    public function rotate(array $options = [])
    {
        $this->_image->rotate($options['degree']);

        return $this;
    }

    /**
     * Wrapper for Imagines thumbnail.
     *
     * This method had a bunch of issues and the code inside the method is a
     * workaround! Please see:
     *
     * @link https://github.com/burzum/cakephp-imagine-plugin/issues/42
     * @link https://github.com/avalanche123/Imagine/issues/478
     *
     * @throws \InvalidArgumentException
     * @param array $options Array of options for processing the image.
     * @throws InvalidArgumentException if no height or width was passed
     * @return $this
     */
    public function thumbnail(array $options = [])
    {
        if (empty($options['height']) || empty($options['width'])) {
            throw new \InvalidArgumentException(__d('imagine', 'You have to pass height and width in the options!'));
        }

        $imageSize = $this->getImageSize();
        if (isset($options['preventUpscale']) && $options['preventUpscale'] === true) {
            if (isset($options['height']) && $options['height'] > $imageSize['y']) {
                return $this;
            }
            if (isset($options['width']) && $options['width'] > $imageSize['x']) {
                return $this;
            }
        }

        $mode = ImageInterface::THUMBNAIL_INSET;
        if (isset($options['mode']) && $options['mode'] === 'outbound') {
            $mode = ImageInterface::THUMBNAIL_OUTBOUND;
        }

        $filter = ImageInterface::FILTER_UNDEFINED;
        if (isset($options['filter'])) {
            $filter = $options['filter'];
        }

        $size = new Box($options['width'], $options['height']);
        $imageSize = $this->_image->getSize();
        $ratios = [
            $size->getWidth() / $imageSize->getWidth(),
            $size->getHeight() / $imageSize->getHeight()
        ];

        // if target width is larger than image width
        // AND target height is longer than image height
        if ($size->contains($imageSize)) {
            return $this->_image;
        }

        if ($mode === ImageInterface::THUMBNAIL_INSET) {
            $ratio = min($ratios);
        } else {
            $ratio = max($ratios);
        }

        if ($mode === ImageInterface::THUMBNAIL_OUTBOUND) {
            if (!$imageSize->contains($size)) {
                $size = new Box(
                    min($imageSize->getWidth(), $size->getWidth()),
                    min($imageSize->getHeight(), $size->getHeight())
                );
            } else {
                $imageSize = $this->_image->getSize()->scale($ratio);
                $this->_image->resize($imageSize, $filter);
            }
            $this->_image->crop(new Point(
                max(0, round(($imageSize->getWidth() - $size->getWidth()) / 2)),
                max(0, round(($imageSize->getHeight() - $size->getHeight()) / 2))
            ), $size);
        } else {
            if (!$imageSize->contains($size)) {
                $imageSize = $imageSize->scale($ratio);
                $this->_image->resize($imageSize, $filter);
            } else {
                $imageSize = $this->_image->getSize()->scale($ratio);
                $this->_image->resize($imageSize, $filter);
            }
        }

        return $this;
    }

    /**
     * Wrapper for Imagines resize
     *
     * @param array $options Array of options for processing the image
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function resize(array $options = [])
    {
        if (empty($options['height']) || empty($options['width'])) {
            throw new \InvalidArgumentException(__d('imagine', 'You have to pass height and width in the options!'));
        }

        $this->_image->resize(new Box($options['width'], $options['height']));

        return $this;
    }

    /**
     * Gets the size of an image
     *
     * @param string|null $Image Image object or string of a file name
     * @return array first value is width, second height
     * @see \Imagine\Image\ImageInterface::getSize()
     */
    public function getImageSize($Image = null)
    {
        $Image = $this->_getImage($Image);
        $BoxInterface = $Image->getSize($Image);

        return [
            $BoxInterface->getWidth(),
            $BoxInterface->getHeight(),
            'x' => $BoxInterface->getWidth(),
            'y' => $BoxInterface->getHeight()
        ];
    }

    /**
     * Gets an image from a file string or returns the image object that is
     * loaded in the ImageProcessor::_image property.
     *
     * @param string|null $Image Image
     * @return \Imagine\Image\AbstractImage
     */
    protected function _getImage($Image = null)
    {
        if (is_string($Image)) {
            $class = 'Imagine\\' . $this->getConfig('engine') . '\Imagine';
            $Imagine = new $class();

            return $Imagine->open($Image);
        }

        if (!empty($this->_image)) {
            return $this->_image;
        }

        throw new RuntimeException('Could not get the image object!');
    }
}
