<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

class RequestSubscriber implements EventSubscriberInterface
{
    private $security;
    private $routerInterface;

    public function __construct()
    {
    }

    // Commenting out this constructor I was using for commented out event below... see comments for why. Leaving for personal notes.
    /*public function __construct(Security $security, RouterInterface $routerInterface)
    {
        $this->security = $security;
        $this->routerInterface = $routerInterface;
    }*/

    /*
     * Commenting out return value; User Token in Symfony\Component\Security\Core\Security::getUser() does not yet seem to be initialized
     * in Subscribed Event "kernel.request".. Creating an Event Listener instead.. See App\EventListener\RequestListener::onKernelRequest
     */
    public static function getSubscribedEvents()
    {
        return [
            /*KernelEvents::REQUEST => [
                ['processRequest', 10]
            ]*/
        ];
    }

    /*public function processRequest(RequestEvent $event)
    {
        // https://symfony.com/doc/current/event_dispatcher.html#request-events-checking-types
        if(!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }

        $user = $this->security->getUser();
        // This seems to always be null, likely because it is not yet initialized as Subscribed Event kernel.request happens
        // earlier in the request lifecycle
        //dump($user);

        if(is_null($user)) {
            return;
        }

        $route = $event->getRequest()->attributes->get('_route');

        if($this->isAuthUserOnAnonPage($route)) {
            $response = new RedirectResponse($this->routerInterface->generate('home'));
            $event->setResponse($response);
        }
    }

    private function isAuthUserOnAnonPage($currentRoute)
    {
        return in_array($currentRoute, array(
            'app_login'
        ));
    }*/
}
