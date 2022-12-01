<?php

namespace App\Controller;

use Psr\Container\ContainerExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class BaseController extends AbstractController
{
    /**
     * @return Request|null
     */
    protected function getCurrentRequest(): ?Request
    {
        try {
            return $this->container->get('request_stack')->getCurrentRequest();
        } catch (ContainerExceptionInterface) {
            //TODO: Process exception
            return null;
        }

    }

    protected function getRefererUrl(): ?string
    {
        return $this->getCurrentRequest()->headers->get('referer');
    }

}