<?php

namespace Frontend\FrontentBundle\Controller;



use Frontend\FrontentBundle\Service\Cart;
use Frontend\FrontentBundle\Service\MobileDetect;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



class CustomerOrderController extends Controller
{

    /**
     * @Secure(roles="ROLE_CUSTOMER")
     */

    public function orderAction()
    {
        echo "Order";
        exit;
    }

}
