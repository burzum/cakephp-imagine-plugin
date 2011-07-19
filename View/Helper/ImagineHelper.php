<?php
App::uses('Utilities', 'Security');
class ImagineHelper extends AppHelper {
/**
 * Helpers
 *
 * @var array $helpers
 * @access public
 */
	public $helpers = array('Html');

/**
 * Finds URL for specified action and sign it.
 *
 * Returns an URL pointing to a combination of controller and action. Param
 *
 * @param  mixed  $url    Cake-relative URL, like "/products/edit/92" or "/presidents/elect/4"
 *                        or an array specifying any of the following: 'controller', 'action',
 *                        and/or 'plugin', in addition to named arguments (keyed array elements),
 *                        and standard URL arguments (indexed array elements)
 * @param array $options List of named arguments that need to sign
 * @param boolean $full If true, the full base URL will be prepended to the result
 * @return string Full translated signed URL with base path and with
 * @access public
 */
	public function url($url = array(), $options, $full = false) {
		if (is_string($url)) {
			$url = array_merge(array('plugin' => 'media', 'admin' => false, 'controller' => 'media', 'action' => 'image'), array($url));
		}
		$options = $this->pack($options);
		$options['hash'] = $this->sign($options);

		$url = array_merge((array)$url, $options + array('base' => false));
		return $this->Html->url($url, $full);
	}

/**
 * Sign
 *
 * @param array $options
 * @return string
 * @access public
 */
	public function sign($options) {
		$mediaSalt = Configure::read('Imagine.salt');
		if (empty($mediaSalt)) {
			throw new Exception(__('Please configure Imagine.salt using Configure::write(\'Imagine.salt\', \'YOUR-SALT-VALUE\')', true));
		}
		ksort($options);
		return urlencode(Security::hash(serialize($options) . $mediaSalt));
	}

/**
 * Pack
 *
 * @param array $options
 * @return array
 * @access public
 */
	public function pack($options) {
		$result = array();
		foreach ($options as $operation => $data) {
			$tmp = array();
			foreach ($data as $key => $value) {
				if (is_string($value) || is_numeric($value)) {
					$tmp[] = "$key|$value";
				}
			}
			$result[$operation] = urlencode(join(';', $tmp));
		}
		return $result;
	}

}