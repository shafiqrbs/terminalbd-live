<?php

namespace Appstore\Bundle\BusinessBundle\Controller;

use Appstore\Bundle\BusinessBundle\Form\WearHouseType;
use Appstore\Bundle\BusinessBundle\Entity\WearHouse;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * WearHouse controller.
 *
 */
class BusinessWearHouseController extends Controller
{

    /**
     * Lists all WearHouse entities.
     *
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     *
     */
    public function indexAction()
    {

        $entity = new WearHouse();
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $em->getRepository('BusinessBundle:WearHouse')->findBy(array('businessConfig' => $option),array( 'name' =>'asc' ));
        $form   = $this->createCreateForm($entity);
        return $this->render('BusinessBundle:WearHouse:index.html.twig', array(
            'entity' => $entity,
            'entities' => $entities,
            'form'   => $form->createView(),
        ));


    }


    /**
     * Creates a new WearHouse entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $em->getRepository('BusinessBundle:WearHouse')->findBy(array('businessConfig' => $option),array( 'name' =>'asc' ));

        $entity = new WearHouse();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $entity->setBusinessConfig($option);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('business_wearhouse', array('id' => $entity->getId())));
        }

        return $this->render('BusinessBundle:WearHouse:index.html.twig', array(
            'entity' => $entity,
            'entities'      => $entities,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a WearHouse entity.
     *
     * @param WearHouse $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(WearHouse $entity)
    {

        $form = $this->createForm(new WearHouseType(), $entity, array(
            'action' => $this->generateUrl('business_wearhouse_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }



    /**
     * Displays a form to edit an existing WearHouse entity.
     *
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $em->getRepository('BusinessBundle:WearHouse')->findBy(array('businessConfig' => $option),array( 'name' =>'asc' ));

        $entity = $em->getRepository('BusinessBundle:WearHouse')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WearHouse entity.');
        }
        $editForm = $this->createEditForm($entity);

        return $this->render('BusinessBundle:WearHouse:index.html.twig', array(
            'entity'      => $entity,
            'entities'      => $entities,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a WearHouse entity.
     *
     * @param WearHouse $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(WearHouse $entity )
    {

        $form = $this->createForm(new WearHouseType(), $entity, array(
            'action' => $this->generateUrl('business_wearhouse_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));


        return $form;
    }
    /**
     * Edits an existing WearHouse entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $em->getRepository('BusinessBundle:WearHouse')->findBy(array('businessConfig' => $option),array( 'name' =>'asc' ));
        $entity = $em->getRepository('BusinessBundle:WearHouse')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WearHouse entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('business_wearhouse'));
        }

        return $this->render('BusinessBundle:WearHouse:index.html.twig', array(
            'entity'      => $entity,
            'entities'      => $entities,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a WearHouse entity.
     *
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     *
     */
    public function deleteAction(WearHouse $entity)
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
        return $this->redirect($this->generateUrl('business_wearhouse'));
    }

    public function sortedAction(Request $request)
    {
        $data = $request ->request->get('item');
        $this->getDoctrine()->getRepository('BusinessBundle:WearHouse')->setWearHouseSorting($data);
        exit;
    }


}
