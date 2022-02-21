<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Exception\EmptyBodyException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class EmptybodySubscriber implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event)
    {
        $method= $event->getRequest()->getMethod();

        if(!in_array($method,[Request::METHOD_POST, Request::METHOD_PUT])){
            return;
        }

        $data = $event->getRequest()->get('data');
        if(null === $data){
            throw new EmptyBodyException();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest',EventPriorities::POST_DESERIALIZE]
        ];
    }
}
