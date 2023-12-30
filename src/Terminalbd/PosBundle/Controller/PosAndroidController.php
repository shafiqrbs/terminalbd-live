<?php

namespace Terminalbd\PosBundle\Controller;

use Appstore\Bundle\InventoryBundle\Entity\InventoryAndroidProcess;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;


/**
 * InvoiceController controller.
 *
 */
class PosAndroidController extends Controller
{
    public function paginate($entities)
    {

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        return $pagination;
    }

    /**
     * @Secure(roles="ROLE_POS_MANAGER")
     */

    public function indexAction()
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $conf = $this->getUser()->getGlobalOption()->getInventoryConfig()->getId();
        $entities = $this->getDoctrine()->getRepository('InventoryBundle:InventoryAndroidProcess')->getAndroidSalesList($conf,"sales");
        $pagination = $this->paginate($entities);
        $sales = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->findAndroidDeviceSales($pagination);
        return $this->render('PosBundle:Android:index.html.twig', array(
            'globalOption' => $globalOption,
            'entities' => $pagination,
            'sales' => $sales,
        ));
    }

    public function insertGroupApiSalesImportAction($android)
    {
        /* @var $go GlobalOption */
        $go = $this->getUser()->getGlobalOption();
        $main = $go->getMainApp()->getSlug();
        $msg = "invalid";
        switch ($main){
            case 'inventory':
                $msg = $this->inventorySalesProcess($android);
                break;
            case 'miss':
                $this->inventorySalesProcess($android);
                break;
            case 'restaurant':
                $this->inventorySalesProcess($android);
                break;
        }
        return new Response($msg);
    }

    /**
     * @Secure(roles="ROLE_POS_MANAGER")
     */

    private function inventorySalesProcess($android){

        $msg = "invalid";
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $android = $this->getDoctrine()->getRepository("InventoryBundle:InventoryAndroidProcess")->findOneBy(array('inventoryConfig'=>$config,'id'=>$android));
        $removeSales = $em->createQuery("DELETE InventoryBundle:Sales e WHERE e.androidProcess= {$android->getId()}");
        if(!empty($removeSales)){
            $removeSales->execute();
        }
        $this->getDoctrine()->getRepository('InventoryBundle:Sales')->insertApiSales($this->getUser()->getGlobalOption(),$android);

        /* @var $sales Sales */

        $salses = $this->getDoctrine()->getRepository("InventoryBundle:Sales")->findBy(array('androidProcess' => $android));
        foreach ($salses as $sales){
            if($sales->getProcess() == "Device"){
                $sales->setProcess('Done');
                $sales->setUpdated($sales->getCreated());
                $sales->setApprovedBy($this->getUser());
                $em->flush();
                $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertAccountSales($sales);
                $msg = "valid";
            }
        }
        if($msg == "valid"){
            $android->setStatus(true);
            $em->persist($android);
            $em->flush();
            $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateApiSalesPurchasePrice($android->getId());
        }
        if($msg == "valid"){
            return "success";
        }else{
            return "failed";
        }
    }

    public function androidSalesProcessAction($device)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->androidDeviceSalesProcess($device);
        exit;
    }


}
