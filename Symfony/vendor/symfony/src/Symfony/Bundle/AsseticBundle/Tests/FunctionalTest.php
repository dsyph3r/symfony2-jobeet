<?php

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\Bundle\AsseticBundle\Tests;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Util\Filesystem;

/**
 * @group functional
 */
class FunctionalTest extends \PHPUnit_Framework_TestCase
{
    protected $cacheDir;

    protected function setUp()
    {
        if (!class_exists('Assetic\\AssetManager')) {
            $this->markTestSkipped('Assetic is not available.');
        }

        $this->cacheDir = __DIR__.'/Resources/cache';
        if (file_exists($this->cacheDir)) {
            $filesystem = new Filesystem();
            $filesystem->remove($this->cacheDir);
        }

        mkdir($this->cacheDir, 0777, true);
    }

    protected function tearDown()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->cacheDir);
    }

    /**
     * @dataProvider provideDebugAndAssetCount
     */
    public function testKernel($debug, $count)
    {
        $kernel = new TestKernel('test', $debug);
        $kernel->boot();
        $container = $kernel->getContainer();

        $names = $container->get('assetic.asset_manager')->getNames();

        $this->assertEquals($count, count($names));
    }

    /**
     * @dataProvider provideDebugAndAssetCount
     */
    public function testRoutes($debug, $count)
    {
        $kernel = new TestKernel('test', $debug);
        $kernel->boot();
        $container = $kernel->getContainer();

        $routes = $container->get('router')->getRouteCollection()->all();

        $matches = 0;
        foreach (array_keys($routes) as $name) {
            if (0 === strpos($name, 'assetic_')) {
                ++$matches;
            }
        }

        $this->assertEquals($count, $matches);
    }

    public function testTwigRenderDebug()
    {
        $kernel = new TestKernel('test', true);
        $kernel->boot();
        $container = $kernel->getContainer();
        $container->enterScope('request');
        $container->set('request', new Request());

        $content = $container->get('templating')->render('::layout.html.twig');
        $crawler = new Crawler($content);

        $this->assertEquals(3, count($crawler->filter('link[href$=".css"]')));
        $this->assertEquals(2, count($crawler->filter('script[src$=".js"]')));
    }

    public function testPhpRenderDebug()
    {
        $kernel = new TestKernel('test', true);
        $kernel->boot();
        $container = $kernel->getContainer();
        $container->enterScope('request');
        $container->set('request', new Request());

        $content = $container->get('templating')->render('::layout.html.php');
        $crawler = new Crawler($content);

        $this->assertEquals(3, count($crawler->filter('link[href$=".css"]')));
        $this->assertEquals(2, count($crawler->filter('script[src$=".js"]')));
    }

    public function provideDebugAndAssetCount()
    {
        return array(
            array(true, 5),
            array(false, 2),
        );
    }
}
