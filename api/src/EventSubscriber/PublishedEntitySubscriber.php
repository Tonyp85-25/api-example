<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\PublishedEntityInterface;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;


class PublishedEntitySubscriber implements EventSubscriberInterface
{
   
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['setDatePublished',EventPriorities::PRE_WRITE]
        ];
    }

    public function setDatePublished(ViewEvent $event)
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

     
        $date =new DateTime();

        if((!$entity instanceof PublishedEntityInterface) || Request::METHOD_POST !== $method)
        {
            return;
        }
        $entity->setPublished($date);
    }
}