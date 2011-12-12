<?php
App::uses('Component', 'Controller/Component');

class ImagineComponent extends Component {

/**
 * Settings
 *
 * @var array
 */
	public $settings = array(
		'hashField' => 'hash',
		'actions' => array());

/**
 * Controller instance
 *
 * @var object
 */
	public $Controller;

/**
 * Constructor
 *
 * @param ComponentCollection $collection
 * @param array $settings
 * @return void
 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		$this->settings = Set::merge($this->settings, $settings);
		parent::__construct($collection, $this->settings);
	}

/**
 * Start Up
 *
 * @param Controller $Controller
 * @return void
 */
	public function startUp(Controller $Controller) {
		$this->Controller = $Controller;
		if (!empty($this->settings['actions'])) {
			if (in_array($this->Controlle->action, $this->settings['actions'])) {
				$this->checkHash();
			}
		}
	}

/**
 * Creates a hash based on the named params but ignores the hash field
 *
 * The hash can also be used to determine if there is already a cached version
 * of the requested image that was processed with these params. How you do that
 * is up to you.
 *
 * @return string
 */
	public function getHash() {
		$mediaSalt = Configure::read('Imagine.salt');
		if (empty($mediaSalt)) {
			throw new Exception(__('Please configure Imagine.salt using Configure::write(\'Imagine.salt\', \'YOUR-SALT-VALUE\')', true));
		}

		if (!empty($this->Controller->request->params['named'])) {
			$params = $this->Controller->request->params['named'];
			unset($params[$this->settings['hashField']]);
			ksort($params);
			return urlencode(Security::hash(serialize($params) . $mediaSalt));
		}
		return '';
	}

/**
 * Compares the hash passed within the named args with the hash calculated based
 * on the other named args and the imagine salt
 *
 * This is done to avoid that people can randomly generate tons of images by
 * just incrementing the width and height for example in the url.
 *
 * @param boolean $error If set to false no 404 page will be rendered if the hash is wrong
 * @return boolean True if the hashes match
 */
	public function checkHash($error = true) {
		if (!isset($this->Controller->request->params['named'][$this->settings['hashField']])) {
			return false;
		}

		$result = $this->Controller->request->params['named'][$this->settings['hashField']] == $this->getHash();

		if (!$result && $error) {
			throw new NotFoundException();
		}

		return $result;
	}

}