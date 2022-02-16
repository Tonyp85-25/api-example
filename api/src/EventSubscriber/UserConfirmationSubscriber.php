<?php

namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Repository\UserRepository;
use App\Security\UserConfirmationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class UserConfirmationSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private UserConfirmationService $userConfirmationService)
    {
    }
    
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['confirmUser', EventPriorities::POST_VALIDATE]
        ];
    }

    public function confirmUser(ViewEvent $event)
    {
        $request = $event->getRequest();
        // TODO check route name if issues
        if ('api_user_confirmations_post_collection' !== $request->get('_route')) {
            return;
        }
        $confirmationToken = $event->getControllerResult();
        $this->userConfirmationService->confirmUser($confirmationToken->confirmationToken);

        $event->setResponse(new JsonResponse(null, Response::HTTP_OK));
    }
}
