<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Tests\Component\EventDispatcher;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventDispatcherTest extends \PHPUnit_Framework_TestCase
{
    /* Some pseudo events */
    const preFoo = 'preFoo';
    const postFoo = 'postFoo';
    const preBar = 'preBar';
    const postBar = 'postBar';

    private $dispatcher;

    private $listener;

    protected function setUp()
    {
        $this->dispatcher = new EventDispatcher();
        $this->listener = new TestEventListener();
    }

    public function testInitialState()
    {
        $this->assertEquals(array(), $this->dispatcher->getListeners());
        $this->assertFalse($this->dispatcher->hasListeners(self::preFoo));
        $this->assertFalse($this->dispatcher->hasListeners(self::postFoo));
    }

    public function testAddListener()
    {
        $this->dispatcher->addListener(array('preFoo', 'postFoo'), $this->listener);
        $this->assertTrue($this->dispatcher->hasListeners(self::preFoo));
        $this->assertTrue($this->dispatcher->hasListeners(self::postFoo));
        $this->assertEquals(1, count($this->dispatcher->getListeners(self::preFoo)));
        $this->assertEquals(1, count($this->dispatcher->getListeners(self::postFoo)));
        $this->assertEquals(2, count($this->dispatcher->getListeners()));
    }

    public function testGetListenersSortsByPriority()
    {
        $listener1 = new TestEventListener();
        $listener2 = new TestEventListener();
        $listener3 = new TestEventListener();

        $this->dispatcher->addListener('preFoo', $listener1, -10);
        $this->dispatcher->addListener('preFoo', $listener2);
        $this->dispatcher->addListener('preFoo', $listener3, 10);

        $expected = array($listener3, $listener2, $listener1);

        $this->assertSame($expected, $this->dispatcher->getListeners('preFoo'));
    }

    public function testGetAllListenersSortsByPriority()
    {
        $listener1 = new TestEventListener();
        $listener2 = new TestEventListener();
        $listener3 = new TestEventListener();
        $listener4 = new TestEventListener();
        $listener5 = new TestEventListener();
        $listener6 = new TestEventListener();

        $this->dispatcher->addListener('preFoo', $listener1, -10);
        $this->dispatcher->addListener('preFoo', $listener2);
        $this->dispatcher->addListener('preFoo', $listener3, 10);
        $this->dispatcher->addListener('postFoo', $listener4, -10);
        $this->dispatcher->addListener('postFoo', $listener5);
        $this->dispatcher->addListener('postFoo', $listener6, 10);

        $expected = array(
            'preFoo'  => array($listener3, $listener2, $listener1),
            'postFoo' => array($listener6, $listener5, $listener4),
        );

        $this->assertSame($expected, $this->dispatcher->getListeners());
    }

    public function testDispatch()
    {
        $this->dispatcher->addListener(array('preFoo', 'postFoo'), $this->listener);
        $this->dispatcher->dispatch(self::preFoo);
        $this->assertTrue($this->listener->preFooInvoked);
        $this->assertFalse($this->listener->postFooInvoked);
    }

    public function testDispatchForClosure()
    {
        $invoked = 0;
        $listener = function () use (&$invoked) {
            $invoked++;
        };
        $this->dispatcher->addListener(array('preFoo', 'postFoo'), $listener);
        $this->dispatcher->dispatch(self::preFoo);
        $this->assertEquals(1, $invoked);
    }

    public function testStopEventPropagation()
    {
        $otherListener = new TestEventListener;

        // postFoo() stops the propagation, so only one listener should
        // be executed
        // Manually set priority to enforce $this->listener to be called first
        $this->dispatcher->addListener('postFoo', $this->listener, 10);
        $this->dispatcher->addListener('postFoo', $otherListener);
        $this->dispatcher->dispatch(self::postFoo);
        $this->assertTrue($this->listener->postFooInvoked);
        $this->assertFalse($otherListener->postFooInvoked);
    }

    public function testDispatchByPriority()
    {
        $invoked = array();
        $listener1 = function () use (&$invoked) {
            $invoked[] = '1';
        };
        $listener2 = function () use (&$invoked) {
            $invoked[] = '2';
        };
        $listener3 = function () use (&$invoked) {
            $invoked[] = '3';
        };
        $this->dispatcher->addListener('preFoo', $listener1, -10);
        $this->dispatcher->addListener('preFoo', $listener2);
        $this->dispatcher->addListener('preFoo', $listener3, 10);
        $this->dispatcher->dispatch(self::preFoo);
        $this->assertEquals(array('3', '2', '1'), $invoked);
    }

    public function testRemoveListener()
    {
        $this->dispatcher->addListener(array('preBar'), $this->listener);
        $this->assertTrue($this->dispatcher->hasListeners(self::preBar));
        $this->dispatcher->removeListener(array('preBar'), $this->listener);
        $this->assertFalse($this->dispatcher->hasListeners(self::preBar));
    }

    public function testAddSubscriber()
    {
        $eventSubscriber = new TestEventSubscriber();
        $this->dispatcher->addSubscriber($eventSubscriber);
        $this->assertTrue($this->dispatcher->hasListeners(self::preFoo));
        $this->assertTrue($this->dispatcher->hasListeners(self::postFoo));
    }

    public function testRemoveSubscriber()
    {
        $eventSubscriber = new TestEventSubscriber();
        $this->dispatcher->addSubscriber($eventSubscriber);
        $this->assertTrue($this->dispatcher->hasListeners(self::preFoo));
        $this->assertTrue($this->dispatcher->hasListeners(self::postFoo));
        $this->dispatcher->removeSubscriber($eventSubscriber);
        $this->assertFalse($this->dispatcher->hasListeners(self::preFoo));
        $this->assertFalse($this->dispatcher->hasListeners(self::postFoo));
    }
}

class TestEventListener
{
    public $preFooInvoked = false;
    public $postFooInvoked = false;

    /* Listener methods */

    public function preFoo(Event $e)
    {
        $this->preFooInvoked = true;
    }

    public function postFoo(Event $e)
    {
        $this->postFooInvoked = true;

        $e->stopPropagation();
    }
}

class TestEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array('preFoo', 'postFoo');
    }
}
