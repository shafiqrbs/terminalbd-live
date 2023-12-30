<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SettingToolBundle:Default:index.html.twig', array('name' => $name));
    }
}
