<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchaseCommission;
use Appstore\Bundle\AccountingBundle\Form\AccountPurchaseCommissionType;
use Symfony\Component\HttpFoundation\Response;

/**
 * AccountPurchaseCommission controller.
 *
 */
class AccountPurchaseCommissionController extends Controller
{

    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        return $pagination;
    }

	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
	 */

	public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $entities = $em->getRepository('AccountingBundle:AccountPurchaseCommission')->findWithSearch( $this->getUser()->getGlobalOption(),$data);
        $pagination = $this->paginate($entities);
        return $this->render('AccountingBundle:AccountPurchaseCommission:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }
    /**
     * Creates a new AccountPurchaseCommission entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new AccountPurchaseCommission();
        $form = $this->createCreateForm($entity);
        $global = $this->getUser()->getGlobalOption();
        $form->handleRequest($request);
        $method = empty($entity->getTransactionMethod()) ? '' : $entity->getTransactionMethod()->getSlug();
        if (($form->isValid() && $method == 'cash') ||
            ($form->isValid() && $method == 'bank' && $entity->getAccountBank()) ||
            ($form->isValid() && $method == 'mobile' && $entity->getAccountMobileBank())
        ) {
            $em = $this->getDoctrine()->getManager();
            $global = $this->getUser()->getGlobalOption();
            $entity->setGlobalOption($global);
            if(!empty($this->getUser()->getProfile()->getBranches())){
                $entity->setBranches($this->getUser()->getProfile()->getBranches());
            }
            if($global->getMainApp()->getSlug() == 'miss'){
                $entity->setCompanyName($entity->getMedicineVendor()->getCompanyName());
                $entity->setMedicineVendor($entity->getMedicineVendor());
            }elseif($global->getMainApp()->getSlug() == 'inventory'){
                $entity->setCompanyName($entity->getVendor()->getCompanyName());
                $entity->setVendor($entity->getVendor());
            }else{
                $entity->setCompanyName($entity->getAccountVendor()->getCompanyName());
                $entity->setAccountVendor($entity->getAccountVendor());
            }
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('account_purchasecommission'));
        }
        $this->get('session')->getFlashBag()->add(
            'notice',"May be you are missing to select bank or mobile account"
        );
        return $this->render('AccountingBundle:AccountPurchaseCommission:new.html.twig', array(
            'entity' => $entity,
            'option' => $global,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AccountPurchaseCommission entity.
     *
     * @param AccountPurchaseCommission $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AccountPurchaseCommission $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountPurchaseCommissionType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_purchasecommission_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
      return $form;
    }

	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
	 */

    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountPurchaseCommission();
        $form   = $this->createCreateForm($entity);
        $global = $this->getUser()->getGlobalOption();
        return $this->render('AccountingBundle:AccountPurchaseCommission:new.html.twig', array(
            'entity' => $entity,
            'option' => $global,
            'form'   => $form->createView(),
        ));
    }



    /**
     * Displays a form to edit an existing AccountPurchaseCommission entity.
     *
     */
    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AccountingBundle:AccountPurchaseCommission')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountPurchaseCommission entity.');
        }
        $entity->setSales($data['value']);
        $profit = (($entity->getSales() * $entity->getProfitPercentage())/100);
        $entity->setProfit($profit);
        $purchase = ($entity->getSales() - $profit);
        $entity->setPurchase($purchase);
        $em->flush();
        exit;
    }

    public function approveAction(AccountPurchaseCommission $entity)
    {
        if (!empty($entity) and $entity->getProcess() != 'approved') {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('approved');
            $entity->setApprovedBy($this->getUser());
            $accountConfig = $this->getUser()->getGlobalOption()->getAccountingConfig()->isAccountClose();
            if($accountConfig == 1){
                $datetime = new \DateTime("yesterday 23:30:30");
                $entity->setCreated($datetime);
                $entity->setUpdated($datetime);
            }
            $em->flush();
            $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->insertPurchaseCommission($entity);
           // $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->purchaseCommissionTransaction($this->getUser()->getGlobalOption(),$entity);
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }

	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
	 */

    public function deleteAction(AccountPurchaseCommission $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountPurchaseCommission entity.');
        }
        $em->remove($entity);
        $em->flush();
        return new Response('success');
        exit;
    }


    /**
     * Deletes a AccountPurchaseCommission entity.
     *
     */
    public function approveDeleteAction(AccountPurchaseCommission $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountPurchaseCommission entity.');
        }
        $em->remove($entity);
        $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->approvedDeleteRecord($entity,'BalanceTransfer');
        $em->flush();
        return new Response('success');
        exit;
    }


	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNT_REVERSE,ROLE_DOMAIN")
	 */

	public function journalReverseAction(AccountPurchaseCommission $entity){

		$em = $this->getDoctrine()->getManager();
		$this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->accountReverse($entity);
		$entity->setProcess(null);
		$entity->setApprovedBy(null);
		$entity->setAmount(0);
		$em->flush();
		return $this->redirect($this->generateUrl('account_purchasecommission'));

	}

}
