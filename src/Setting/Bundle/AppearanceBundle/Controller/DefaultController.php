<?php

namespace Setting\Bundle\AppearanceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SettingAppearanceBundle:Default:index.html.twig', array('name' => $name));
    }
}
