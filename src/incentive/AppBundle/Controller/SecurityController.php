<?php

namespace incentive\AppBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SecurityController extends BaseController
{


    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return Response
     */
    function renderLogin(array $data)
    {
        return $this->render('@incentiveApp/Security/login.html.twig', $data);
    }
}
