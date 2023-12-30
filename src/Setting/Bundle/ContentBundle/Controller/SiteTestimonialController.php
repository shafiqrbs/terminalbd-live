<?php

namespace Setting\Bundle\ContentBundle\Controller;

use Setting\Bundle\ContentBundle\Entity\Testimonial;
use Setting\Bundle\ContentBundle\Form\SiteTestimonialType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Setting\Bundle\ContentBundle\Entity\SiteContent;


/**
 * SiteContent controller.
 *
 */
class SiteTestimonialController extends Controller
{

    /**
     * Lists all SiteContent entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('SettingContentBundle:Testimonial')->findAll();
        return $this->render('SettingContentBundle:SiteTestimonial:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new SiteContent entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Testimonial();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity ->upload();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('sitetestimonial'));
        }
        return $this->render('SettingContentBundle:SiteTestimonial:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a SiteContent entity.
     *
     * @param SiteContent $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Testimonial $entity)
    {
	    $form = $this->createForm(new SiteTestimonialType(), $entity, array(
            'action' => $this->generateUrl('sitetestimonial_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
	    return $form;
    }

    /**
     * Displays a form to create a new SiteContent entity.
     *
     */
    public function newAction()
    {
        $entity = new Testimonial();
        $form   = $this->createCreateForm($entity);
        return $this->render('SettingContentBundle:SiteTestimonial:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a SiteContent entity.
     *
     */
    public function showAction($id)
    {

    }

    /**
     * Displays a form to edit an existing SiteContent entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingContentBundle:Testimonial')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SiteContent entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('SettingContentBundle:SiteTestimonial:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a SiteContent entity.
    *
    * @param SiteContent $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Testimonial $entity)
    {
        $form = $this->createForm(new SiteTestimonialType(), $entity, array(
            'action' => $this->generateUrl('sitetestimonial_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing SiteContent entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:Testimonial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SiteContent entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $em->flush();
            return $this->redirect($this->generateUrl('sitetestimonial'));
        }

        return $this->render('SettingContentBundle:SiteTestimonial:new.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a SiteContent entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
	    $em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('SettingContentBundle:Testimonial')->find($id);
	    if (!$entity) {
		    throw $this->createNotFoundException('Unable to find SiteContent entity.');
	    }
	    $em->remove($entity);
	    $em->flush();
        return $this->redirect($this->generateUrl('sitetestimonial'));
    }


}
