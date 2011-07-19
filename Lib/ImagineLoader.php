<?php
namespace Imagine;

class ImagineLoader {

/**
 * Loader for the Imagine Namespace
 *
 * @param string
 * @return void
 */
	public static function load($name) {
		$imagineBase = \Configure::read('Imagine.base');
		if (empty($imagineBase)) {
			$imagineBase = \CakePlugin::path('Imagine') . 'Vendor' . DS . 'Imagine' . DS . 'lib' . DS;
		}

		$filePath = $imagineBase . $name . '.php';
		if (file_exists($filePath)) {
			require_once($filePath);
			return;
		}

		$imagineBase = $imagineBase . 'Image' . DS;
		if (file_exists($imagineBase . $name . '.php')) {
			require_once($imagineBase . $name . '.php');
			return;
		}
	}
}

spl_autoload_register(__NAMESPACE__ .'\ImagineLoader::load');