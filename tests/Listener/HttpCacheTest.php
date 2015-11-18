<?php

namespace Dafiti\Silex\Listener;

use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpKernel;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class HttpCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Dafiti\Silex\Listener\HttpCache::__construct
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Config http_cache not found
     */
    public function testShouldThrowExceptionWhenHttpCacheConfigNotExists()
    {
        new HttpCache(new \Pimple());
    }

    /**
     * @covers \Dafiti\Silex\Listener\HttpCache::getSubscribedEvents
     */
    public function testSubscribeEvents()
    {
        $expected = [
            HttpKernel\KernelEvents::RESPONSE => [
                'onKernelResponse', 100
            ]
        ];

        $result = HttpCache::getSubscribedEvents();

        $this->assertEquals($expected, $result);
    }

    /**
     * @covers \Dafiti\Silex\Listener\HttpCache::__construct
     * @covers \Dafiti\Silex\Listener\HttpCache::onKernelResponse
     */
    public function testShouldRetrieveSameEventWhenCacheIsDisabled()
    {
        $data = [
            'http_cache' => [
                'enabled' => false
            ]
        ];

        $kernel = $this->getMockBuilder('\Symfony\Component\HttpKernel\HttpKernelInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $request     = new HttpFoundation\Request();
        $requestType = HttpKernel\HttpKernelInterface::MASTER_REQUEST;
        $response    = new HttpFoundation\Response();

        $event    = new FilterResponseEvent($kernel, $request, $requestType, $response);
        $listener = new Httpcache(new \Pimple($data));
        $result   = $listener->onKernelResponse($event);

        $this->assertInstanceOf('\Symfony\Component\HttpKernel\Event\FilterResponseEvent', $result);
    }

    /**
     * @covers \Dafiti\Silex\Listener\HttpCache::__construct
     * @covers \Dafiti\Silex\Listener\HttpCache::onKernelResponse
     */
    public function testShouldSetMaxAgeIntoResponseWhenCacheEnabled()
    {
        $data = [
            'http_cache' => [
                'enabled' => true,
                'max_age' => 300
            ]
        ];

        $kernel = $this->getMockBuilder('\Symfony\Component\HttpKernel\HttpKernelInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $request     = new HttpFoundation\Request();
        $requestType = HttpKernel\HttpKernelInterface::MASTER_REQUEST;
        $response    = new HttpFoundation\Response();

        $event    = new FilterResponseEvent($kernel, $request, $requestType, $response);
        $listener = new Httpcache(new \Pimple($data));
        $result   = $listener->onKernelResponse($event);

        $this->assertInstanceOf('\Symfony\Component\HttpKernel\Event\FilterResponseEvent', $result);
        $this->assertEquals($data['http_cache']['max_age'], $result->getResponse()->getMaxAge());
    }

    /**
     * @covers \Dafiti\Silex\Listener\HttpCache::__construct
     * @covers \Dafiti\Silex\Listener\HttpCache::onKernelResponse
     */
    public function testShouldSetEtagIntoResponseWhenCacheEnabled()
    {
        $data = [
            'http_cache' => [
                'enabled'      => true,
                'max_age'      => 300,
                'etag' => true
            ]
        ];

        $content = json_encode(['store' => 'Dafiti']);

        $kernel = $this->getMockBuilder('\Symfony\Component\HttpKernel\HttpKernelInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $request     = new HttpFoundation\Request();
        $requestType = HttpKernel\HttpKernelInterface::MASTER_REQUEST;
        $response    = new HttpFoundation\Response($content, 200);

        $event    = new FilterResponseEvent($kernel, $request, $requestType, $response);
        $listener = new Httpcache(new \Pimple($data));
        $result   = $listener->onKernelResponse($event);

        $encripted = '"'. md5($response->getContent()) . '"';

        $this->assertInstanceOf('\Symfony\Component\HttpKernel\Event\FilterResponseEvent', $result);
        $this->assertEquals($data['http_cache']['max_age'], $result->getResponse()->getMaxAge());
        $this->assertEquals($encripted, $result->getResponse()->getEtag());
    }
}
