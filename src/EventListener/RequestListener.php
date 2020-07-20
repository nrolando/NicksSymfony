<?php


namespace App\EventListener;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

class RequestListener
{
    private $security;
    private $routerInterface;

    public function __construct(Security $security, RouterInterface $routerInterface)
    {
        $this->security = $security;
        $this->routerInterface = $routerInterface;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        /*
         * A single page can make several requests (one master request, and then multiple sub-requests - typically when embedding
         * controllers in templates). For the core Symfony events, you might need to check to see if the event is for a “master”
         * request or a “sub request”: https://symfony.com/doc/current/event_dispatcher.html#request-events-checking-types
         */
        if(!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }

        $user = $this->security->getUser();

        /* Currently no need to do redirect anon users. access_control should redirect users to the login page
         * if requested page is blocked to anon users.
         */
        if(is_null($user)) {
            return;
        }

        // Can get user roles for further auth processing if needed
        //$userRoles = $user->getRoles();

        //dump($event->getRequest()->attributes);
        $route = $event->getRequest()->attributes->get('_route');
        //dump($route);

        // If logged in user is on anon page only, redirect to home page
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
    }
}