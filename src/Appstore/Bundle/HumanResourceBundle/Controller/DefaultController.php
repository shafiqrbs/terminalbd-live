<?php

namespace Appstore\Bundle\HumanResourceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('HumanResourceBundle:Default:index.html.twig', array('name' => $name));
    }
}
