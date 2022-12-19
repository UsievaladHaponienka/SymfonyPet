<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LoginRedirectListener
{
    private array $allowedUrls;

    public function __construct(
        private readonly RouterInterface $router,
        private readonly TokenStorageInterface $tokenStorage
    )
    {
        $this->init();
    }

    private function init(): void
    {
        $this->allowedUrls = [
            $this->router->generate('app_login'),
            $this->router->generate('app_register'),
        ];
    }

    /**
     * If user is not authorized, redirects to login page
     * List of routes which should be available for non-authorized user should be placed $allowedUrl property
     * @see init
     *
     * @param RequestEvent $event
     * @return void
     */
    public function checkLogin(RequestEvent $event): void
    {
        $userToken = $this->tokenStorage->getToken();

        if (!$userToken && !in_array($event->getRequest()->getRequestUri(), $this->allowedUrls) ) {
            $redirectUrl = $this->router->generate('app_login');
            $response =  new RedirectResponse($redirectUrl);

            $event->setResponse($response);
        }
    }
}
