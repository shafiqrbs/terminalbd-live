<?php

namespace Terminalbd\PosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('PosBundle:Default:index.html.twig', array('name' => $name));
    }
}
