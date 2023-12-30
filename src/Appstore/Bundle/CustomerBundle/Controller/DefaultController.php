<?php

namespace Appstore\Bundle\CustomerBundle\Controller;

use Setting\Bundle\ToolBundle\Entity\AppModule;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CustomerBundle:Default:index.html.twig', array('name' => $name));
    }

}
