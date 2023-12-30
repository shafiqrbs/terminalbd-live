<?php

namespace Bindu\BinduBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BinduBundle:Default:index.html.twig', array('name' => $name));
    }
}
