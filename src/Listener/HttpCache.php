<?php

namespace Dafiti\Silex\Listener;

use Pimple;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class HttpCache implements EventSubscriberInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * Construct listener with config.
     *
     * @param \Pimple $config Configuration
     */
    public function __construct(Pimple $config)
    {
        if (!isset($config['http_cache'])) {
            throw new \InvalidArgumentException('Config http_cache not found');
        }

        $this->config = $config['http_cache'];
    }

    /**
     * Response Event.
     *
     * @param FilterResponseEvent $event
     *
     * @return FilterResponseEvent
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!isset($this->config['enabled']) || !$this->config['enabled']) {
            return $event;
        }

        $maxAge = isset($this->config['max_age']) ? $this->config['max_age'] : 0;

        $event->getResponse()
            ->setMaxAge($maxAge)
            ->setPublic();

        if (isset($this->config['etag']) && $this->config['etag'] === true) {
            $etag = md5($event->getResponse()->getContent());

            $event->getResponse()
                ->setEtag($etag);
        }

        return $event;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', 100],
        ];
    }
}
