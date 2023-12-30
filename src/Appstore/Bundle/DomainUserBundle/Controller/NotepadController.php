<?php

namespace Appstore\Bundle\DomainUserBundle\Controller;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\DomainUserBundle\Entity\Notepad;
use Appstore\Bundle\DomainUserBundle\Form\NotepadType;

/**
 * Notepad controller.
 *
 */
class NotepadController extends Controller
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
     * @Secure(roles="ROLE_CRM,ROLE_DOMAIN")
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('DomainUserBundle:Notepad')->findWithSearch($globalOption,$data);
        $pagination = $this->paginate($entities);
		$notepadID = $this->getDoctrine()->getRepository('DomainUserBundle:Notepad')->generateNotepad($globalOption);
	    $entity = $em->getRepository('DomainUserBundle:Notepad')->findOneBy(array('globalOption'=>$globalOption,'id' => $notepadID));

	    return $this->render('DomainUserBundle:Notepad:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
            'notepad' => $entity,
        ));
    }

    /**
     * Creates a new Notepad entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Notepad();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $globalOption = $this->getUser()->getGlobalOption();
            $entity->setGlobalOption($globalOption);
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('domain_notepad'));
        }

        return $this->render('DomainUserBundle:Notepad:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Notepad entity.
     *
     * @param Notepad $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Notepad $entity)
    {
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new NotepadType($location), $entity, array(
            'action' => $this->generateUrl('domain_notepad_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new Notepad entity.
     *
     */

    /**
     * @Secure(roles="ROLE_CRM,ROLE_DOMAIN")
     */

    public function newAction()
    {
        $entity = new Notepad();
        $form   = $this->createCreateForm($entity);

        return $this->render('DomainUserBundle:Notepad:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Notepad entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DomainUserBundle:Notepad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Notepad entity.');
        }

        return $this->render('DomainUserBundle:Notepad:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing Notepad entity.
     *
     */

    /**
     * @Secure(roles="ROLE_CRM,ROLE_DOMAIN")
     */

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DomainUserBundle:Notepad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Notepad entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('DomainUserBundle:Notepad:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

     public function insertAction($id)
    {
        $em = $this->getDoctrine()->getManager();
		$notes = $_REQUEST['notes'];
        $entity = $em->getRepository('DomainUserBundle:Notepad')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Notepad entity.');
        }

        $entity->setContent($notes);
        $em->flush();
        exit;


    }

    /**
    * Creates a form to edit a Notepad entity.
    *
    * @param Notepad $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Notepad $entity)
    {
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new NotepadType($location), $entity, array(
            'action' => $this->generateUrl('domain_notepad_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing Notepad entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DomainUserBundle:Notepad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Notepad entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            return $this->redirect($this->generateUrl('domain_notepad'));
        }

        return $this->render('DomainUserBundle:Notepad:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a Notepad entity.
     *
     */

    /**
     * @Secure(roles="ROLE_CRM,ROLE_DOMAIN")
     */

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('DomainUserBundle:Notepad')->findOneBy(array('globalOption'=>$globalOption,'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Notepad entity.');
        }
        try {

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'error',"Data has been deleted successfully"
            );

        } catch (ForeignKeyConstraintViolationException $e) {
            $this->get('session')->getFlashBag()->add(
                'notice',"Data has been relation another Table"
            );
        }
        exit;

        return $this->redirect($this->generateUrl('domain_notepad'));
    }


}
