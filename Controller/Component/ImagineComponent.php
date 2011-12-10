<?php
App::uses('Component', 'Controller/Component');

class ImagineComponent extends Component {

	public $settings = array('hash' => 'hash');
	public $Controller;
/**
 * 
 */
	public function startUp(Controller $Controller) {
		$this->Controller = $Controller;
	}

/**
 * Creates a hash that can be used to create versions of an image based on the passed params
 *
 * @return string
 */
	public function hashParams() {
		$cacheHash = '';
		if (!empty($this->Controller->request->params['named'])) {
			$params = $this->Controller->request->params['named'];
			unset($params['hash']);
			ksort($params);
			return md5(serialize($params));
		}
		return $cacheHash;
	}

	public function render() {
		$this->_render();
	}

	protected function __render() {
		$this->Controller->set('cache', '3 days');
		$this->Controller->set('name', $image['Image']['filename']);
		$this->Controller->set('download', false);
		$this->Controller->set('extension', 'jpg');
		$this->Controller->set('id', $image['Image']['id'] . $cacheHash);
		$this->Controller->set('path', $image['Image']['path']);
		$this->render();
	}
}