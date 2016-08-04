<?php
/**
 * Created by PhpStorm.
 * User: thibaulthenry
 * Date: 04/08/2016
 * Time: 10:37
 */

namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class Locale implements EventSubscriberInterface
{
    private $container;
    private $defaultLocale;

    public function __construct(ContainerInterface $container, $defaultLocale)
    {
        $this->container = $container;
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        if (!$this->container->has('session')) {
            return;
        }

        $session = $this->container->get('session');
        $event->getRequest()->setLocale($session->get('_locale', $this->defaultLocale));
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 255]],
        ];
    }

}
