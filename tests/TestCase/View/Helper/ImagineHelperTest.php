<?php
declare(strict_types=1);

/**
 * Copyright 2011-2017, Florian Krämer
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * Copyright 2011-2017, Florian Krämer
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace Burzum\Imagine\Test\TestCase\View\Helper;

use Burzum\Imagine\View\Helper\ImagineHelper;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * ImagineHelperTest class
 *
 * @property ImagineHelper $Imagine
 */
class ImagineHelperTest extends TestCase
{
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        Router::reload();
        Router::connect('/:controller/:action');
        Router::connect('/:controller/:action/*');

        Configure::write('Imagine.salt', 'this-is-a-nice-salt');
        $controller = null;
        $View = new View($controller);
        $this->Imagine = new ImagineHelper($View);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Imagine);
    }

    /**
     * testUrl method
     *
     * @return void
     */
    public function testUrl(): void
    {
        $result = $this->Imagine->url(
            [
                'controller' => 'Images',
                'action' => 'display',
                1,
            ],
            false,
            [
                'thumbnail' => [
                    'width' => 200,
                    'height' => 150,
                ],
            ]
        );
        $expected = '/Images/display/1?thumbnail=width%7C200%3Bheight%7C150&hash=69aa9f46cdc5a200dc7539fc10eec00f2ba89023';
        $this->assertEquals($result, $expected);
    }

    /**
     * testHash method
     *
     * @return void
     */
    public function testHash(): void
    {
        $options = $this->Imagine->pack([
                'thumbnail' => [
                    'width' => 200,
                    'height' => 150,
                ],
            ]);
        $result = $this->Imagine->hash($options);
        $this->assertEquals($result, '69aa9f46cdc5a200dc7539fc10eec00f2ba89023');
    }

    /**
     * testHash method
     *
     * @return void
     */
    public function testMissingSaltForHash(): void
    {
        $this->expectException(\Exception::class);
        Configure::write('Imagine.salt', null);
        $this->Imagine->hash(['foo']);
    }

    /**
     * testUrl method
     *
     * @return void
     */
    public function testPack(): void
    {
        $result = $this->Imagine->pack([
                'thumbnail' => [
                    'width' => 200,
                    'height' => 150,
                ],
            ]);

        $this->assertEquals($result, ['thumbnail' => 'width|200;height|150']);
    }
}
