<?php
/**
 * Copyright 2011-2017, Florian Krämer
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * Copyright 2011-2017, Florian Krämer
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace Burzum\Imagine\View\Helper;

use Cake\Core\Configure;
use Cake\View\Helper;
use Cake\Utility\Security;
use Cake\Routing\Router;
use RuntimeException;

/**
 * CakePHP Imagine Plugin
 *
 * @package Imagine.View.Helper
 */
class ImagineHelper extends Helper {

	/**
	 * Finds URL for specified action and sign it.
	 *
	 * Returns an URL pointing to a combination of controller and action. Param
	 *
	 * @param  mixed  $url    Cake-relative URL, like "/products/edit/92" or "/presidents/elect/4"
	 *                        or an array specifying any of the following: 'controller', 'action',
	 *                        and/or 'plugin', in addition to named arguments (keyed array elements),
	 *                        and standard URL arguments (indexed array elements)
	 * @param bool $full If true, the full base URL will be prepended to the result
	 * @param array $options List of named arguments that need to sign
	 * @return string Full translated signed URL with base path and with
	 */
	public function url($url = null, $full = false, $options = []) {
		if (is_string($url)) {
			$url = array_merge([
				'plugin' => 'media',
				'admin' => false,
				'controller' => 'media',
				'action' => 'image'
			], [
				$url
			]);
		}

		// backward compatibility check, switches params 2 and 3
		if (is_bool($options)) {
			$tmp = $options;
			$options = $full;
			$full = $tmp;
		}

		$options = $this->pack($options);
		$options['hash'] = $this->hash($options);

		$url = array_merge((array)$url, $options + ['base' => false]);
		return Router::url($url, $full);
	}

	/**
	 * Signs the url with a salted hash
	 *
	 * @throws \RuntimeException
	 * @param array $options
	 * @return string
	 */
	public function hash($options) {
		$mediaSalt = Configure::read('Imagine.salt');
		if (empty($mediaSalt)) {
			throw new RuntimeException(sprintf(
				'Please configure `%s` using `%s`',
				'Imagine.salt',
				'Configure::write(\'Imagine.salt\', \'YOUR-SALT-VALUE\')'
			));
		}
		ksort($options);
		return urlencode(Security::hash(serialize($options) . $mediaSalt));
	}

	/**
	 * Packs the image options array into an array of named arguments that can be used in a cake url
	 *
	 * @param array $options
	 * @return array
	 */
	public function pack($options) {
		$result = [];
		foreach ($options as $operation => $data) {
			$tmp = [];
			foreach ($data as $key => $value) {
				if (is_string($value) || is_numeric($value)) {
					$tmp[] = "$key|$value";
				}
			}
			$result[$operation] = implode(';', $tmp);
		}

		return $result;
	}
}
