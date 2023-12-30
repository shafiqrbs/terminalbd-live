<?php

namespace Appstore\Bundle\BusinessBundle\Controller;

use Appstore\Bundle\BusinessBundle\Entity\Courier;
use Appstore\Bundle\BusinessBundle\Form\CourierType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Courier controller.
 *u
 */
class CourierController extends Controller
{

    /**
     * Lists all Courier entities.
     *
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     *
     */
    public function indexAction()
    {

        $entity = new Courier();
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $em->getRepository('BusinessBundle:Courier')->findBy(array('businessConfig' => $option),array( 'name' =>'asc' ));
        $form   = $this->createCreateForm($entity);
        return $this->render('BusinessBundle:Courier:index.html.twig', array(
            'entity' => $entity,
            'entities' => $entities,
            'form'   => $form->createView(),
        ));


    }


    /**
     * Creates a new Courier entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $em->getRepository('BusinessBundle:Courier')->findBy(array('businessConfig' => $option),array( 'name' =>'asc' ));

        $entity = new Courier();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setBusinessConfig($option);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('business_courier', array('id' => $entity->getId())));
        }
        return $this->render('BusinessBundle:Courier:index.html.twig', array(
            'entity' => $entity,
            'entities'      => $entities,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Courier entity.
     *
     * @param Courier $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Courier $entity)
    {
        $form = $this->createForm(new CourierType(), $entity, array(
            'action' => $this->generateUrl('business_courier_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }



    /**
     * Displays a form to edit an existing Courier entity.
     *
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $em->getRepository('BusinessBundle:Courier')->findBy(array('businessConfig' => $option),array( 'name' =>'asc' ));

        $entity = $em->getRepository('BusinessBundle:Courier')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Courier entity.');
        }
        $editForm = $this->createEditForm($entity);

        return $this->render('BusinessBundle:Courier:index.html.twig', array(
            'entity'      => $entity,
            'entities'      => $entities,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Courier entity.
     *
     * @param Courier $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Courier $entity )
    {
        $form = $this->createForm(new CourierType(), $entity, array(
            'action' => $this->generateUrl('business_courier_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));


        return $form;
    }
    /**
     * Edits an existing Courier entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $em->getRepository('BusinessBundle:Courier')->findBy(array('businessConfig' => $option),array( 'name' =>'asc' ));
        $entity = $em->getRepository('BusinessBundle:Courier')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Courier entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('business_courier'));
        }

        return $this->render('BusinessBundle:Courier:index.html.twig', array(
            'entity'      => $entity,
            'entities'      => $entities,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a Courier entity.
     *
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     *
     */
    public function deleteAction(Courier $entity)
    {
        $em = $this->getDoctrine()->getManager();
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
        }catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'notice', 'Please contact system administrator further notification.'
            );
        }
        return $this->redirect($this->generateUrl('business_courier'));
    }

    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()->getRepository('BusinessBundle:Courier')->find($id);

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
        return $this->redirect($this->generateUrl('business_courier'));
    }



}
