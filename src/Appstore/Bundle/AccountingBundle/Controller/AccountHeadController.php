<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountHead;
use Appstore\Bundle\AccountingBundle\Form\AccountHeadSubType;
use Appstore\Bundle\AccountingBundle\Form\AccountHeadType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AccountHead controller.
 *
 */
class AccountHeadController extends Controller
{

    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
           100  /*limit per page*/
        );
        return $pagination;
    }

    /**
     * Lists all AccountHead entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
	    $data = $_REQUEST;
	    $global = $this->getUser()->getGlobalOption();
        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findBy(array('isParent' => 1,'status' => 1),array('name'=>'ASC'));
        $heads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getAllChildrenAccount($global->getId());
        return $this->render('AccountingBundle:AccountHead:index.html.twig', array(
            'accountHead' => $accountHead,
            'heads' => $heads,
            'searchForm'  => $data,
        ));
    }


    /**
     * Creates a new AccountHead entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN_ACCOUNTING")
     */
    public function createAction(Request $request)
    {
        $entity = new AccountHead();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $global = $this->getUser()->getGlobalOption();
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($global);
            $entity->setCode($entity->getCode());
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('accounthead_new', array('id' => $entity->getId())));
        }

        return $this->render('AccountingBundle:AccountHead:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AccountHead entity.
     *
     * @param AccountHead $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AccountHead $entity)
    {
        $form = $this->createForm(new AccountHeadSubType(), $entity, array(
            'action' => $this->generateUrl('accounthead_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new AccountHead entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN_ACCOUNTING")
     */
    public function newAction()
    {
        $entity = new AccountHead();
        $form   = $this->createCreateForm($entity);
        return $this->render('AccountingBundle:AccountHead:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AccountHead entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountHead')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountHead entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AccountingBundle:AccountHead:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing AccountHead entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN_ACCOUNTING")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountHead')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountHead entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('AccountingBundle:AccountHead:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a AccountHead entity.
    *
    * @param AccountHead $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AccountHead $entity)
    {
        $form = $this->createForm(new AccountHeadType(), $entity, array(
            'action' => $this->generateUrl('accounthead_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing AccountHead entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountHead')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountHead entity.');
        }
        
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setSlug($entity->getName());
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('accounthead'));
        }

        return $this->render('AccountingBundle:AccountHead:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a AccountHead entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN_ACCOUNTING")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AccountingBundle:AccountHead')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AccountHead entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('accounthead'));
    }

    /**
     * Creates a form to delete a AccountHead entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('accounthead_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

	public function parentSelectAction()
	{

		$accountHeads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findBy(array('isParent'=>1),array('name'=>'ASC'));
		$items      = array();
		$items[]    = array('value' => '','text'=> '---Select Parent Head---');
		foreach ($accountHeads as $entity):
			$items[]=array('value' => $entity->getId(),'text'=> $entity->getName());
		endforeach;
		return new JsonResponse($items);

	}

    public function subAccountSelectAction($id)
    {
        $global = $this->getUser()->getGlobalOption();
        $accountHeads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findBy(array('parent' => $id , 'globalOption'=> $global , 'status'=>1),array('name'=>'ASC'));
        $items      = '';
        $items    .="<option value=''>--Choose Sub-account Head--</option>";
        foreach ($accountHeads as $entity):
            $items .="<option value='{$entity->getId()}'>{$entity->getName()}</option>";
        endforeach;
        return new Response($items);

    }

    public function inlineUpdateAction(Request $request)
	{
		$data = $request->request->all();
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('AccountingBundle:AccountHead')->find($data['pk']);
		if (!$entity) {
			throw $this->createNotFoundException('Unable to find AccountHead entity.');
		}
		if($data['name'] == 'code'){
			$entity->setCode($data['value']);
		}
		if($data['name'] == 'name'){
			$entity->setName($data['value']);
		}
		if($data['name'] == 'parent'){
			$parent = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->find($data['value']);
			$entity->setParent($parent);
		}
		$em->flush();
		exit;
	}

}
