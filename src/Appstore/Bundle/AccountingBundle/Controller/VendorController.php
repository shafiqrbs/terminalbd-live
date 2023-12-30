<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Appstore\Bundle\AccountingBundle\Form\VendorType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Appstore\Bundle\AccountingBundle\Entity\AccountVendor;
use Symfony\Component\HttpFoundation\Response;

/**
 * AccountVendor controller.
 *
 */
class VendorController extends Controller
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
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */


    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('AccountingBundle:AccountVendor')->findWithSearch($globalOption,$data);
        $pagination = $this->paginate($entities);
        return $this->render('AccountingBundle:AccountVendor:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
            // 'overview' => $overview,
        ));
    }

    /**
     * Creates a new AccountVendor entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */

    public function createAction(Request $request)
    {
        $entity = new AccountVendor();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($this->getUser()->getGlobalOption());
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('account_vendor'));
        }

        return $this->render('AccountingBundle:AccountVendor:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AccountVendor entity.
     *
     * @param AccountVendor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AccountVendor $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new VendorType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_vendor_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new AccountVendor entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */

    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountVendor();
        $form   = $this->createCreateForm($entity);
        return $this->render('AccountingBundle:AccountVendor:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AccountVendor entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */

    public function showAction($id)
    {
    }

    /**
     * Displays a form to edit an existing AccountVendor entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountVendor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountVendor entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('AccountingBundle:AccountVendor:new.html.twig',[
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ]);
    }


    /**
     * Creates a form to edit a AccountVendor entity.
     *
     * @param AccountVendor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(AccountVendor $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new VendorType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_vendor_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    /**
     * Edits an existing AccountVendor entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */


    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountVendor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountVendor entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->getDoctrine()->getRepository('AccountingBundle:AccountVendor')->updateVendorCompanyName($entity);
            return $this->redirect($this->generateUrl('account_vendor'));
        }

        return $this->render('AccountingBundle:AccountVendor:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Expenditure entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */

    public function deleteAction(AccountVendor $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountPurchase entity.');
        }
        $em->remove($entity);
        $em->flush();
        return new Response('success');
        exit;
    }

    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AccountingBundle:AccountVendor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }

        $status = $entity->isStatus();
        if($status == 1){
            $entity->setStatus(false);
        } else{
            $entity->setStatus(true);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        exit;

    }

    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $global = $this->getUser()->getGlobalOption();
            $item = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->searchAutoComplete($item,$global);
        }
        return new JsonResponse($item);
    }

    public function searchVendorNameAction($name)
    {
        return new JsonResponse(array(
            'id' => $name,
            'text' => $name
        ));
    }

    public function ledgerAction()
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $type = $_REQUEST['type'];
        $vendor = $_REQUEST['vendor'];
        $balance = 0;
        if(!empty($vendor)){
            $result = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->vendorSingleOutstanding($globalOption,$type,$vendor);
            $balance = empty($result) ? 0 : $result;
        }
        $taka = number_format($balance).' Taka';
        return new Response($taka);
        exit;

    }

}
