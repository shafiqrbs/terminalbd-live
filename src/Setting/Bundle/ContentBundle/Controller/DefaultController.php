<?php

namespace Setting\Bundle\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SettingContentBundle:Default:index.html.twig', array('name' => 'Text'));
    }
}
