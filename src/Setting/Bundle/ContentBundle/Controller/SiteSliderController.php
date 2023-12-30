<?php

namespace Setting\Bundle\ContentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ContentBundle\Entity\SiteSlider;
use Setting\Bundle\ContentBundle\Form\SiteSliderType;

/**
 * SiteSlider controller.
 *
 */
class SiteSliderController extends Controller
{

    /**
     * Lists all SiteSlider entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingContentBundle:SiteSlider')->findAll();

        return $this->render('SettingContentBundle:SiteSlider:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new SiteSlider entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new SiteSlider();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);

            $uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');

            // Here, "getMyFile" returns the "UploadedFile" instance that the form bound in your $myFile property
            $uploadableManager->markEntityToUpload($entity, $entity->getFile());

            $em->flush();

            return $this->redirect($this->generateUrl('siteslider'));
        }

        return $this->render('SettingContentBundle:SiteSlider:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a SiteSlider entity.
     *
     * @param SiteSlider $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(SiteSlider $entity)
    {

        $form = $this->createForm(new SiteSliderType(), $entity, array(
            'action' => $this->generateUrl('siteslider_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;
    }

    /**
     * Displays a form to create a new SiteSlider entity.
     *
     */
    public function newAction()
    {
        $entity = new SiteSlider();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingContentBundle:SiteSlider:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a SiteSlider entity.
     * @param SiteSlider $slider
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(SiteSlider $slider)
    {

        $deleteForm = $this->createDeleteForm($slider->getId());

        return $this->render('SettingContentBundle:SiteSlider:show.html.twig', array(
            'entity'      => $slider,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing SiteSlider entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:SiteSlider')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SiteSlider entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingContentBundle:SiteSlider:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a SiteSlider entity.
    *
    * @param SiteSlider $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(SiteSlider $entity)
    {

        $form = $this->createForm(new SiteSliderType(), $entity, array(
            'action' => $this->generateUrl('siteslider_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));


        return $form;
    }
    /**
     * Edits an existing SiteSlider entity.
     *
     */
    public function updateAction(Request $request, SiteSlider $entity )
    {
        $em = $this->getDoctrine()->getManager();

        $deleteForm = $this->createDeleteForm($entity->getId());
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
	        $entity->upload();
            //$uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');
            // Here, "getMyFile" returns the "UploadedFile" instance that the form bound in your $myFile property
            //$uploadableManager->markEntityToUpload($entity, $entity->getFile());
            $em->flush();
            return $this->redirect($this->generateUrl('siteslider'));
        }

        return $this->render('SettingContentBundle:SiteSlider:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a SiteSlider entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SettingContentBundle:SiteSlider')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find SiteSlider entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('siteslider'));
    }

    /**
     * Creates a form to delete a SiteSlider entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('siteslider_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
