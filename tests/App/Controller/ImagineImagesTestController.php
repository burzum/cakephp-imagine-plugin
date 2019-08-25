<?php
declare(strict_types=1);

/**
 * Copyright 2011-2017, Florian Kramer
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * Copyright 2011-2017, Florian Kramer
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace Burzum\Imagine\Test\App\Controller;

use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\Http\Response;

/**
 * ImagineImagesTestController
 *
 * @property ImagineComponent $Imagine
 */
class ImagineImagesTestController extends Controller
{
    /**
     * @var string
     */
    public $name = 'Images';

    /**
     * @var array
     */
    public $uses = ['Images'];

    /**
     * Redirect url
     * @var mixed
     */
    public $redirectUrl = null;

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Burzum/Imagine.Imagine');
    }

    /**
     * @inheritdoc
     */
    public function beforeFilter(EventInterface $Event)
    {
        parent::beforeFilter($Event);
        $this->Imagine->userModel = 'UserModel';
    }

    /**
     * @inheritdoc
     */
    public function redirect($url, int $status = 302): ?Response
    {
        $this->redirectUrl = $url;

        return null;
    }
}
